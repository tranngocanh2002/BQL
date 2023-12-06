<?php
/* @var $this yii\web\View */
/* @var $serviceBill common\models\ServiceBill */
/* @var $serviceBillItems common\models\ServiceBillItem */

/* @var $apartment common\models\Apartment */
/* @var $campaign_type */

use common\helpers\CUtils;
use yii\helpers\Html;
use common\models\ServicePaymentFee;
use common\models\ServiceBuildingConfig;
use common\models\AnnouncementTemplate;
die($campaign_type);
/*
 * type : 0 - phí nước, 1 - phí dịch vụ, 2 - phí xe
 */
$start_current_month = strtotime(date('Y-m-01 00:00:00'));
$end_current_month = strtotime(date('Y-m-t 23:59:59', $start_current_month));
//phí dịch vụ của tháng hiện tại
$servicePaymentFee = ServicePaymentFee::find()
    ->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE])
    ->andWhere(['>=', 'fee_of_month', $start_current_month])
    ->andWhere(['<=', 'fee_of_month', $end_current_month])
    ->one();
$serviceBuildingConfig = null;
if(!empty($servicePaymentFee)){
    //lấy thông tin config phí dịch vụ
    $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $servicePaymentFee->building_cluster_id, 'service_map_management_id' => $servicePaymentFee->service_map_management_id]);
}
//phí dịch vụ nợ cũ -> trước tháng hiện tại
$total_servicePaymentFee = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE])
    ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

//phí nước tháng hiện tại
$servicePaymentFeeWater = ServicePaymentFee::find()
    ->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE])
    ->andWhere(['>=', 'fee_of_month', $start_current_month])
    ->andWhere(['<=', 'fee_of_month', $end_current_month])
    ->one();
//phí nước nợ cũ
$total_servicePaymentFeeWater = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE])
    ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

//tổng nợ
$total_payment = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID])->one();
$totalPayment = 0;
if(!empty($total_payment)){
    $totalPayment = (int)$total_payment->more_money_collecte;
}

$announcementTemplate = AnnouncementTemplate::findOne(['building_cluster_id' => $apartment->building_cluster_id, 'type' => $campaign_type]);
$html = $announcementTemplate->content_pdf;
$html = str_replace('{{RESIDEND_USER_NAME}}', $apartment->resident_user_name, $html);
$html = str_replace('{{APARTMENT_NAME}}', $apartment->name, $html);
?>
<?php
$buildingFeeList = $this->render('table/buidling_fee_list', ['serviceBuildingConfig' => $serviceBuildingConfig, 'total_servicePaymentFee' => $total_servicePaymentFee, 'servicePaymentFee' => $servicePaymentFee, 'apartment' => $apartment]);
$html = str_replace('{{BUILDING_FEE_LIST}}', $buildingFeeList, $html);
?>
<?php
$waterFeeList = $this->render('table/water_fee_list', ['serviceBuildingConfig' => $serviceBuildingConfig, 'servicePaymentFeeWater' => $servicePaymentFeeWater, 'total_servicePaymentFeeWater' => $total_servicePaymentFeeWater, 'apartment' => $apartment]);
$html = str_replace('{{WATER_FEE_LIST}}', $waterFeeList, $html);
?>
<?php
$html = str_replace('{{TOTAL_PAYMENT}}', CUtils::formatPrice($totalPayment), $html);

//update tài khoản ngân hàng
if(!empty($announcementTemplate->buildingCluster)){
    $html = str_replace('{{BANK_NUMBER}}', $announcementTemplate->buildingCluster->bank_account, $html);
    $html = str_replace('{{BANK_NAME}}', $announcementTemplate->buildingCluster->bank_name, $html);
}

echo $html;
?>