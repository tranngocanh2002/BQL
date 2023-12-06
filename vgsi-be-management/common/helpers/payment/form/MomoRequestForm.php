<?php

namespace common\helpers\payment\form;

use common\helpers\Encoder;
use common\helpers\payment\MomoPay;
use yii\base\Model;

class MomoRequestForm extends Model
{
    public $partnerCode;
    public $partnerRefId;
    public $customerNumber;
    public $appData;
    public $hash;
    public $version;
    public $payType;
    public $description;
    public $extra_data;
    public $amount;
    public $partnerTransId;

    public function rules()
    {
        return [
            [['partnerCode', 'partnerRefId', 'customerNumber', 'appData', 'hash', 'version', 'payType', 'amount'], 'required'],
            [['partnerCode', 'partnerRefId', 'customerNumber', 'appData', 'hash', 'description'], 'string'],
            [['version', 'payType', 'extra_data', 'amount', 'partnerTransId'], 'safe'],
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