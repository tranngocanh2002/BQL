<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 16/04/2017
 * Time: 8:50 SA
 */

namespace common\helpers\gmap;


class GMapDistanceRequest
{

    const MODE_DRIVING = 'driving';
    const MODE_BICYCLING = 'bicycling';
    const MODE_TRANSIT = 'transit';

    const UNIT_METRIC = 'metric';
    const UNIT_IMPERIAL = 'imperial';

    const TRAFFIC_MODE_BEST_GUEST = 'best_guess'; //  indicates that the returned duration_in_traffic should be the best estimate of travel time given what is known about both historical traffic conditions and live traffic
    const TRAFFIC_MODE_PESSIMISTIC = 'pessimistic'; // indicates that the returned duration_in_traffic should be longer than the actual travel time on most days, though occasional days with particularly bad traffic conditions may exceed this value
    const TRAFFIC_MODE_OPTIMISTIC = 'optimistic'; // indicates that the returned duration_in_traffic should be shorter than the actual travel time on most days, though occasional days with particularly good traffic conditions may be faster than this value

    const AVOID_TOLLS = 'tools';
    const AVOID_HIGHWAYS = 'highways';
    const AVOID_FERRIES = 'ferries';
    const AVOID_INDOOR = 'indoor';

    public $origins;
    public $destinations;

    public $mode = self::MODE_DRIVING;
    public $avoid = self::AVOID_FERRIES;
    public $units = self::UNIT_METRIC;
    public $arrival_time = 0;
    public $departure_time = 0;
    public $traffic_model = self::TRAFFIC_MODE_BEST_GUEST;

    /**
     * @return array
     */
    public function getRequestParam()
    {
        return [
            'origins' => $this->origins,
            'destinations' => $this->destinations,
            'mode' => $this->mode,
            'avoid' => $this->avoid,
            'units' => $this->units,
            'traffic_mode' => $this->traffic_model,
        ];
    }


}