<?php

namespace common\helpers;

use Yii;

class AccountKit
{

    private $app_id;
    private $secret;
    private $version;
    private $url_graph;

    public function __construct()
    {
        // Initialize variables
        $account_kit = Yii::$app->params['account_kit'];
        $this->app_id = $account_kit['app_id'];
        $this->secret = $account_kit['app_secret'];
        $this->version = $account_kit['api_version']; // 'v1.1' for example
        $this->url_graph = $account_kit['url_graph'];
    }

    private function doCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $data;
    }

    public function getAccessToken($code)
    {
        // Exchange authorization code for access token
        $token_exchange_url = $this->url_graph . $this->version . '/access_token?grant_type=authorization_code&code=' . $code . "&access_token=AA|$this->app_id|$this->secret";
        Yii::info($token_exchange_url);
        $response = self::doCurl($token_exchange_url);
        Yii::info($response);
        if (!empty($response['error'])) {
            return null;
        } else {
            return $response['access_token'];
        }
    }

    public function getUserInfo($access_token)
    {

        // Get Account Kit information
        $appsecret_proof = hash_hmac('sha256', $access_token, $this->secret);
        $me_endpoint_url = $this->url_graph . $this->version . '/me?appsecret_proof=' . $appsecret_proof . '&access_token=' . $access_token;
        Yii::info($me_endpoint_url);
        $response = self::doCurl($me_endpoint_url);
        Yii::info($response);
        if(!empty($response['error'])){
            return null;
        }else{
            $phone = isset($response['phone']) ? $response['phone']['number'] : '';
            $email = isset($response['email']) ? $response['email']['address'] : '';
            return [
                'phone' => $phone,
                'email' => $email,
            ];
        }

    }
}