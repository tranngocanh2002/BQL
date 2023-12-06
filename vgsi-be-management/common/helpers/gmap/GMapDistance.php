<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 16/04/2017
 * Time: 9:14 SA
 */

namespace common\helpers\gmap;


class GMapDistance
{
    const STATUS_OK = 'OK';
    const STATUS_NOT_FOUND = 'NOT_FOUND';
    const STATUS_ZERO_RESULTS = 'ZERO_RESULTS';
    const STATUS_MAX_ROUTE_LENGTH_EXCEEDED = 'MAX_ROUTE_LENGTH_EXCEEDED';

    public $status;
    public $duration;
    public $distance;
    public $fare;

}