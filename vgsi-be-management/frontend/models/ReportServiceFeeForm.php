<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ReportServiceFeeForm")
 * )
 */
class ReportServiceFeeForm extends Model
{
    public $month;
    public $from_month;
    public $to_month;
    public $type;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['month', 'from_month', 'to_month', 'type'], 'integer'],
        ];
    }

    /**
     *      @SWG\Property(property="month", type="integer", description="dữ liệu của tháng"),
     *      @SWG\Property(property="total", type="integer", description="tổng phí"),
     *      @SWG\Property(property="total_unpaid", type="integer", description="tổng phí chưa thanh toán"),
     *      @SWG\Property(property="total_paid", type="integer", description="tổng phí đã thanh toán"),
     */
//    public function byDay($params)
//    {
//        Yii::info($params);
//        $this->load(CUtils::modifyParams($params),'');
//        $buildingCluster = Yii::$app->building->BuildingCluster;
//        $building_cluster_id = $buildingCluster->id;
//        Yii::info($this->month);
//        $month = (!empty($this->month)) ? (int)$this->month : time();
//        $startDay = strtotime(date('Y-m-01 00:00:00', $month));
//        $endDay = strtotime(date('Y-m-01 00:00:00', strtotime("+1 month", $month)));
//        Yii::info($startDay);
//        Yii::info($endDay);
//        Yii::info(date('Y-m-d', $startDay));
//        Yii::info(date('Y-m-d', $endDay));
//
//        $sql = Yii::$app->db;
//        $countByService = $sql->createCommand("select status,service_map_management_id,sum(total_price) as total_price from service_fee_report_date where building_cluster_id = $building_cluster_id and `date` >= $startDay and `date` <= $endDay group by status,service_map_management_id")->queryAll();
//        $countAllService = [];
//        $countAllByStatus = [];
//        $serviceMapArr = [];
//        foreach ($countByService as $row){
//            $id_map = (int)$row['service_map_management_id'];
//            if(empty($serviceMapArr[$id_map])){
//                $serviceMap = ServiceMapManagement::findOne(['id' => $id_map]);
//                $serviceMapArr[$id_map] = $serviceMap->service_name;
//            }
//            if(empty($countAllService[$id_map])){
//                $countAllService[$id_map] = [
//                    'name' => $serviceMapArr[$id_map],
//                    'total_price' => (int)$row['total_price']
//                ];
//            }else{
//                $countAllService[$id_map]['total_price'] += (int)$row['total_price'];
//            }
//
//            if(empty($countAllByStatus[$id_map])){
//                $countAllByStatus[$id_map] = [
//                    'name' => $serviceMapArr[$id_map],
//                    'total_unpaid' => 0,
//                    'total_paid' => 0
//                ];
//            }
//            if((int)$row['status'] == ServicePaymentFee::STATUS_UNPAID){
//                $countAllByStatus[$id_map]['total_unpaid'] = (int)$row['total_price'];
//            }else if((int)$row['status'] == ServicePaymentFee::STATUS_PAID){
//                $countAllByStatus[$id_map]['total_paid'] = (int)$row['total_price'];
//            }
//        }
//
//        //tạo tại array response
//        $countAllServiceObj = [];
//        foreach($countAllService as $i => $item) {
//            $countAllServiceObj[] = [
//                'id' => $i,
//                'name' => $item['name'],
//                'total_price' => isset($item['total_price']) ? $item['total_price'] : 0,
//            ];
//        }
//
//        $countAllByStatusObj = [];
//        foreach($countAllByStatus as $i => $item) {
//            $countAllByStatusObj[] = [
//                'id' => $i,
//                'name' => $item['name'],
//                'total_unpaid' => isset($item['total_unpaid']) ? $item['total_unpaid'] : 0,
//                'total_paid' => isset($item['total_paid']) ? $item['total_paid'] : 0,
//            ];
//        }
//
//        return [
//            'countAllService' =>  $countAllServiceObj,
//            'countAllByStatus' =>  $countAllByStatusObj,
//        ];
//    }

    public function byMonth($params)
    {
        Yii::info($params);
        $this->load(CUtils::modifyParams($params),'');
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $from_month = (!empty($this->from_month)) ? (int)$this->from_month : time();
        $to_month = (!empty($this->to_month)) ? (int)$this->to_month : time();
        if(!isset($this->type)){
            $this->type = 0;
        }
        $start_date = strtotime(date('Y-m-01 00:00:00', $from_month));
        if($this->type == 0){
            $next_date = 'month';
            $end_date = strtotime(date('Y-m-01 00:00:00', strtotime("+1 $next_date", $to_month)));
        }else{
            $next_date = 'day';
            $end_date = strtotime(date('Y-m-d 00:00:00', strtotime("+1 $next_date", $to_month)));
        }

        $datas = [];
        $i = 0;
        $date_time_next = $start_date;
        while ($date_time_next < $end_date) {
            $start_time = $date_time_next;
            $end_time = strtotime("+1 $next_date", $start_time);
            $datas[] = self::countByTime($building_cluster_id, $start_time, $end_time);
            $date_time_next = $end_time;
            if ($i >= 50 && $this->type == 0) {
                break;
            }
        }
        return $datas;
    }

    private function countByTime($building_cluster_id, $start_time, $end_time){
        $sql = Yii::$app->db;
        $countByService = $sql->createCommand("select status,sum(total_price) as total_price from service_fee_report_date where building_cluster_id = $building_cluster_id and `date` >= $start_time and `date` < $end_time group by status")->queryAll();
        $res = [
            'month' => $start_time,
            'total' => 0,
            'total_unpaid' => 0,
            'total_paid' => 0,
        ];
        $total = 0;
        foreach ($countByService as $row){
            $total += (int)$row['total_price'];
            if((int)$row['status'] == ServicePaymentFee::STATUS_UNPAID){
                $res['total_unpaid'] = (int)$row['total_price'];
            }else{
                $res['total_paid'] = (int)$row['total_price'];
            }
        }
        $res['total'] = $total;
        return $res;
    }
}
