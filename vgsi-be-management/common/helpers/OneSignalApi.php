<?php

namespace common\helpers;

use Yii;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\Common\HttpMethodsClient as HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use OneSignal\Config;
use OneSignal\OneSignal;


class OneSignalApi
{

    private $app_id;
    private $api;

    public function __construct()
    {
        $OneSignal = Yii::$app->params['OneSignal'];
        $this->app_id = $OneSignal['APP_ID'];
        $config = new Config();
        $config->setApplicationId($OneSignal['APP_ID']);
        $config->setApplicationAuthKey($OneSignal['REST_API_KEY']);
        $config->setUserAuthKey($OneSignal['USER_AUTH_KEY']);

        $guzzle = new GuzzleClient([ // http://docs.guzzlephp.org/en/stable/quickstart.html
            // ..config
        ]);

        $client = new HttpClient(new GuzzleAdapter($guzzle), new GuzzleMessageFactory());
        $this->api = new OneSignal($config, $client);
    }

    public function updateDevice($device_id, $objs)
    {
        try {
            return $this->api->devices->update($device_id, $objs);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
        return true;
    }

    public function sendToTags($subtitle, $content, $filters, $data = [])
    {
        $objs = [
            'headings' => ['en' => $subtitle],
//            'subtitle' => ['en' => $subtitle],
            'contents' => ['en' => $content],
            'priority' => 10,
//            'filters' => $filters,
//            'filters' => [
//                [
//                    'field' => 'tag',
//                    'key' => 'is_vip',
//                    'relation' => '!=',
//                    'value' => 'true',
//                ],
//                [
//                    'operator' => 'OR',
//                ],
//                [
//                    'field' => 'tag',
//                    'key' => 'is_admin',
//                    'relation' => '=',
//                    'value' => 'true',
//                ],
//            ],
        ];
        try {
            if (!empty($data)) {
                $objs['data'] = $data;
            }
            if (!empty($filters)) {
                $objs['filters'] = $filters;
                return $this->api->notifications->add($objs);
            }
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
        return true;
    }

    public function sendToPlayerIds($subtitle, $content, $player_ids, $data = [])
    {
        $objs = [
            'headings' => ['en' => $subtitle],
//            'subtitle' => ['en' => $subtitle],
            'contents' => ['en' => $content],
            'priority' => 10,
        ];
        try {
            if (!empty($data)) {
                $objs['data'] = $data;
            }
            if (!empty($player_ids)) {
                $objs['include_player_ids'] = $player_ids;
                return $this->api->notifications->add($objs);
            }
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
        return true;
    }

    public function sendToWorkerTags($subtitle, $content, $filters, $data = [])
    {
        $objs = [
            'headings' => ['en' => $subtitle],
//            'subtitle' => ['en' => $subtitle],
            'contents' => ['en' => $content],
            'priority' => 10,
//            'filters' => $filters,
//            'filters' => [
//                [
//                    'field' => 'tag',
//                    'key' => 'is_vip',
//                    'relation' => '!=',
//                    'value' => 'true',
//                ],
//                [
//                    'operator' => 'OR',
//                ],
//                [
//                    'field' => 'tag',
//                    'key' => 'is_admin',
//                    'relation' => '=',
//                    'value' => 'true',
//                ],
//            ],
        ];
        if (!empty($data)) {
            $objs['data'] = $data;
        }
        if (!empty($filters)) {
            $objs['filters'] = $filters;
            QueueLib::channelNotify(json_encode($objs));
        }
        return null;
    }

    public function sendToWorkerPlayerIds($subtitle, $content, $subtitle_en, $content_en, $player_ids, $data = [], $url = null, $app_id = null, $callback = [])
    {

        if(empty($subtitle)){
            Yii::error('empty subtitle');
            return null;
        }
        $objs = [
            'app_id' => (!empty($app_id)) ? $app_id : $this->app_id,
            'headings' => ['vi' => $subtitle, 'en' => $subtitle_en],
//            'subtitle' => ['en' => $subtitle],
            'contents' => ['vi' => $content, 'en' => $content_en],
            'priority' => 10,
//            'callback' => [
//                'id' => $campaignQueue->id,
//                'url' => Yii::$app->params['domain_api'] . '/campaign/callback',
//                'api_key_name' => 'x-lumi-api-key',
//                'api_key_value' => Yii::$app->params['system-api-key'],
//            ]
        ];
        if(!empty($callback)){
            $objs['callback'] = $callback;
        }
        if (!empty($url)) {
            $objs['url'] = $url;
        }
        if (!empty($data)) {
            $objs['data'] = $data;
        }
        if (!empty($player_ids)) {
            $objs['include_player_ids'] = $player_ids;
            QueueLib::channelNotify(json_encode($objs));
        }
        return null;
    }

}