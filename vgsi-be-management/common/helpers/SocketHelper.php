<?php
/**
 * Created by PhpStorm.
 * User: lucnn@luci.vn
 * Date: 12/05/2017
 * Time: 5:07 CH
 */

namespace common\helpers;

use Yii;
use common\helpers\msgpack\Packer;

class SocketHelper
{
    /**
     * Default namespace
     *
     * @var string
     */
    const DEFAULT_NAMESPACE = '/';

    /**
     * @var int
     */
    const REGULAR_EVENT     = 2;

    /**
     * @var int
     */
    const BINARY_EVENT      = 5;

    /**
     * @var string
     */
    protected $uid = 'emitter';

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Rooms
     * @var array
     */
    protected $rooms;

    /**
     * @var array
     */
    protected $validFlags = [
        'json',
        'volatile',
        'broadcast'
    ];

    /**
     * @var array
     */
    protected $flags;

    /**
     * @var Packer
     */
    protected $packer;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var array
     */
    protected $res;

    /**
     * Emitter constructor.
     * @param string $prefix
     */
    public function __construct($prefix = '')
    {
        if(empty($prefix)){
            $prefix = Yii::$app->params['socket_prefix'];
        }
        $this->prefix = $prefix;
        $this->packer = new Packer();
        $this->reset([]);
    }


    /**
     * Set room
     *
     * @param  string $room
     * @return $this
     */
    public function in($room)
    {
        //multiple
        if (is_array($room)) {
            foreach ($room as $r) {
                $this->in($r);
            }
            return $this;
        }
        //single
        if (!in_array($room, $this->rooms)) {
            array_push($this->rooms, $room);
        }
        return $this;
    }

    /**
     * Alias for in
     *
     * @param  string $room
     * @return $this
     */
    public function to($room)
    {
        return $this->in($room);
    }

    /**
     * Set a namespace
     *
     * @param  string $namespace
     * @return $this
     */
    public function of($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Set flags with magic method
     *
     * @param  int $flag
     * @return $this
     */
    public function __get($flag)
    {
        return $this->flag($flag);
    }

    /**
     * Set flags
     *
     * @param  int $flag
     * @return $this
     */
    public function flag($flag) {
        if (!in_array($flag, $this->validFlags)) {
            throw new \InvalidArgumentException('Invalid socket.io flag used: ' . $flag);
        }

        $this->flags[$flag] = true;

        return $this;
    }

    /**
     * Set type
     *
     * @param  int $type
     * @return $this
     */
    public function type($type = self::REGULAR_EVENT)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Emitting
     *
     * @return $this
     */
    public function emit()
    {
        $packet = [
            'type' => $this->type,
            'data' => func_get_args(),
            'nsp'  => $this->namespace,
        ];
        $options = [
            'rooms' => $this->rooms,
            'flags' => $this->flags,
        ];
        $channelName = sprintf('%s#%s#', $this->prefix, $packet['nsp']);
        $message = $this->packer->pack([$this->uid, $packet, $options]);

        // hack buffer extensions for msgpack with binary
        if ($this->type === self::BINARY_EVENT) {
            $message = str_replace(pack('c', 0xda), pack('c', 0xd8), $message);
            $message = str_replace(pack('c', 0xdb), pack('c', 0xd9), $message);
        }
        // publish
        $res = array();
        if (is_array($this->rooms) && count($this->rooms) > 0) {
            foreach ($this->rooms as $room) {
                $chnRoom = $channelName . $room . '#';
            $res[] = Yii::$app->redis_socket->executeCommand('PUBLISH', [
                    'channel' => $chnRoom,
                    'message' => $message
                ]);
            }
        } else {
            $res[] = Yii::$app->redis_socket->executeCommand('PUBLISH', [
                'channel' => $channelName,
                'message' => $message
            ]);
        }
        $this->res = $res;
        // reset state
        return $this->reset();
    }

    /**
     * Reset all values
     * @return $this
     */
    protected function reset()
    {
        $this->rooms     = [];
        $this->flags     = [];
        $this->namespace = self::DEFAULT_NAMESPACE;
        $this->type      = self::REGULAR_EVENT;
        return $this;
    }

    /*
     * @return Res Redis publish
     */
    public function res(){
        $res = $this->res;
        $this->res = [];
        return $res;
    }
}