<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 16/04/2017
 * Time: 8:46 SA
 */

namespace common\helpers\gmap;


use common\helpers\MyCurl;
use yii\helpers\Json;

class GMapService
{
    const GMAP_SERVICE_BASE = 'https://maps.googleapis.com/maps/api/';
    const GMAP_SERVICE_DISTANCE_MATRIX = 'distancematrix/';

    const GMAP_OUT_JSON = 'json';
    const GMAP_OUT_XML = 'xml';

    private $ch;
    private $key;

    public function __construct()
    {
        $this->ch = new MyCurl();
        $this->key = \Yii::$app->params['gmap_key'];
    }

    public function buildUrlService($service, $out_format){
        return self::GMAP_SERVICE_BASE.$service.$out_format;
    }

    /**
     * @param $request GMapDistanceRequest
     * @return GMapDistanceResponse|null
     */
    public function getDistance($request){
        try{
            $params = $request->getRequestParam();
            $params['key'] = $this->key;
            $response = $this->ch->get($this->buildUrlService(self::GMAP_SERVICE_DISTANCE_MATRIX, self::GMAP_OUT_JSON), $params);
        }catch (\Exception $e){
            $response = '';
            \Yii::error('Call gmap service error: '.$e->getMessage());
            return null;
        }
        if(!empty($response->body)){
            \Yii::info($response->body);
            $gmap_response = new GMapDistanceResponse();
            $gmap_response->load(Json::decode($response->body));
            return $gmap_response;
        }else{
            return null;
        }
    }

}