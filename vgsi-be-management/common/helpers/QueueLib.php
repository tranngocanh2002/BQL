<?php

namespace common\helpers;

use Exception;
use Yii;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\helpers\Json;

class QueueLib
{
    public static function channelEparking($message, $building_cluster_id)
    {
        $queueName = Yii::$app->params['queueName']['SendEparking'];
        Yii::info($queueName);
        self::publishRMQ($queueName . '_' . $building_cluster_id, $message);
    }

    public static function channelFaceRecognition($message, $building_cluster_id)
    {
        $queueName = Yii::$app->params['queueName']['SendFaceRecognition'];
        Yii::info($queueName);
        self::publishRMQ($queueName . '_' . $building_cluster_id, $message);
    }

    public static function channelEmailAws($message, $force = false)
    {
        $queueName = Yii::$app->params['queueName']['SendEmailAws'];
        $queueNameSend = Yii::$app->params['queueNameSend']['SendEmailAws'];
        Yii::info($queueName);
        if ($queueNameSend == true || $force == true) {
            self::publishRMQ($queueName, $message);
        }
    }

    public static function channelEmailAwsAttachments($message, $force = false)
    {
        $queueName = Yii::$app->params['queueName']['SendEmailAwsAttachments'];
        $queueNameSend = Yii::$app->params['queueNameSend']['SendEmailAwsAttachments'];
        Yii::info($queueName);
        if ($queueNameSend == true || $force == true) {
            self::publishRMQ($queueName, $message);
        }
    }

    public static function channelNotify($message, $force = false)
    {
        $queueName = Yii::$app->params['queueName']['SendNotify'];
        $queueNameSend = Yii::$app->params['queueNameSend']['SendNotify'];
        Yii::info($queueName);
        Yii::info($message);
        if ($queueNameSend == true || $force == true) {
            self::publishRMQ($queueName, $message);
        }
    }

    public static function channelSms($message, $force = false)
    {
        $queueName = Yii::$app->params['queueName']['SendSms'];
        $queueNameSend = Yii::$app->params['queueNameSend']['SendSms'];
        Yii::info($queueName);
        Yii::info($message);
        if ($queueNameSend == true || $force == true) {
            self::publishRMQ($queueName, $message);
        }
    }

    private static function publishRMQ($queueName, $message)
    {
        $ch = new MyCurl();
        $config = Yii::$app->params['rabbitmq'];
        $base_url = $config['base_url'];
        $user = $config['user'];
        $pass = $config['pass'];

        $url = $base_url . "api/exchanges/%2F/amq.default/publish";
        try {
            $ch->options = [
                "HTTPAUTH" => CURLAUTH_BASIC,
                "USERPWD" => "$user:$pass"
            ];
            $body = Json::encode([
                'properties' => [
                    'delivery_mode' => 1,
                ],
                'name' => "amq.default",
                'payload' => $message,
                'payload_encoding' => 'string',
                'routing_key' => $queueName,
                'vhost' => '/'
            ]);
            $response = $ch->post($url, $body);
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
        /**
         * Fix with return multi header.
         * Xay ra khi phia mqtt tra ve dang http status 100
         */
        if (preg_match('/HTTP\/\d\.\d (\d+)/ims', $response, $output_array)) {
            if (count($output_array) > 0) {
                if ($output_array[1] == '100') {
                    $response = new MyCurlResponse($response);
                }
            }
        }
        Yii::info($response->body);
        try {
            $result = Json::decode($response->body);
        } catch (Exception $e) {
            return false;
        }

        if (isset($result['routed'])) {
            return ($result['routed'] == true);
        } else {
            Yii::error($response->body);
            return false;
        }
    }

}
