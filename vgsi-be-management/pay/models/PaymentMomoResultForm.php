<?php

namespace pay\models;

use common\helpers\ErrorCode;
use yii\base\Model;


class PaymentMomoResultForm extends Model
{
    public $partnerCode;
    public $accessKey;
    public $orderId;
    public $localMessage;
    public $message;
    public $transId;
    public $orderInfo;
    public $amount;
    public $errorCode;
    public $responseTime;
    public $requestId;
    public $extraData;
    public $payType;
    public $orderType;
    public $signature;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'partnerCode',
                'accessKey',
                'orderId',
                'localMessage',
                'message',
                'transId',
                'orderInfo',
                'amount',
                'errorCode',
                'responseTime',
                'requestId',
                'extraData',
                'payType',
                'orderType',
                'signature',
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function result($params)
    {
        try {
            $this->load($params, '');
            if (!$this->validate()) {
                \Yii::error($this->errors);
                return [
                    'success' => false,
                    'message' => \Yii::t('pay', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $this->getErrors()
                ];
            }
            $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'; //Put your secret key in there
            //Checksum
            $rawHash = "partnerCode=" . $this->partnerCode .
                "&accessKey=" . $this->accessKey .
                "&requestId=" . $this->requestId .
                "&amount=" . $this->amount .
                "&orderId=" . $this->orderId .
                "&orderInfo=" . $this->orderInfo .
                "&orderType=" . $this->orderType .
                "&transId=" . $this->transId .
                "&message=" . $this->message .
                "&localMessage=" . $this->localMessage .
                "&responseTime=" . $this->responseTime .
                "&errorCode=" . $this->errorCode .
                "&payType=" . $this->payType .
                "&extraData=" . $this->extraData;

            $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);

//        echo "<script>console.log('Debug huhu Objects: " . $rawHash . "' );</script>";
//        echo "<script>console.log('Debug huhu Objects: " . $partnerSignature . "' );</script>";

            $success = false;
            if ($this->signature == $partnerSignature) {
                if ($this->errorCode == '0') {
                    $success = true;
                    $result = 'Payment status success';
                } else {
                    $result = 'Payment status: ' . $this->message . '/' . $this->localMessage;
                }
            } else {
//            $result = 'This transaction could be hacked, please check your signature and returned signature';
                $result = 'ERROR!: Fail checksum';
            }

            return [
                'success' => $success,
                'message' => $result,
            ];
        } catch (\Exception $e) {
            \Yii::error($e);
            return [
                'success' => false,
                'message' => \Yii::t('pay', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $e
            ];
        }
    }
}
