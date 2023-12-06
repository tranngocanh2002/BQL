<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 16/04/2017
 * Time: 8:50 SA
 */

namespace common\helpers\gmap;


use common\models\City;

class GMapDistanceResponse
{

    const STATUS_OK = 'OK';
    const STATUS_INVALID_REQUEST = 'INVALID_REQUEST';
    const STATUS_MAX_EXCEEDED = 'MAX_ELEMENTS_EXCEEDED';
    const STATUS_OVER_QUERY_LIMIT = 'OVER_QUERY_LIMIT';
    const STATUS_REQUEST_DENIED = 'REQUEST_DENIED';
    const STATUS_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

    public $status;
    public $origin_addresses;

    public $destination_addresses;
    /**
     * @var GMapDistance[]
     */
    public $rows = [];

    /**
     * @param $data array
     */
    public function load($data)
    {
        $this->status = isset($data['status'])?$data['status']:self::STATUS_UNKNOWN_ERROR;
        $this->origin_addresses = isset($data['origin_addresses'])?$data['origin_addresses']:[];
        $this->destination_addresses = isset($data['destination_addresses'])?$data['destination_addresses']:[];
        if(is_array($data['rows'])){
            foreach ($data['rows'] as $row){
                if(is_array($row['elements'])){
                    foreach ($row['elements'] as $element){
                        $gmap_element = new GMapDistance();
                        $gmap_element->status = isset($element['status'])?$element['status']:GMapDistance::STATUS_NOT_FOUND;
                        $gmap_element->distance = isset($element['distance'])?$element['distance']['value']:0;
                        $gmap_element->duration = isset($element['duration'])?$element['duration']['value']:0;
                        $gmap_element->fare = isset($element['fare'])?$element['fare']['value']:0;
                        $this->rows[] = $gmap_element;
                    }
                }
            }
        }
    }

    /**
     * @return GMapDistance|int
     */
    public function getDistance(){
        if(count($this->rows) > 0){
            return $this->rows[0];
        }
        return null;
    }

    /**
     * @return null|City
     */
    public function getOriginCity(){
        if(count($this->origin_addresses) > 0){
            $address = $this->origin_addresses[0];
            $array_addr = explode(',',$address);
            if(count($array_addr) > 1){
                $city_name = trim($array_addr[count($array_addr)-2]);
                return City::findOne(['long_name' => $city_name]);
            }
        }
        return null;
    }

    /**
     * @return null|City
     */
    public function getDestinationCity(){
        if(count($this->destination_addresses) > 0){
            $address = $this->destination_addresses[0];
            $array_addr = explode(',',$address);
            if(count($array_addr) > 1){
                $city_name = trim($array_addr[count($array_addr)-2]);
                return City::findOne(['long_name' => $city_name]);
            }
        }
        return null;
    }

}