<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 29/03/2017
 * Time: 3:49 CH
 */

namespace console\controllers;


use common\helpers\MyCurl;
use common\helpers\MyCurlResponse;
use common\helpers\QueueLib;
use common\helpers\RabitmqHelper;
use common\models\Admin;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class RabbitMqController extends Controller
{
    public function actionPublish($data)
    {
        $result = QueueLib::channelNotify($data);
        echo $result."\n";
    }
}