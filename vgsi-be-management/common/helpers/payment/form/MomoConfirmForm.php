<?php

namespace common\helpers\payment\form;

use common\helpers\Encoder;
use yii\base\Model;

class MomoConfirmForm extends Model
{
    public $partnerCode;
    public $partnerRefId;
    public $requestType;
    public $requestId;
    public $momoTransId;
    public $signature;
    public $customerNumber;
    public $serectkey;

    public function rules()
    {
        return [
            [['partnerCode', 'partnerRefId', 'requestType', 'requestId', 'momoTransId', 'signature', 'customerNumber'], 'required'],
            [['partnerCode', 'partnerRefId', 'requestType', 'requestId', 'momoTransId', 'signature', 'customerNumber', 'description', 'serectkey'], 'string'],
        ];
    }

    public function setSignature()
    {
        $rawHash = "partnerCode=" . $this->partnerCode
            . "&partnerRefId=" . $this->partnerRefId
            . "&requestType=" . $this->requestType
            . "&requestId=" . $this->requestId
            . "&momoTransId=" . $this->momoTransId;
        $this->signature = Encoder::hashSha256($rawHash, $this->serectkey);
    }
}