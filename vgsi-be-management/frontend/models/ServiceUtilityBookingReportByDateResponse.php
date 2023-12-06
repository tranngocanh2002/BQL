<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityConfig;
use common\models\ServiceUtilityFree;
use common\models\ServiceUtilityPrice;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityBookingReportByDateResponse")
 * )
 */
class ServiceUtilityBookingReportByDateResponse extends Model
{
    public $start_date;
    public $end_date;
    public $service_utility_config_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_date', 'end_date', 'service_utility_config_id'], 'required'],
            [['start_date', 'end_date', 'service_utility_config_id'], 'integer'],
        ];
    }

    /**
     * @SWG\Property(property="date", type="integer"),
     * @SWG\Property(property="time", type="array",
     *      @SWG\Items(type="object",
     *          @SWG\Property(property="start_time", type="string"),
     *          @SWG\Property(property="end_time", type="string"),
     *          @SWG\Property(property="total_slot", type="integer"),
     *          @SWG\Property(property="total_slot_book", type="integer"),
     *      ),
     * ),
     */
    public function report()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $serviceUtilityConfig = ServiceUtilityConfig::findOne(['id' => $this->service_utility_config_id, 'building_cluster_id' => $buildingCluster->id]);
        if(empty($serviceUtilityConfig)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $times = [];
        $serviceUtilityPrices = ServiceUtilityPrice::find()->where(['service_utility_config_id' => $this->service_utility_config_id])->all();
        foreach ($serviceUtilityPrices as $serviceUtilityPrice){
            $times[$serviceUtilityPrice->start_time .'_'.$serviceUtilityPrice->end_time] = [
                'start_time' => $serviceUtilityPrice->start_time,
                'end_time' => $serviceUtilityPrice->end_time,
                'total_slot' => $serviceUtilityConfig->total_slot,
                'total_slot_book' => 0
            ];
        }
        Yii::info($times);
        $start_date_min = strtotime(date('Y-m-d 00:00:00', $this->start_date));
        $start_date_max = strtotime('+1 day', strtotime(date('Y-m-d 00:00:00', $this->end_date)));
        $serviceUtilityBooks = ServiceUtilityBooking::find()->where(['service_utility_config_id' => $this->service_utility_config_id])
            ->andWhere(['>=', 'status', ServiceUtilityBooking::STATUS_CREATE])
            ->andWhere(['>=', 'start_time', $start_date_min])
            ->andWhere(['<=', 'end_time', $start_date_max])
            ->all();
        $bookByDates = [];
        foreach ($serviceUtilityBooks as $serviceUtilityBook){
            $timeBooks = [];
            if(!empty($serviceUtilityBook->book_time)){
                $book_time = json_decode($serviceUtilityBook->book_time, true);
                foreach ($book_time as $item){
                    $timeBooks[] = [
                        'start_time_text' => date('H:i:s', $item['start_time']),
                        'end_time_text' => date('H:i:s', $item['end_time']),
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                        'total_slot_book' => $serviceUtilityBook->total_slot,
                    ];
                }
            }
            $bookByDates[date('Ymd', $serviceUtilityBook->start_time)][] = $timeBooks;
        }
        Yii::info($bookByDates);
        $dates = [];
        $date_time_next = $start_date_min;
        $i=0;
        while($date_time_next < $start_date_max){
            $i++;
            $str_date_next = date('Ymd', $date_time_next);
            $time_news = $times;
            if(isset($bookByDates[$str_date_next])){
                foreach ($bookByDates[$str_date_next] as $items){
                    foreach ($items as $timeBook){
                        foreach ($time_news as $k=>$time_new){
                            $start_time_in = strtotime(date('Y-m-d', $timeBook['start_time']).' '.$time_new['start_time'].':00');
                            $end_time_in = strtotime(date('Y-m-d', $timeBook['end_time']).' '.$time_new['end_time'].':00');
                            if($start_time_in <= $timeBook['start_time'] && $end_time_in >= $timeBook['end_time']){
                                $time_news[$k]['total_slot_book'] += $timeBook['total_slot_book'];
                            }
                        }
                    }
                }
            }
            $dates[$str_date_next] = [
                'date' => $date_time_next,
                'time' => array_values($time_news)
            ];
            $date_time_next = strtotime('+1 day', strtotime(date('Y-m-d 00:00:00', $date_time_next)));
            if($i >= 100){
                break;
            }
        }
        return array_values($dates);
    }
}
