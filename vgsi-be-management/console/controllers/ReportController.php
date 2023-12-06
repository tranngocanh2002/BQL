<?php

namespace console\controllers;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingCluster;
use common\models\RequestReportDate;
use common\models\ResidentUserCountByAge;
use common\models\ServiceBill;
use common\models\ServiceBookingReportWeek;
use common\models\ServiceFeeReportDate;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;


class ReportController extends Controller
{
    /*
     * $time = 'Y-m-d'
     */
    public function actionRequestDate($time = null)
    {
        $sql = Yii::$app->db;
        if ($time == null) {
            $time = time();
        } else {
            $time = strtotime($time);
        }
        echo date('Y-m-d 00:00:00', strtotime("-1 day", $time));
        echo "\n";
        $date = strtotime(date('Y-m-d 00:00:00', strtotime("-1 day", $time)));
        $startDate = strtotime(date('Y-m-d 00:00:00', strtotime("-1 day", $time)));
        $endDate = strtotime(date('Y-m-d 23:59:59', strtotime("-1 day", $time)));
        $buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
        foreach ($buildingClusters as $buildingCluster) {
            $dataRes = $sql->createCommand("select count(*) as total,status,request_category_id from request where building_cluster_id = $buildingCluster->id and created_at >= $startDate and created_at <= $endDate group by status,request_category_id")->queryAll();
            foreach ($dataRes as $item) {
                //check report đã tạo thì update
                $requestReportDate = RequestReportDate::findOne(['date' => $date, 'building_cluster_id' => $buildingCluster->id, 'status' => $item['status'], 'request_category_id' => $item['request_category_id']]);
                if (empty($requestReportDate)) {
                    $requestReportDate = new RequestReportDate();
                }
                $requestReportDate->date = $date;
                $requestReportDate->status = $item['status'];
                $requestReportDate->request_category_id = $item['request_category_id'];
                $requestReportDate->total = $item['total'];
                $requestReportDate->building_cluster_id = $buildingCluster->id;
                $requestReportDate->save();
            }
        }
    }

    /*
     * $time = 'Y-m-d'
     */
    public function actionServiceFeeDate($time = null)
    {
        $sql = Yii::$app->db;
        if ($time == null) {
            $time = strtotime(date('Y-m-01 00:00:00', strtotime("-1 month", time())));
        } else {
            $time = strtotime($time);
        }
        echo date('Y-m-d 00:00:00', $time);
        echo "\n";
        $dateStartMonth = strtotime(date('Y-m-01 00:00:00', $time));
//        $date = strtotime(date('Y-m-d 00:00:00', strtotime("+1 day", $time)));
        $date = strtotime(date('Y-m-d 00:00:00', strtotime("+1 day", time())));
        $buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
        foreach ($buildingClusters as $buildingCluster) {
            echo "buildingCluster $buildingCluster->id \n";
            $startDate = $dateStartMonth;
            while ($startDate <= $date) {
                echo date("Y-m-d H:i:s", $startDate) . "\n";
                $endDate = strtotime(date('Y-m-d 00:00:00', strtotime("+1 day", $startDate)));
                $dataRes = $sql->createCommand("select sum(price) as total_price,status,service_map_management_id from service_payment_fee where building_cluster_id = $buildingCluster->id and is_draft = 0 and is_debt = 1 and created_at >= $startDate and created_at <= $endDate group by status,service_map_management_id")->queryAll();
                foreach ($dataRes as $item) {
                    echo "Data => " . $item['service_map_management_id'] . " = > " . $item['status'] . " => " . $item['total_price'] . "\n";
                    //check report đã tạo thì update
                    $reportDate = ServiceFeeReportDate::findOne(['date' => $startDate, 'building_cluster_id' => $buildingCluster->id, 'status' => $item['status'], 'service_map_management_id' => $item['service_map_management_id']]);
                    if (empty($reportDate)) {
                        echo " create \n";
                        $reportDate = new ServiceFeeReportDate();
                    }
                    $reportDate->date = $startDate;
                    $reportDate->status = $item['status'];
                    $reportDate->service_map_management_id = $item['service_map_management_id'];
                    $reportDate->total_price = $item['total_price'];
                    $reportDate->building_cluster_id = $buildingCluster->id;
                    $reportDate->save();
                }
                $startDate = $endDate;
            }
        }
    }

    /*
     * $time = 'Y-m-d'
     * chuyển $time về đầu tuần vào cuối tuần
     * lấy dữ liệu báo cáo trong một tuần
     * chạy hàng ngày -> lùi về 1 ngày trước đó, update dữ liệu cho tuần hiện tại
     * chạy lúc 02h30 hàng ngày
     */
    public function actionServiceBookingWeek($current_day = 0, $time = null)
    {
        if ($time == null) {
            $time = time();
        } else {
            $time = strtotime($time);
        }
        if($current_day == 0){
            $time = strtotime("-1 day", $time);
        }
        echo date('Y-m-d 00:00:00', $time);
        echo "\n";
        $start_time_week = CUtils::startTimeWeek($time); // đầu tuần
        $next_start_time_week = CUtils::startTimeNextWeek($time); // đầu tuần tiếp theo

        $datas = [];

        $buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
        foreach ($buildingClusters as $buildingCluster) {
            echo "buildingCluster $buildingCluster->id \n";
            $dataRes = ServiceUtilityBooking::find()
                ->leftJoin('service_payment_fee', 'service_payment_fee.id=service_utility_booking.service_payment_fee_id')
                ->where(['service_utility_booking.building_cluster_id' => $buildingCluster->id, 'service_payment_fee.is_debt' => ServicePaymentFee::IS_DEBT])
                ->andWhere(['>=', 'service_payment_fee.fee_of_month', $start_time_week])
                ->andWhere(['<', 'service_payment_fee.fee_of_month', $next_start_time_week])
                ->all();
            foreach ($dataRes as $item) {
                if(!isset($datas[$item->building_cluster_id])){
                    $datas[$item->building_cluster_id] = [];
                }
                if(!isset($datas[$item->building_cluster_id][$item->service_map_management_id])){
                    $datas[$item->building_cluster_id][$item->service_map_management_id] = [];
                }
                if(!isset($datas[$item->building_cluster_id][$item->service_map_management_id][$item->service_utility_config_id])){
                    $datas[$item->building_cluster_id][$item->service_map_management_id][$item->service_utility_config_id] = [];
                }
                if(!isset($datas[$item->building_cluster_id][$item->service_map_management_id][$item->service_utility_config_id][$item->service_utility_free_id])){
                    $datas[$item->building_cluster_id][$item->service_map_management_id][$item->service_utility_config_id][$item->service_utility_free_id] = [];
                }
                if(!isset($datas[$item->building_cluster_id][$item->service_map_management_id][$item->service_utility_config_id][$item->service_utility_free_id][$item->servicePaymentFee->status])){
                    $datas[$item->building_cluster_id][$item->service_map_management_id][$item->service_utility_config_id][$item->service_utility_free_id][$item->servicePaymentFee->status] = 0;
                }
                $datas[$item->building_cluster_id][$item->service_map_management_id][$item->service_utility_config_id][$item->service_utility_free_id][$item->servicePaymentFee->status] += $item->price;
                echo $item->status;
                echo "=====";
                if(!empty($item->servicePaymentFee)){
                    echo $item->servicePaymentFee->status;
                }else{
                    echo 'not fee';
                }
                echo "\r\n";
            }
        }

        if(!empty($datas)){
            foreach ($datas as $building_cluster_id => $smm){
                foreach ($smm as $service_map_management_id => $suc){
                    foreach ($suc as $service_utility_config_id => $suf){
                        foreach ($suf as $service_utility_free_id => $sp){
                            foreach ($sp as $status => $price){
                                $serviceBookReportWeek = ServiceBookingReportWeek::findOne([
                                    'building_cluster_id' => $building_cluster_id,
                                    'service_map_management_id' => $service_map_management_id,
                                    'service_utility_config_id' => $service_utility_config_id,
                                    'service_utility_free_id' => $service_utility_free_id,
                                    'status' => $status,
                                    'date' => $start_time_week,
                                ]);
                                if(empty($serviceBookReportWeek)){
                                    $serviceBookReportWeek = new ServiceBookingReportWeek();
                                    $serviceBookReportWeek->building_cluster_id = $building_cluster_id;
                                    $serviceBookReportWeek->service_map_management_id = $service_map_management_id;
                                    $serviceBookReportWeek->service_utility_config_id = $service_utility_config_id;
                                    $serviceBookReportWeek->service_utility_free_id = $service_utility_free_id;
                                    $serviceBookReportWeek->status = $status;
                                    $serviceBookReportWeek->date = $start_time_week;
                                }
                                $serviceBookReportWeek->total_price = $price;
                                if(!$serviceBookReportWeek->save()){
                                    Yii::error($serviceBookReportWeek->errors);
                                    echo 'serviceBookReportWeek error';
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /*
     * Tính thống kê độ tuổi của cư dân
     * Nếu hệ thống lớn, thì tách job chạy riêng cho từng cluster
     */
    public function actionCountResidentByAge()
    {
        $buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
        $arrayAllDataRes = [];
        foreach ($buildingClusters as $buildingCluster){
            $arrayDataRes = [
                '0_14' => [
                    'start_age' => 0,
                    'end_age' => 14,
                    'total_foreigner' => 0,
                    'total_vietnam' => 0,
                    'total' => 0,
                ],
                '15_54' => [
                    'start_age' => 15,
                    'end_age' => 54,
                    'total_foreigner' => 0,
                    'total_vietnam' => 0,
                    'total' => 0,
                ],
                '55_x' => [
                    'start_age' => 55,
                    'end_age' => 1000,
                    'total_foreigner' => 0,
                    'total_vietnam' => 0,
                    'total' => 0,
                ],
            ];
            $apartmentMapResidentUsers = ApartmentMapResidentUser::find()
                ->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])
                ->groupBy(['resident_user_id'])->all();
            foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
                $age = 15;
                $year_current = date('Y', time());
                if(!empty($apartmentMapResidentUser->resident_user_birthday)){
                    $age = $year_current - date('Y', $apartmentMapResidentUser->resident_user_birthday);
                }
                foreach ($arrayDataRes as $k => $v){
                    if($age >= $v['start_age'] && $age <= $v['end_age']){
                        $arrayDataRes[$k]['total']++;
                        if(empty($apartmentMapResidentUser->resident_user_nationality) || in_array(strtolower($apartmentMapResidentUser->resident_user_nationality), ['vn', 'vi'])){
                            $arrayDataRes[$k]['total_vietnam']++;
                        }else{
                            $arrayDataRes[$k]['total_foreigner']++;
                        }
                    }
                }
            }
            $arrayAllDataRes[$buildingCluster->id] = $arrayDataRes;
        }

        foreach ($arrayAllDataRes as $building_cluster_id => $items){
            foreach ($items as $item){
                $residentUserCountByAge = ResidentUserCountByAge::findOne(['building_cluster_id' => $building_cluster_id, 'start_age' => $item['start_age'], 'end_age' => $item['end_age']]);
                if(empty($residentUserCountByAge)){
                    $residentUserCountByAge = new ResidentUserCountByAge();
                    $residentUserCountByAge->building_cluster_id = $building_cluster_id;
                    $residentUserCountByAge->start_age = $item['start_age'];
                    $residentUserCountByAge->end_age = $item['end_age'];
                }
                $residentUserCountByAge->total = $item['total'];
                $residentUserCountByAge->total_vietnam = $item['total_vietnam'];
                $residentUserCountByAge->total_foreigner = $item['total_foreigner'];
                if(!$residentUserCountByAge->save()){
                    Yii::error($residentUserCountByAge->errors);
                }
            }
        }
    }
}