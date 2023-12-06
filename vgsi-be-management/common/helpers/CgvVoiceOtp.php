<?php
/**
 * Created by PhpStorm.
 * User: nguyennhuluc1990@gmail.com
 * Date: 23/06/2020
 * Time: 1:57 CH
 */
namespace common\helpers;

use Exception;
use Yii;
use yii\helpers\Json;


class CgvVoiceOtp
{
    private $url_api = '';
    private $access_token = '';
    private $ch;

    public function __construct()
    {
        $push_bullet_config = Yii::$app->params['Cgv_Voice_Otp'];
        $this->url_api = $push_bullet_config['URL_API'];
        $this->access_token = $push_bullet_config['ACCESS_TOKEN'];
        $this->ch = new MyCurl();
    }

    /**
     * @param $otp_code
     * @param $phone
     * @return bool
     */
    public function sendOtpVoice($otp_code, $phone)
    {
        try {
            $this->ch->headers = [
                'Content-Type' => 'application/json',
                'X-Access-Token' => $this->access_token,
            ];
            $body = Json::encode([
                'phone' => $phone,
                'code' => $otp_code
            ]);
            $response = $this->ch->post($this->url_api, $body);
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
        Yii::info($response->body);
        return true;
    }

}