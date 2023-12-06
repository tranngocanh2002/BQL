<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityPrice;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityPriceResponse")
 * )
 */
class ServiceUtilityPriceResponse extends ServiceUtilityPrice
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="service_utility_free_id", type="integer"),
     * @SWG\Property(property="service_utility_config_id", type="integer"),
     * @SWG\Property(property="start_time", type="string"),
     * @SWG\Property(property="end_time", type="string"),
     * @SWG\Property(property="slot_null", type="integer", description="Số chỗ trống hiện tại"),
     * @SWG\Property(property="price_hourly", type="integer", description="giá theo 1 giờ"),
     * @SWG\Property(property="price_adult", type="integer", description="giá lượt người lớn"),
     * @SWG\Property(property="price_child", type="integer", description="giá lượt trẻ em"),
     */
    public function fields()
    {
        return [
            'id',
            'building_cluster_id',
            'service_utility_free_id',
            'service_utility_config_id',
            'start_time',
            'end_time',
            'slot_null' => function($model){
                $total_slot = 0;
                if(!empty($model->serviceUtilityConfig)){
                    $total_slot = $model->serviceUtilityConfig->total_slot;
                }
                $session = Yii::$app->session;
                $current_time = time();
                if (isset($session['current_time'])){
                    $current_time = $session['current_time'];
                }
                $current_time_start = strtotime(date('Y-m-d 00:00:00', $current_time));
                $current_time_end = strtotime(date('Y-m-d 23:59:59', $current_time));
                $start_time_in = strtotime(date('Y-m-d', $current_time).' '.$model->start_time.':00');
                $end_time_in = strtotime(date('Y-m-d', $current_time).' '.$model->end_time.':00');
                $serviceUtilityBookings = ServiceUtilityBooking::find()->where(['service_utility_config_id' => $model->serviceUtilityConfig->id])
                    ->andWhere(['>=', 'start_time', $current_time_start])
                    ->andWhere(['<=', 'end_time', $current_time_end])
                    ->andWhere(['>=', 'status', ServiceUtilityBooking::STATUS_CREATE])
                    ->all();
                $serviceUtilityBookingCount = 0;
                foreach ($serviceUtilityBookings as $serviceUtilityBooking){
                    if(!empty($serviceUtilityBooking->book_time)){
                        $book_time = json_decode($serviceUtilityBooking->book_time, true);
                        foreach ($book_time as $item){
                            $start_time = $item['start_time'];
                            $end_time = $item['end_time'];
                            if($start_time_in <= $start_time && $end_time_in >= $end_time){
                                $serviceUtilityBookingCount += $serviceUtilityBooking->total_slot;
                            }
                        }
                    }
                }
                $slot_null = $total_slot - $serviceUtilityBookingCount;
                if($slot_null < 0){
                    $slot_null = 0;
                }
                return $slot_null;
            },
            'price_hourly',
            'price_adult',
            'price_child',
        ];
    }
}
