<?php

namespace common\helpers\payment\form;

use common\helpers\Encoder;
use common\helpers\payment\MomoPay;
use yii\base\Model;

class MomoRefundForm extends Model
{
    public $partnerCode;
    public $requestId;
    public $hash;
    public $version;
    public $partnerRefId;
    public $momoTransId;
    public $amount;
    public $storeId;
    public $description;
    public $extra;

    public function rules()
    {
        return [
            [['partnerCode', 'requestId', 'hash', 'version', 'partnerRefId', 'momoTransId', 'amount'], 'required'],
            [['partnerCode', 'requestId', 'hash', 'partnerRefId', 'momoTransId'], 'string'],
            [['storeId', 'version', 'description', 'amount', 'extra'], 'safe'],
        ];
    }

    public function setHash()
    {
        $arrData = [
            "partnerCode" => $this->partnerCode,
            "partnerRefId" => $this->partnerRefId,
            "partnerTransId" => $this->partnerTransId,
            "amount" => $this->amount
        ];

        $this->hash = Encoder::encryptRSA($arrData, MomoPay::PUBLIC_KEY);
    }
}