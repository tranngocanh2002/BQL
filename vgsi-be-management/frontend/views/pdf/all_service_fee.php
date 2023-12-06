<?php
use common\helpers\CUtils;
use common\models\Apartment;
use common\models\ServiceBuildingConfig;
use common\models\ServiceParkingFee;
use common\models\ServicePaymentFee;

/* @var $apartment common\models\Apartment */

//==================================================================
/*
 * type : 0 - phí nước, 1 - phí dịch vụ, 2 - phí xe , 3 - điện, 4 - Booking, 5 - nợ cũ chuyển giao
 */
$start_current_month = strtotime(date('Y-m-01 00:00:00', time()));
$end_current_month = strtotime(date('Y-m-t 23:59:59', $start_current_month));
$start_time_building_fee = date('d/m/Y', $start_current_month);
$end_time_building_fee = date('d/m/Y', $end_current_month);
$start_time_water_fee = strtotime('-1 month', $start_current_month);
$end_time_water_fee = strtotime('-1 month', $end_current_month);
$start_time_electric_fee = strtotime('-1 month', $start_current_month);
$end_time_electric_fee = strtotime('-1 month', $end_current_month);
$start_time_parking_fee = date('d/m/Y', $start_current_month);
$end_time_parking_fee = date('d/m/Y', $end_current_month);
$start_time_booking_fee = date('d/m/Y', $start_current_month);
$end_time_booking_fee = date('d/m/Y', $end_current_month);

//==================================================================
$not_empty_building = true;
//phí dịch vụ của tháng hiện tại
$servicePaymentFees = ServicePaymentFee::find()
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['>=', 'fee_of_month', $start_current_month])
//    ->andWhere(['<=', 'fee_of_month', $end_current_month])
//    ->one();
    ->all();
$building_level_month = '';
$serviceBuildingConfig = null;
$total_building_fee = 0;
if (!empty($servicePaymentFees)) {
    foreach ($servicePaymentFees as $servicePaymentFee){
        //lấy thông tin config phí dịch vụ
        if(empty($serviceBuildingConfig)){
            $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $servicePaymentFee->building_cluster_id, 'service_map_management_id' => $servicePaymentFee->service_map_management_id]);
        }
        $Y = '';
        if(date('Y', $servicePaymentFee->fee_of_month) !== date('Y', time())){
            $Y = '/'.date('Y', $servicePaymentFee->fee_of_month);
        }
        $building_level_month .= ','.date('m', $servicePaymentFee->fee_of_month).$Y;
        $total_building_fee += $servicePaymentFee->more_money_collecte;
    }
}
//phí dịch vụ nợ cũ -> trước tháng hiện tại
$total_servicePaymentFee = ServicePaymentFee::find()
    ->select(["SUM(more_money_collecte) as more_money_collecte"])
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

if(empty($servicePaymentFee) && (int)$total_servicePaymentFee->more_money_collecte == 0){
    $not_empty_building = false;
}

//==================================================================
$not_empty_parking = true;
//phí gửi xe của tháng hiện tại, và các tháng tiếp theo
$servicePaymentFeePackings = ServicePaymentFee::find()
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_PARKING_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['>=', 'fee_of_month', $start_current_month])
//    ->andWhere(['<=', 'fee_of_month', $end_current_month])
    ->all();

$total_parking_fee = 0;
$DETAIL_FEE_PARKING = [];
foreach ($servicePaymentFeePackings as $servicePaymentFeePacking){
    $serviceParkingFee = ServiceParkingFee::findOne(['service_payment_fee_id' => $servicePaymentFeePacking->id]);
    $number = '';
    if(!empty($serviceParkingFee)){
        if(!empty($serviceParkingFee->serviceManagementVehicle)){
            $number = $serviceParkingFee->serviceManagementVehicle->number;
        }
    }else{
        Yii::warning($servicePaymentFeePacking->id);
        continue;
    }
    $total_parking_fee += $servicePaymentFeePacking->more_money_collecte;

    $Y = '';
    if(date('Y', $serviceParkingFee->fee_of_month) !== date('Y', time())){
        $Y = '/'.date('Y', $serviceParkingFee->fee_of_month);
    }
    $level_month_next = date('m', $serviceParkingFee->fee_of_month).$Y;
    if(!isset($DETAIL_FEE_PARKING[$serviceParkingFee->service_parking_level_id])){
        $DETAIL_FEE_PARKING[$serviceParkingFee->service_parking_level_id] = [
            'level_id' => $serviceParkingFee->service_parking_level_id,
            'level_name' => $serviceParkingFee->serviceParkingLevel->name,
            'level_price' => $serviceParkingFee->serviceParkingLevel->price,
            'level_number' => $number,
            'level_count' => 1,
            'level_month' => $level_month_next,
            'level_total_price' => $servicePaymentFeePacking->more_money_collecte,
        ];
    }else{
        $DETAIL_FEE_PARKING[$serviceParkingFee->service_parking_level_id]['level_count']++;
        $DETAIL_FEE_PARKING[$serviceParkingFee->service_parking_level_id]['level_month'] .= ',' . $level_month_next;
        $DETAIL_FEE_PARKING[$serviceParkingFee->service_parking_level_id]['level_total_price'] += $servicePaymentFeePacking->more_money_collecte;
    }

}

//phí xe nợ cũ -> trước tháng hiện tại
$total_servicePaymentFeePacking = ServicePaymentFee::find()
    ->select(["SUM(more_money_collecte) as more_money_collecte"])
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_PARKING_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

if(empty($servicePaymentFeePackings) && (int)$total_servicePaymentFeePacking->more_money_collecte == 0){
    $not_empty_parking = false;
}

//==================================================================
$not_empty_water = true;
//phí nước tháng hiện tại
$servicePaymentFeeWater = ServicePaymentFee::find()
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['>=', 'fee_of_month', $start_time_water_fee])
    ->andWhere(['<=', 'fee_of_month', $end_time_water_fee])
    ->one();

$total_water_fee = 0;
$json_desc = [];
if (!empty($servicePaymentFeeWater)) {
    if (!empty($servicePaymentFeeWater->json_desc)) {
        $json_desc = json_decode($servicePaymentFeeWater->json_desc, true);
    }
    $json_desc['price'] = $servicePaymentFeeWater->more_money_collecte;
    $total_water_fee += $servicePaymentFeeWater->more_money_collecte;
}

//phí nước nợ cũ
$total_servicePaymentFeeWater = ServicePaymentFee::find()
    ->select(["SUM(more_money_collecte) as more_money_collecte"])
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['<', 'fee_of_month', $start_time_water_fee])->one();

if(empty($servicePaymentFeeWater) && (int)$total_servicePaymentFeeWater->more_money_collecte == 0){
    $not_empty_water = false;
}

//==================================================================
$not_empty_electric = true;
//phí điện tháng hiện tại
$servicePaymentFeeElectric = ServicePaymentFee::find()
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_ELECTRIC_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['>=', 'fee_of_month', $start_time_electric_fee])
    ->andWhere(['<=', 'fee_of_month', $end_time_electric_fee])
    ->one();

$total_electric_fee = 0;
$json_desc_electric = [];
if (!empty($servicePaymentFeeElectric)) {
    if (!empty($servicePaymentFeeElectric->json_desc)) {
        $json_desc_electric = json_decode($servicePaymentFeeElectric->json_desc, true);
    }
    $json_desc_electric['price'] = $servicePaymentFeeElectric->more_money_collecte;
    $total_electric_fee += $servicePaymentFeeElectric->more_money_collecte;
}

//phí điện nợ cũ
$total_servicePaymentFeeElectric = ServicePaymentFee::find()
    ->select(["SUM(more_money_collecte) as more_money_collecte"])
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_ELECTRIC_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['<', 'fee_of_month', $start_time_electric_fee])->one();

if(empty($servicePaymentFeeElectric) && (int)$total_servicePaymentFeeElectric->more_money_collecte == 0){
    $not_empty_electric = false;
}

//==================================================================
$not_empty_booking = true;
//phí đặt tiện ích tháng hiện tại
$servicePaymentFeeBooking = ServicePaymentFee::find()
    ->select(["SUM(more_money_collecte) as more_money_collecte"])
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['>=', 'fee_of_month', $start_current_month])
    ->andWhere(['<=', 'fee_of_month', $end_current_month])
    ->one();

$total_booking_fee = 0;
$json_desc_booking = [];
if (!empty($servicePaymentFeeBooking)) {
    if (!empty($servicePaymentFeeBooking->json_desc)) {
        $json_desc_booking = json_decode($servicePaymentFeeBooking->json_desc, true);
    }
    $json_desc_booking['price'] = $servicePaymentFeeBooking->more_money_collecte;
    $total_booking_fee += $servicePaymentFeeBooking->more_money_collecte;
}

//phí đặt tiện ích nợ cũ
$total_servicePaymentFeeBooking = ServicePaymentFee::find()
    ->select(["SUM(more_money_collecte) as more_money_collecte"])
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

if(empty($servicePaymentFeeBooking) && (int)$total_servicePaymentFeeBooking->more_money_collecte == 0){
    $not_empty_booking = false;
}

//==================================================================
$not_empty_old_debit = true;
//phí nợ cũ trước chuyển giao
$total_servicePaymentFeeOldDebit = ServicePaymentFee::find()
    ->select(["SUM(more_money_collecte) as more_money_collecte"])
    ->where([
        'apartment_id' => $apartment->id,
        'status' => ServicePaymentFee::STATUS_UNPAID,
        'type' => ServicePaymentFee::TYPE_SERVICE_OLD_DEBIT_FEE,
        'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
        'is_debt' => ServicePaymentFee::IS_DEBT
    ])
    ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

if((int)$total_servicePaymentFeeOldDebit->more_money_collecte == 0){
    $not_empty_old_debit = false;
}
//Tổng nợ trong tháng
$total_in_month = $total_building_fee + $total_parking_fee + $total_water_fee + $total_electric_fee + $total_booking_fee;

//Tổng nợ cũ
$total_no_cu = (int)$total_servicePaymentFee->more_money_collecte
    + (int)$total_servicePaymentFeePacking->more_money_collecte
    + (int)$total_servicePaymentFeeWater->more_money_collecte
    + (int)$total_servicePaymentFeeElectric->more_money_collecte
    + (int)$total_servicePaymentFeeBooking->more_money_collecte
    + (int)$total_servicePaymentFeeOldDebit->more_money_collecte;

//==================================================================
//tổng nợ

$total_payment = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'is_debt' => ServicePaymentFee::IS_DEBT])->one();
$totalPayment = 0;
if (!empty($total_payment)) {
    $totalPayment = (int)$total_payment->more_money_collecte;
}
?>
<style type="text/css">
    body{
        font-size: 16pt;
        font-family: "Times New Roman";
        font-weight: 700;
    }

    .lst-kix_list_2-7 > li:before {
        content: "o  "
    }
    .lst-kix_list_2-4 > li:before {
        content: "o  "
    }

    .lst-kix_list_1-1 > li {
        counter-increment: lst-ctn-kix_list_1-1
    }

    .lst-kix_list_1-7 > li {
        counter-increment: lst-ctn-kix_list_1-7
    }

    .lst-kix_list_1-2 > li {
        counter-increment: lst-ctn-kix_list_1-2
    }

    .lst-kix_list_1-5 > li {
        counter-increment: lst-ctn-kix_list_1-5
    }

    .lst-kix_list_1-8 > li {
        counter-increment: lst-ctn-kix_list_1-8
    }

    .lst-kix_list_1-4 > li {
        counter-increment: lst-ctn-kix_list_1-4
    }

    .lst-kix_list_1-0 > li:before {
        content: "" counter(lst-ctn-kix_list_1-0, upper-latin) ". "
    }

    ul.lst-kix_list_2-0 {
        list-style-type: none
    }

    .lst-kix_list_1-1 > li:before {
        content: "" counter(lst-ctn-kix_list_1-1, lower-latin) ". "
    }

    .lst-kix_list_1-2 > li:before {
        content: "" counter(lst-ctn-kix_list_1-2, lower-roman) ". "
    }

    .lst-kix_list_1-3 > li:before {
        content: "" counter(lst-ctn-kix_list_1-3, decimal) ". "
    }

    .lst-kix_list_1-4 > li:before {
        content: "" counter(lst-ctn-kix_list_1-4, lower-latin) ". "
    }

    .lst-kix_list_1-0 > li {
        counter-increment: lst-ctn-kix_list_1-0
    }

    .lst-kix_list_1-6 > li {
        counter-increment: lst-ctn-kix_list_1-6
    }

    .lst-kix_list_1-7 > li:before {
        content: "" counter(lst-ctn-kix_list_1-7, lower-latin) ". "
    }

    .lst-kix_list_1-3 > li {
        counter-increment: lst-ctn-kix_list_1-3
    }

    .lst-kix_list_1-5 > li:before {
        content: "" counter(lst-ctn-kix_list_1-5, lower-roman) ". "
    }

    .lst-kix_list_1-6 > li:before {
        content: "" counter(lst-ctn-kix_list_1-6, decimal) ". "
    }

    .lst-kix_list_2-1 > li:before {
        content: "o  "
    }

    .lst-kix_list_1-8 > li:before {
        content: "" counter(lst-ctn-kix_list_1-8, lower-roman) ". "
    }

    ol {
        margin: 0;
        padding: 0
    }

    table td, table th {
        padding: 0
    }

    .c4 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 51.5pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c15 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 72pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c19 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 494.8pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c18 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 52.9pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c20 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 63.3pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c8 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 72.8pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c27 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 422.8pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c22 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 54.8pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c3 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 26.9pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c7 {
        border-right-style: solid;
        padding: 5pt 5.4pt 5pt 5.4pt;
        border-bottom-color: #000000;
        border-top-width: 1pt;
        border-right-width: 1pt;
        border-left-color: #000000;
        vertical-align: top;
        border-right-color: #000000;
        border-left-width: 1pt;
        border-top-style: solid;
        border-left-style: solid;
        border-bottom-width: 1pt;
        width: 100.7pt;
        border-top-color: #000000;
        border-bottom-style: solid
    }

    .c23 {
        margin-left: 36pt;
        padding-top: 0pt;
        padding-left: 0pt;
        padding-bottom: 8pt;
        line-height: 1.0791666666666666;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    .c11 {
        color: #000000;
        font-weight: 700;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Calibri";
        font-style: normal
    }

    .c0 {
        color: #ff0000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Calibri";
        font-style: normal
    }

    .c1 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Calibri";
        font-style: normal
    }

    .c10 {
        color: #000000;
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Calibri";
        font-style: italic
    }

    .c30 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        orphans: 2;
        widows: 2;
        text-align: right
    }

    .c2 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    .c33 {
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Arial";
        font-style: normal
    }

    .c25 {
        padding-top: 0pt;
        padding-bottom: 8pt;
        line-height: 1.0791666666666666;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    .c5 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.0;
        orphans: 2;
        widows: 2;
        text-align: center
    }

    .c32 {
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Calibri";
        font-style: normal
    }

    .c31 {
        font-weight: 400;
        text-decoration: none;
        font-size: 11pt;
        font-family: "Calibri";
        font-style: normal
    }

    .c28 {
        font-weight: 400;
        text-decoration: none;
        vertical-align: baseline;
        font-size: 11pt;
        font-family: "Calibri"
    }

    .c24 {
        border-spacing: 0;
        border-collapse: collapse;
        width: 100%;
    }

    .c9 {
        padding-top: 0pt;
        padding-bottom: 0pt;
        line-height: 1.15;
        text-align: left
    }

    .c29 {
        background-color: #ffffff;
    }

    .c13 {
        padding: 0;
        margin: 0;
        list-style-type:none
    }

    .c26 {
        color: #000000
    }

    .c16 {
        height: 0pt
    }

    .c6 {
        height: 11pt
    }

    .c17 {
        vertical-align: super
    }

    .c14 {
        color: #ff0000
    }

    .c12 {
        font-style: italic
    }

    .c21 {
        font-weight: 700
    }

    .title {
        padding-top: 24pt;
        color: #000000;
        font-weight: 700;
        font-size: 36pt;
        padding-bottom: 6pt;
        font-family: "Calibri";
        line-height: 1.0791666666666666;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    .subtitle {
        padding-top: 18pt;
        color: #666666;
        font-size: 24pt;
        padding-bottom: 4pt;
        font-family: "Georgia";
        line-height: 1.0791666666666666;
        page-break-after: avoid;
        font-style: italic;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    li {
        color: #000000;
        font-size: 11pt;
        font-family: "Calibri"
    }

    p {
        margin: 0;
        color: #000000;
        font-size: 11pt;
        font-family: "Calibri"
    }

    h1 {
        padding-top: 24pt;
        color: #000000;
        font-weight: 700;
        font-size: 24pt;
        padding-bottom: 6pt;
        font-family: "Calibri";
        line-height: 1.0791666666666666;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h2 {
        padding-top: 18pt;
        color: #000000;
        font-weight: 700;
        font-size: 18pt;
        padding-bottom: 4pt;
        font-family: "Calibri";
        line-height: 1.0791666666666666;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h3 {
        padding-top: 14pt;
        color: #000000;
        font-weight: 700;
        font-size: 14pt;
        padding-bottom: 4pt;
        font-family: "Calibri";
        line-height: 1.0791666666666666;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h4 {
        padding-top: 12pt;
        color: #000000;
        font-weight: 700;
        font-size: 12pt;
        padding-bottom: 2pt;
        font-family: "Calibri";
        line-height: 1.0791666666666666;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h5 {
        padding-top: 11pt;
        color: #000000;
        font-weight: 700;
        font-size: 11pt;
        padding-bottom: 2pt;
        font-family: "Calibri";
        line-height: 1.0791666666666666;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }

    h6 {
        padding-top: 10pt;
        color: #000000;
        font-weight: 700;
        font-size: 10pt;
        padding-bottom: 2pt;
        font-family: "Calibri";
        line-height: 1.0791666666666666;
        page-break-after: avoid;
        orphans: 2;
        widows: 2;
        text-align: left
    }
    .header_main{
        padding: 10px;
        width: 100%;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
    }
</style>
<div class="c29">
    <div class="header_main">Danh sách phí căn hộ: <span style="font-size: 20px; color:red"><?= $apartment->name.'/'.trim($apartment->parent_path, '/') ?></span></div>
    <table class="c24">
        <tbody>
        <tr class="c16">
            <td class="c3" colspan="1" rowspan="1">
                <p class="c2"><span class="c11">STT</span></p>
                <p class="c2"><span class="c10">No.</span></p>
            </td>
            <td class="c7" colspan="1" rowspan="1">
                <p class="c2"><span class="c11">Chi tiết</span></p>
                <p class="c2"><span class="c10">Description </span></p>
            </td>
            <td class="c4" colspan="1" rowspan="1">
                <p class="c2"><span class="c11">CS cũ </span></p>
                <p class="c2"><span class="c10">Old Index </span></p>
            </td>
            <td class="c22" colspan="1" rowspan="1">
                <p class="c2"><span class="c11">CS mới</span></p>
                <p class="c2"><span class="c10">New Index</span></p>
            </td>
            <td class="c8" colspan="1" rowspan="1">
                <p class="c2"><span class="c11">Khối lượng </span></p>
                <p class="c2"><span class="c10">Consumed Quantity </span></p>
            </td>
            <td class="c18" colspan="1" rowspan="1">
                <p class="c2"><span class="c11">ĐVT</span></p>
                <p class="c2"><span class="c10">Unit</span></p>
            </td>
            <td class="c20" colspan="1" rowspan="1">
                <p class="c2"><span class="c11">Đơn giá (VNĐ)</span></p>
                <p class="c2"><span class="c10">Unit price</span></p>
            </td>
            <td class="c15" colspan="1" rowspan="1">
                <p class="c2"><span class="c11">Thành tiền (VNĐ) </span></p>
                <p class="c2"><span class="c10">Amount </span></p>
            </td>
        </tr>
        <tr class="c16">
            <td class="c19" colspan="8" rowspan="1">
                <ol class="c13">
                    <li class="c23">
                        <span class="c11">Phí dịch vụ phát sinh trong tháng </span>
                        <span class="c14 c21 c32"><?= date('m/Y', $start_current_month) ?></span>
                        <span class="c0">&nbsp;</span>
                        <span class="c10">The service fees arising in </span>
                        <span class="c14 c12 c28"><?= date('m/Y', $start_current_month) ?></span>
                    </li>
                </ol>
            </td>
        </tr>
        <?php $next_index = 1; ?>
        <?php if(!empty($total_building_fee)){ ?>
            <tr class="c16">
                <td class="c3" colspan="1" rowspan="1">
                    <p class="c2"><span class="c11"><?= $next_index++ ?></span></p>
                </td>
                <td class="c7" colspan="1" rowspan="1">
                    <p class="c2">
                        <span class="c21">Phí quản lý</span><span>/ </span>
                        <span class="c12">Management fee</span><br>
                        <span style="color: red"><?= 'Tháng: '. trim($building_level_month,',') ?></span>
                    </p>
                </td>
                <td class="c4" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c22" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c8" colspan="1" rowspan="1"><p class="c2"><span class="c14"><?= $apartment->capacity ?></span></p></td>
                <td class="c18" colspan="1" rowspan="1"><p class="c5"><span>m</span><span class="c26 c17 c31">2</span></p></td>
                <td class="c20" colspan="1" rowspan="1">
                    <p class="c2">
                        <span class="c14"><?php if($serviceBuildingConfig){ echo CUtils::formatPrice($serviceBuildingConfig->price); } ?></span>
                    </p>
                </td>
                <td class="c15" colspan="1" rowspan="1">
                    <p class="c2">
                        <span class="c14"><?= CUtils::formatPrice($total_building_fee) ?></span>
                    </p>
                </td>
            </tr>
        <?php } ?>
        <?php if(!empty($total_parking_fee)){ ?>
            <tr class="c16">
                <td class="c3" colspan="1" rowspan="1"><p class="c2"><span class="c11"><?= $next_index++ ?></span></p></td>
                <td class="c7" colspan="1" rowspan="1">
                    <p class="c2">
                        <span class="c21">Gửi xe /</span>
                        <span class="c12">parking charge</span>
                        <span class="c11">&nbsp;</span>
                    </p>
                </td>
                <td class="c4" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c22" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c8" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c18" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c20" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c15" colspan="1" rowspan="1"><p class="c2"><span class="c14"><?= CUtils::formatPrice($total_parking_fee) ?></span></p></td>
            </tr>
            <?php foreach ($DETAIL_FEE_PARKING as $FEE_PARKING){ ?>
                <tr class="c16">
                    <td class="c3" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                    <td class="c7" colspan="1" rowspan="1"><p class="c2"><span class="c0">* <?= $FEE_PARKING['level_name'] .': '.$FEE_PARKING['level_number'] .'<br> Tháng: '.$FEE_PARKING['level_month'] ?></span></p></td>
                    <td class="c4" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                    <td class="c22" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                    <td class="c8" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= $FEE_PARKING['level_count'] ?></span></p></td>
                    <!--                    <td class="c18" colspan="1" rowspan="1"><p class="c5"><span class="c1">chiếc/pcs</span></p></td>-->
                    <td class="c18" colspan="1" rowspan="1"><p class="c5"><span class="c1">Tháng</span></p></td>
                    <td class="c20" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= CUtils::formatPrice($FEE_PARKING['level_price']) ?></span> </p></td>
                    <td class="c15" colspan="1" rowspan="1"><p class="c2"><span class="c14"><?= CUtils::formatPrice($FEE_PARKING['level_total_price']) ?></span> </p></td>
                </tr>
            <?php } ?>
        <?php } ?>
        <?php if(!empty($total_water_fee)){ ?>
            <tr class="c16">
                <td class="c3" colspan="1" rowspan="1"><p class="c2"><span class="c1"><?= $next_index++ ?></span></p></td>
                <td class="c7" colspan="1" rowspan="1"><p class="c2"><span class="c26 c21">Nước/</span><span class="c26">&nbsp;</span><span class="c26 c12">Water charge</span><span class="c26">&nbsp;</span></p> </td>
                <td class="c4" colspan="1" rowspan="1">
                    <p class="c2">
                        <span class="c0"><?= (!empty($json_desc)) ? $json_desc['month']['start_index'] : 0 ?></span>
                    </p>
                </td>
                <td class="c22" colspan="1" rowspan="1">
                    <p class="c2">
                        <span class="c0"><?= (!empty($json_desc)) ? $json_desc['month']['end_index'] : 0 ?></span>
                    </p>
                </td>
                <td class="c8" colspan="1" rowspan="1">
                    <p class="c2">
                        <span class="c0"><?= (!empty($json_desc)) ? $json_desc['month']['total_index'] : 0 ?></span>
                    </p>
                </td>
                <td class="c18" colspan="1" rowspan="1"><p class="c5"><span>m</span><span class="c17">3</span></p></td>
                <td class="c20" colspan="1" rowspan="1"><p class="c2 c6"><span class="c0"></span></p></td>
                <td class="c15" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= CUtils::formatPrice($total_water_fee) ?></span></p></td>
            </tr>
            <?php if(isset($json_desc['dm_arr'])){ foreach ($json_desc['dm_arr'] as $dm){ ?>
                <tr class="c16">
                    <td class="c3" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                    <td class="c7" colspan="1" rowspan="1"><p class="c2"><span class="c0">* <?= $dm['text'] ?></span></p></td>
                    <td class="c4" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                    <td class="c22" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                    <td class="c8" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= $dm['index'] ?> </span></p></td>
                    <td class="c18" colspan="1" rowspan="1"><p class="c5"><span>m</span><span class="c17">3</span></p></td>
                    <td class="c20" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= $dm['price'] ?></span></p></td>
                    <td class="c15" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= $dm['total_price'] ?></span></p></td>
                </tr>
            <?php }} ?>
        <?php } ?>
        <?php if(!empty($total_electric_fee)){ ?>
            <tr class="c16">
                <td class="c3" colspan="1" rowspan="1"><p class="c2"><span class="c1"><?= $next_index++ ?></span></p></td>
                <td class="c7" colspan="1" rowspan="1"><p class="c2"><span class="c26 c21">Điện sinh hoạt/ </span><span class="c26 c12">Electricity charge</span></p></td>
                <td class="c4" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= (!empty($json_desc_electric)) ? $json_desc_electric['month']['start_index'] : 0 ?> </span></p></td>
                <td class="c22" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= (!empty($json_desc_electric)) ? $json_desc_electric['month']['end_index'] : 0 ?></span></p></td>
                <td class="c8" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= (!empty($json_desc_electric)) ? $json_desc_electric['month']['total_index'] : 0 ?></span></p></td>
                <td class="c18" colspan="1" rowspan="1"><p class="c5"><span class="c1">Số</span></p></td>
                <td class="c20" colspan="1" rowspan="1"><p class="c2 c6"><span class="c0"></span></p></td>
                <td class="c15" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= CUtils::formatPrice($total_electric_fee) ?></span></p></td>
            </tr>
            <?php if(isset($json_desc_electric['dm_arr'])){ foreach ($json_desc_electric['dm_arr'] as $dm){ ?>
                <tr class="c16">
                    <td class="c3" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                    <td class="c7" colspan="1" rowspan="1"><p class="c2"><span class="c0">* <?= $dm['text'] ?></span></p></td>
                    <td class="c4" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                    <td class="c22" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                    <td class="c8" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= $dm['index'] ?> </span></p></td>
                    <td class="c18" colspan="1" rowspan="1"><p class="c5"><span class="c1">Số</span></p></td>
                    <td class="c20" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= $dm['price'] ?></span></p></td>
                    <td class="c15" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= $dm['total_price'] ?></span></p></td>
                </tr>
            <?php }} ?>
        <?php } ?>
        <?php if(!empty($total_booking_fee)){ ?>
            <tr class="c16">
                <td class="c3" colspan="1" rowspan="1"><p class="c2"><span class="c1"><?= $next_index++ ?></span></p></td>
                <td class="c7" colspan="1" rowspan="1">
                    <p class="c2">
                        <span class="c21">Phí khác /</span><span>&nbsp;</span>
                        <span class="c12">Others</span>
                    </p>
                </td>
                <td class="c4" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c22" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c8" colspan="1" rowspan="1"><p class="c30"><span class="c0">-</span></p></td>
                <td class="c18" colspan="1" rowspan="1"><p class="c2 c6"><span class="c1"></span></p></td>
                <td class="c20" colspan="1" rowspan="1"><p class="c2 c6"><span class="c0"></span></p></td>
                <td class="c15" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= CUtils::formatPrice($total_booking_fee) ?></span></p></td>
            </tr>
        <?php } ?>
        <tr class="c16">
            <td class="c27" colspan="7" rowspan="1">
                <ul class="c13">
                    <li class="c23"><span class="c11">A. Tổng cộng</span><span class="c1">/ Subtotal: <?= CUtils::subtotalShowText($next_index++) ?> </span>
                    </li>
                </ul>
            </td>
            <td class="c15" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= CUtils::formatPrice($total_in_month) ?></span></p></td>
        </tr>
        <tr class="c16">
            <td class="c27" colspan="7" rowspan="1">
                <ol class="c13">
                    <li class="c23">
                        <span class="c11">B. Nợ cũ</span>
                        <span class="c1">/ </span>
                        <span class="c10">Unsettled Payables</span>
                        <span class="c1">&nbsp;</span>
                    </li>
                </ol>
            </td>
            <td class="c15" colspan="1" rowspan="1">
                <p class="c2"><span class="c0"><?= CUtils::formatPrice($total_no_cu) ?></span></p>
            </td>
        </tr>
        <!--        <tr class="c16">-->
        <!--            <td class="c27" colspan="7" rowspan="1">-->
        <!--                <ol class="c13">-->
        <!--                    <li class="c23">-->
        <!--                        <span class="c11">C. Đã thanh toán / </span>-->
        <!--                        <span class="c10">Paid</span>-->
        <!--                    </li>-->
        <!--                </ol>-->
        <!--            </td>-->
        <!--            <td class="c15" colspan="1" rowspan="1">-->
        <!--                <p class="c2">-->
        <!--                    <span class="c0">@Đã thanh toán cho phí A</span>-->
        <!--                </p>-->
        <!--            </td>-->
        <!--        </tr>-->
        <tr class="c16">
            <td class="c27" colspan="7" rowspan="1">
                <ul class="c13 lst-kix_list_2-0">
                    <li class="c23">
                        <span class="c11">Tổng số tiền còn phải thanh toán</span>
                        <span class="c1"> / </span><span class="c10">Total</span>
                        <span class="c1">: (A) + (B)</span>
                        <!--                        <span class="c1">: (A) + (B) - (C)</span>-->
                    </li>
                </ul>
            </td>
            <td class="c15" colspan="1" rowspan="1"><p class="c2"><span class="c0"><?= CUtils::formatPrice($totalPayment) ?></span>
                </p></td>
        </tr>
        </tbody>
    </table>
</div>