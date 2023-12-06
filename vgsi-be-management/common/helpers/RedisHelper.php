<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 07/06/2017
 * Time: 5:07 CH
 */

namespace common\helpers;


use Yii;
use yii\helpers\Json;

class RedisHelper
{
    /**
     * @param $channel
     * @param $payload
     * @return boolean
     */
    public static function pub($channel, $payload){
        return Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => $channel,
            'message' => Json::encode($payload)
        ]);
    }
}