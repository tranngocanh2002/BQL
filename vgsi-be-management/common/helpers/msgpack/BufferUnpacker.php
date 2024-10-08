<?php

/*
 * This file is part of the rybakit/msgpack.php package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace common\helpers\msgpack;

use common\helpers\msgpack\Exception\InsufficientDataException;
use common\helpers\msgpack\Exception\IntegerOverflowException;
use common\helpers\msgpack\Exception\UnpackingFailedException;
use common\helpers\msgpack\TypeTransformer\Collection;

class BufferUnpacker
{
    const INT_AS_EXCEPTION = 0;
    const INT_AS_STR = 1;
    const INT_AS_GMP = 2;

    /**
     * @var int
     */
    private $intOverflowMode = self::INT_AS_EXCEPTION;

    /**
     * @var string
     */
    private $buffer = '';

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * @var Collection
     */
    private $transformers;

    /**
     * @param int|null $intOverflowMode
     */
    public function __construct($intOverflowMode = null)
    {
        if (null !== $intOverflowMode) {
            $this->intOverflowMode = $intOverflowMode;
        }
    }

    /**
     * @param Collection|null $transformers
     */
    public function setTransformers(Collection $transformers = null)
    {
        $this->transformers = $transformers;
    }

    /**
     * @return Collection|null
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * @param int $intOverflowMode
     *
     * @throws \InvalidArgumentException
     */
    public function setIntOverflowMode($intOverflowMode)
    {
        if (!in_array($intOverflowMode, [
            self::INT_AS_EXCEPTION,
            self::INT_AS_STR,
            self::INT_AS_GMP,
        ], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid integer overflow mode: %s.', $intOverflowMode));
        }

        $this->intOverflowMode = $intOverflowMode;
    }

    /**
     * @return int
     */
    public function getIntOverflowMode()
    {
        return $this->intOverflowMode;
    }

    /**
     * @param string $data
     *
     * @return $this
     */
    public function append($data)
    {
        $this->buffer .= $data;

        return $this;
    }

    /**
     * @param string|null $buffer
     *
     * @return $this
     */
    public function reset($buffer = null)
    {
        $this->buffer = (string) $buffer;
        $this->offset = 0;

        return $this;
    }

    /**
     * @return array
     */
    public function tryUnpack()
    {
        $data = [];
        $offset = $this->offset;

        try {
            do {
                $data[] = $this->unpack();
                $offset = $this->offset;
            } while (isset($this->buffer[$this->offset]));
        } catch (InsufficientDataException $e) {
            $this->offset = $offset;
        }

        if ($this->offset) {
            $this->buffer = (string) substr($this->buffer, $this->offset);
            $this->offset = 0;
        }

        return $data;
    }

    /**
     * @return mixed
     *
     * @throws UnpackingFailedException
     */
    public function unpack()
    {
        $this->ensureLength(1);

        $c = ord($this->buffer[$this->offset]);
        $this->offset += 1;

        // fixint
        if ($c <= 0x7f) {
            return $c;
        }
        // fixstr
        if ($c >= 0xa0 && $c <= 0xbf) {
            return $this->unpackStr($c & 0x1f);
        }
        // fixarray
        if ($c >= 0x90 && $c <= 0x9f) {
            return $this->unpackArray($c & 0xf);
        }
        // fixmap
        if ($c >= 0x80 && $c <= 0x8f) {
            return $this->unpackMap($c & 0xf);
        }
        // negfixint
        if ($c >= 0xe0) {
            return $c - 256;
        }

        switch ($c) {
            case 0xc0: return null;
            case 0xc2: return false;
            case 0xc3: return true;

            // MP_BIN
            case 0xc4: return $this->unpackStr($this->unpackU8());
            case 0xc5: return $this->unpackStr($this->unpackU16());
            case 0xc6: return $this->unpackStr($this->unpackU32());

            case 0xca: return $this->unpackFloat();
            case 0xcb: return $this->unpackDouble();

            // MP_UINT
            case 0xcc: return $this->unpackU8();
            case 0xcd: return $this->unpackU16();
            case 0xce: return $this->unpackU32();
            case 0xcf: return $this->unpackU64();

            // MP_INT
            case 0xd0: return $this->unpackI8();
            case 0xd1: return $this->unpackI16();
            case 0xd2: return $this->unpackI32();
            case 0xd3: return $this->unpackI64();

            // MP_STR
            case 0xd9: return $this->unpackStr($this->unpackU8());
            case 0xda: return $this->unpackStr($this->unpackU16());
            case 0xdb: return $this->unpackStr($this->unpackU32());

            // MP_ARRAY
            case 0xdc: return $this->unpackArray($this->unpackU16());
            case 0xdd: return $this->unpackArray($this->unpackU32());

            // MP_MAP
            case 0xde: return $this->unpackMap($this->unpackU16());
            case 0xdf: return $this->unpackMap($this->unpackU32());

            // MP_EXT
            case 0xd4: return $this->unpackExt(1);
            case 0xd5: return $this->unpackExt(2);
            case 0xd6: return $this->unpackExt(4);
            case 0xd7: return $this->unpackExt(8);
            case 0xd8: return $this->unpackExt(16);
            case 0xc7: return $this->unpackExt($this->unpackU8());
            case 0xc8: return $this->unpackExt($this->unpackU16());
            case 0xc9: return $this->unpackExt($this->unpackU32());
        }

        throw new UnpackingFailedException(sprintf('Unknown code: 0x%x.', $c));
    }

    private function unpackU8()
    {
        $this->ensureLength(1);

        $num = $this->buffer[$this->offset];
        $this->offset += 1;

        $num = unpack('C', $num);

        return $num[1];
    }

    private function unpackU16()
    {
        $this->ensureLength(2);

        $num = $this->buffer[$this->offset].$this->buffer[$this->offset + 1];
        $this->offset += 2;

        $num = unpack('n', $num);

        return $num[1];
    }

    private function unpackU32()
    {
        $this->ensureLength(4);

        $num = substr($this->buffer, $this->offset, 4);
        $this->offset += 4;

        $num = unpack('N', $num);

        return $num[1];
    }

    private function unpackU64()
    {
        $this->ensureLength(8);

        $num = substr($this->buffer, $this->offset, 8);
        $this->offset += 8;

        //$num = unpack('J', $num);

        $set = unpack('N2', $num);
        $value = $set[1] << 32 | $set[2];

        // PHP does not support unsigned integers.
        // If a number is bigger than 2^63, it will be interpreted as a float.
        // @link http://php.net/manual/en/language.types.integer.php#language.types.integer.overflow

        return ($value < 0) ? $this->handleIntOverflow($value) : $value;
    }

    private function unpackI8()
    {
        $this->ensureLength(1);

        $num = $this->buffer[$this->offset];
        $this->offset += 1;

        $num = unpack('c', $num);

        return $num[1];
    }

    private function unpackI16()
    {
        $this->ensureLength(2);

        $num = $this->buffer[$this->offset].$this->buffer[$this->offset + 1];
        $this->offset += 2;

        $num = unpack('s', strrev($num));

        return $num[1];
    }

    private function unpackI32()
    {
        $this->ensureLength(4);

        $num = substr($this->buffer, $this->offset, 4);
        $this->offset += 4;

        $num = unpack('i', strrev($num));

        return $num[1];
    }

    private function unpackI64()
    {
        $this->ensureLength(8);

        $num = substr($this->buffer, $this->offset, 8);
        $this->offset += 8;

        $set = unpack('N2', $num);

        return $set[1] << 32 | $set[2];
    }

    private function unpackFloat()
    {
        $this->ensureLength(4);

        $num = substr($this->buffer, $this->offset, 4);
        $this->offset += 4;

        $num = unpack('f', strrev($num));

        return $num[1];
    }

    private function unpackDouble()
    {
        $this->ensureLength(8);

        $num = substr($this->buffer, $this->offset, 8);
        $this->offset += 8;

        $num = unpack('d', strrev($num));

        return $num[1];
    }

    private function unpackStr($length)
    {
        if (!$length) {
            return '';
        }

        $this->ensureLength($length);

        $str = substr($this->buffer, $this->offset, $length);
        $this->offset += $length;

        return $str;
    }

    private function unpackArray($size)
    {
        $array = [];

        for ($i = $size; $i; $i--) {
            $array[] = $this->unpack();
        }

        return $array;
    }

    private function unpackMap($size)
    {
        $map = [];

        for ($i = $size; $i; $i--) {
            $key = $this->unpack();
            $value = $this->unpack();

            $map[$key] = $value;
        }

        return $map;
    }

    private function unpackExt($length)
    {
        $this->ensureLength($length);

        $type = $this->unpackI8();
        $data = substr($this->buffer, $this->offset, $length);

        if ($this->transformers && $transformer = $this->transformers->find($type)) {
            return $transformer->reverseTransform($this->unpack());
        }

        $this->offset += $length;

        return new Ext($type, $data);
    }

    private function ensureLength($length)
    {
        if (!isset($this->buffer[$this->offset + $length - 1])) {
            throw new InsufficientDataException($length, strlen($this->buffer) - $this->offset);
        }
    }

    private function handleIntOverflow($value)
    {
        if (self::INT_AS_STR === $this->intOverflowMode) {
            return sprintf('%u', $value);
        }
        if (self::INT_AS_GMP === $this->intOverflowMode) {
            return gmp_init(sprintf('%u', $value));
        }

        throw new IntegerOverflowException($value);
    }
}
