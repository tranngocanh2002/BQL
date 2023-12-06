<?php

namespace console\controllers;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\models\BuildingCluster;
use common\models\Job;
use common\models\MaintenanceDevice;
use common\models\ManagementUser;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;


class MaintenanceDeviceController extends Controller
{
    /*
     * nhắc lịch bảo trì
     * Chạy định kỳ hàng ngày vào 07:00
     * chia ra nhiều luồng để chạy
     * $s : số luồng chạy đồng thời
     * $t : số dư phép chia mỗi luồng
     */
    public function actionCheck($s, $t)
    {
        echo "Job Check Start: " . date('Y-m-d H:i:s', time()) . "\n";
        $start_time = strtotime(date('Y-m-d 00:00:00', strtotime('+7 day',time())));
        $end_time = strtotime(date('Y-m-d 23:59:59', strtotime('+7 day',time())));
        $maintenanceDevices = MaintenanceDevice::find()->where([
            'status' => MaintenanceDevice::STATUS_ON
        ])
            ->andWhere(['>=', 'maintenance_time_next', $start_time])
            ->andWhere(['<=', 'maintenance_time_next', $end_time])
            ->andWhere("id%$s=$t");
        $building_cluster_ids = [];
        foreach ($maintenanceDevices->each() as $maintenanceDevice) {
            $building_cluster_ids[$maintenanceDevice->building_cluster_id][] = $maintenanceDevice;
        }
        foreach ($building_cluster_ids as $building_cluster_id => $maintenanceDevices){
            $emailAdmins = self::getArrayEmail($building_cluster_id, 'Admin BQL');
            $emailKts = self::getArrayEmail($building_cluster_id, 'Kỹ thuật');
            self::sendEmailRemind($building_cluster_id, $maintenanceDevices, $emailAdmins, $emailKts, $end_time);
        }
        echo "Job Check End: " . date('Y-m-d H:i:s', time()) . "\n";
    }

    public function getArrayEmail($building_cluster_id, $auth_group_name){
        $managementUserAdmins = ManagementUser::find()->where([
            'building_cluster_id' => $building_cluster_id,
            'is_deleted' => ManagementUser::NOT_DELETED
        ])->andWhere("auth_group_id in (select id from auth_group where building_cluster_id = $building_cluster_id and `name` = '".$auth_group_name."')")->all();
        $emails = [];
        foreach ($managementUserAdmins as $managementUserAdmin){
            $emails[] = $managementUserAdmin->email;
        }
        return $emails;
    }

    public function sendEmailRemind($building_cluster_id, $maintenanceDevices, $emailAdmins, $emailKts, $end_time){
        $buildingCluster = BuildingCluster::findOne(['id' => $building_cluster_id]);
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'maintenanceDevice-html'],
                    [
                        'buildingCluster' => $buildingCluster,
                        'maintenanceDevices' => $maintenanceDevices,
                        'end_time' => $end_time,
                    ]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => $buildingCluster->name])
                ->setTo($emailAdmins)
                ->setCc($emailKts)
                ->setSubject('Nhắc lịch bảo trì')
                ->send();
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }
}