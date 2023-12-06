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
class Pushbullet
{
    private $base_url = '';
    private $access_token = '';
    private $ch;

    public function __construct()
    {
        $push_bullet_config = Yii::$app->params['pushbullet'];
        $this->base_url = $push_bullet_config['base_url'];
        $this->access_token = $push_bullet_config['access_token'];
        $this->ch = new MyCurl();
    }

    /**
     * @param $email
     * @param $message
     * @return bool
     */
    public function pushNotification($email, $message)
    {

        try {
            $this->ch->headers = [
                'Content-Type' => 'application/json',
                'Access-Token' => $this->access_token,
            ];
            $body = Json::encode([
                'type' => 'note',
                'title' => '(Carento) New Booking Request',
                'body' => $message,
                'email' => $email
            ]);
            $response = $this->ch->post($this->base_url . '/pushes', $body);
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            $response = '';
            return false;
        }
        Yii::info($response->body);
        return true;
    }

}