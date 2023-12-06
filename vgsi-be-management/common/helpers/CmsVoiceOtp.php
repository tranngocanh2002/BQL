<?php
/**
 * Created by PhpStorm.
 * User: hoanglv@gmail.com
 * Date: 07/12/2023
 * Time: 1:57 CH
 */
namespace common\helpers;

use Exception;
use Yii;
use yii\helpers\Json;


class CmsVoiceOtp
{
    private $url_api = '';
    private $encrypted = '';
    private $ch;
    private $from;
    private $algorithm;
    private $iv;

    public function __construct()
    {
        $push_bullet_configs = Yii::$app->params['Cgv_Voice_Otp'];
        $push_bullet_config = [
            'URL_API'   => "https://api-connect.io:443/voice-otp/voice-otp",
            'encrypted' => "sNcgTZwk4nSNQCtQzH82Jh8Ys9c0iZlk",
            'from'      => "02471089892",
            'algorithm' => "aes-256-cbc",
            'iv'        => "0000000000000000",
        ];
        $this->url_api      = $push_bullet_config['URL_API'];
        $this->encrypted    = $push_bullet_config['encrypted'];
        $this->from         = $push_bullet_config['from'];
        $this->algorithm    = $push_bullet_config['algorithm'];
        $this->iv           = $push_bullet_config['iv'];
        $this->ch           = new MyCurl();
    }

    /**
     * @param $otp_code
     * @param $phone
     * @return bool
     */
    public function sendOtpVoice($otp_code, $phone)
    {
        try {

            $api_url = $this->url_api ;
            $endpoint = $api_url;
            $method = 'POST';

            // Dữ liệu JSON để gửi
            $encrypted = $this->encryptAES256CBC($otp_code, $this->encrypted, $this->iv);
            $data = [
                'from' => $this->from,
                'to' => $phone,
                'otp' => [
                    'encrypted' => $encrypted ,
                    'algorithm' => $this->algorithm,
                    'iv' => $this->iv,
                ],
            ];
            // Chuyển đổi dữ liệu thành chuỗi JSON
            $json_data = json_encode($data);

            // Tạo một nguồn nguồn (cURL)
            $ch = curl_init($endpoint);

            // Thiết lập tùy chọn cho yêu cầu cURL
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);

            // Thực hiện yêu cầu và lấy kết quả
            $response = curl_exec($ch);

            // Kiểm tra lỗi
            if (curl_errno($ch)) {
                Yii::error(curl_error($ch));
                return false;
            }

            // Đóng kết nối cURL
            curl_close($ch);

            Yii::info("Send sms success");
            return true;

        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
        return true;
    }

    function encryptAES256CBC($data, $key, $iv) {
        return openssl_encrypt($data, 'aes-256-cbc', hex2bin($key), 0, hex2bin($iv));
    }
}