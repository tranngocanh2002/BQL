<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceUtilityFree;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ReportServiceBookingRevenueForm")
 * )
 */
class ReportServiceBookingRevenueForm extends Model
{
    public $service_utility_free_id;
    public $start_date;
    public $end_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_utility_free_id'], 'integer'],
            [['start_date', 'end_date'], 'safe']
        ];
    }

    /**
     *    @SWG\Property(property="service_utility_free_id", type="integer", description="id loại dịch vụ"),
     *    @SWG\Property(property="service_utility_free_name", type="string", description="tên loại dịch vụ"),
     *    @SWG\Property(property="total_price", type="integer", description="tổng tiền"),
     */
    public function revenue($params)
    {
        Yii::info($params);
        $this->load(CUtils::modifyParams($params),'');
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;

        if(empty($this->start_date)){
            $this->start_date = time();
        }
        if(empty($this->end_date)){
            $this->end_date = time();
        }

        $start_date = CUtils::startTimeWeek($this->start_date); // đầu tuần
        $end_date = CUtils::startTimeNextWeek($this->end_date); // đầu tuần tiếp theo

        $sql = Yii::$app->db;
        $countByService = $sql->createCommand("select service_utility_free_id,sum(total_price) as total_price from service_booking_report_week where building_cluster_id = $building_cluster_id and status = 1 and `date` >= $start_date and `date` <= $end_date group by service_utility_free_id")->queryAll();
        $countAllServiceBooking = [];
        $serviceMapArr = [];
        foreach ($countByService as $row){
            $service_utility_free_id = (int)$row['service_utility_free_id'];
            if(empty($serviceMapArr[$service_utility_free_id])){
                $serviceMap = ServiceUtilityFree::findOne(['id' => $service_utility_free_id]);
                $serviceMapArr[$service_utility_free_id] = $serviceMap->name;
            }
            if(empty($countAllServiceBooking[$service_utility_free_id])){
                $countAllServiceBooking[$service_utility_free_id] = [
                    'service_utility_free_id' => $service_utility_free_id,
                    'service_utility_free_name' => $serviceMapArr[$service_utility_free_id],
                    'total_price' => (int)$row['total_price']
                ];
            }else{
                $countAllServiceBooking[$service_utility_free_id]['total_price'] += (int)$row['total_price'];
            }
        }

        return array_values($countAllServiceBooking);
    }
}
