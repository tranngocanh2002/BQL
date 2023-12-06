<?php

namespace common\helpers;
use common\models\SmsLog;
use Exception;
use Yii;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 14/04/2017
 * Time: 1:57 CH
 */
class eSMSHelper
{
    private $base_url = '';
    private $api_key = '';
    private $api_secret = '';
    private $ch;
    private $production = false;

    const SMS_TYPE_CSKH = 6;
    const SMS_TYPE_NOTIFY = 4;
    const SMS_TYPE_FIX = 8;
    const SMS_TYPE_BRAND_NAME = 2;

    const RESPONSE_SUCCESS = 100;


    public function __construct()
    {
        $esms_config = Yii::$app->params['esms'];
        $this->base_url = $esms_config['base_url'];
        $this->api_key = $esms_config['api_key'];
        $this->api_secret = $esms_config['api_secret'];
        $this->ch = new \common\helpers\MyCurl();
        $this->production = $esms_config['production'];
    }

    /**
     * @param $msisdn
     * @param $message
     * @return bool|SmsLog
     */
    public function sendSMSNotify($msisdn, $message){
        $msisdn = \common\helpers\CUtils::validateMsisdn($msisdn);
        if(empty($msisdn) || empty($message)) return false;

        $sms_log = new SmsLog();
        $sms_log->content = $message;
        $sms_log->from = 'Notify';
        $sms_log->to = $msisdn;
        $sms_log->status = \common\models\SmsLog::STATUS_SENDING;
        if(!$sms_log->save()){
            Yii::error($sms_log->getErrors());
            return false;
        }
        try{
            if($this->production){
                $response = $this->ch->get($this->base_url.'/SendMultipleMessage_V4_get', [
                    'Phone' => $msisdn,
                    'ApiKey' => $this->api_key,
                    'SecretKey' => $this->api_secret,
                    'Content' => $message,
                    'SmsType' => self::SMS_TYPE_NOTIFY
                ]);
            }else{
                $response = new \stdClass();
                $response->body = '{"CodeResult": "100","CountRegenerate":"0","SMSID": "24342680"}';
            }
        }catch (Exception $e){
            Yii::error($e->getMessage());
            $response = '';
            $sms_log->status = SmsLog::STATUS_NOT_CONNECT;
            $sms_log->update();
            return false;
        }
        Yii::info($response->body);
        $result = Json::decode($response->body);
        $sms_log->response = $response->body;
        $sms_log->sms_id = isset($result['SMSID'])?$result['SMSID']:'0';
        if(isset($result['CodeResult']) && $result['CodeResult'] == self::RESPONSE_SUCCESS){
            $sms_log->status = SmsLog::STATUS_SENDED;
        }else{
            $sms_log->status = SmsLog::STATUS_SEND_FALL;
        }

        if(!$sms_log->update()){
            Yii::error($sms_log->getErrors());
        }
        if($sms_log->status == SmsLog::STATUS_SENDED){
            return $sms_log;
        }
        return false;
    }

}