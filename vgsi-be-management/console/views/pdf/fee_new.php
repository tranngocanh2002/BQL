<?php
/* @var $this yii\web\View */
/* @var $serviceBill common\models\ServiceBill */
/* @var $serviceBillItems common\models\ServiceBillItem */

/* @var $apartment common\models\Apartment */

use common\helpers\CUtils;
use yii\helpers\Html;
use common\models\ServicePaymentFee;
use common\models\ServiceBuildingConfig;
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

$total_price_all = 0;
?>
<div style="width: 675px">
    <div style="height: 65px">
        <div style="width: 330px;float: left">
            <p style="margin-top: 3.2pt; margin-left: 120.4pt; margin-bottom: 0pt; text-indent: -27pt; line-height: 107%; widows: 0; orphans: 0; font-size: 13pt;"><span style="font-family: 'Times New Roman'; color: #4f81bc;">THÔNG BÁO THU PHÍ THÁNG <?= 'T' . date('m/Y') ?> EXPENSE NOTICE <?= date('m Y') ?></span></p>
        </div>
        <div style="float: right">
            <p style="margin-top: 5.1pt; margin-left: 57.75pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 11pt;"><br style="clear: both; mso-column-break-before: always;"><span style="font-family: 'Times New Roman';">Số: <?= $apartment->name ?>_<?= 'T' . date('m/Y') ?>_TBP</span></p>
            <p style="margin-top: 3.3pt; margin-left: 104.2pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 11pt;"><span style="font-family: 'Times New Roman';">Ngày:</span><span style="font-family: 'Times New Roman'; letter-spacing: -0.05pt;">&nbsp;</span><span style="font-family: 'Times New Roman';"><?= date('d/m/Y') ?></span></p>
        </div>
    </div>
    <div>
        <p style="margin-top: 0.4pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 14pt;"><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
        <p style="margin-top: 4.95pt; margin-left: 7.55pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Tên Khách hàng/ Client's Name: <?= $apartment->resident_user_name ?></span></strong></p>
        <p style="margin-top: 6.75pt; margin-left: 7.55pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Mã căn hộ :</span></strong><strong><span style="font-family: 'Times New Roman';">&nbsp;&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: 1.15pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';"><?= $apartment->name ?></span></strong><span style="width: 308.45pt; display: inline-block;">&nbsp;</span><span style="font-family: 'Times New Roman';">STT</span><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span><span style="font-family: 'Times New Roman';">:</span><span style="width: 82.6pt; display: inline-block;">&nbsp;</span><span style="font-family: 'Times New Roman'; background-color: #ebf0de;">103</span></p>
        <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 9pt;"><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
        <p style="margin-top: 0.3pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 10.5pt;"><span style="font-family: 'Times New Roman';">&nbsp;</span></p>

        <?php if(!empty($servicePaymentFee) || !empty($total_servicePaymentFee)){ ?>
            <ol style="margin: 0pt; padding-left: 0pt;" type="I">
                <li style="margin-left: 14.9pt; widows: 0; orphans: 0; font-family: 'Times New Roman'; font-size: 8pt; font-weight: bold; letter-spacing: -0.05pt;"><span style="letter-spacing: normal;">Phí quản lý/</span><span style="letter-spacing: -1.15pt;">&nbsp;</span><span style="letter-spacing: normal;">Management</span><span style="letter-spacing: -0.35pt;">&nbsp;</span><span style="letter-spacing: normal;">Cost</span><span style="width: 155.26pt; display: inline-block;">&nbsp;</span><span style="letter-spacing: normal;">Từ/from</span><span style="letter-spacing: -0.6pt;">&nbsp;</span><span style="letter-spacing: normal;">01/07/2019</span><span style="letter-spacing: -0.55pt;">&nbsp;</span><span style="letter-spacing: normal;">đến/to</span><span style="letter-spacing: -0.5pt;">&nbsp;</span><span style="letter-spacing: normal;">31/07/2019</span></li>
            </ol>
            <p style="margin-top: 0.4pt; margin-bottom: 0.05pt; widows: 0; orphans: 0; font-size: 7.5pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
            <table style="margin-left: 5.65pt; border: 1pt solid #000000; border-collapse: collapse;" cellspacing="0" cellpadding="0">
                <tbody>
                <tr style="height: 29.7pt;">
                    <td style="width: 49.4pt; border-right-style: solid; border-right-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0.55pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8.5pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin: 0pt 8.65pt 0pt 10.25pt; text-align: center; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">STT/No</span></strong></p>
                    </td>
                    <td style="width: 75.45pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 5.15pt; margin-left: 30.3pt; margin-bottom: 0pt; text-indent: -20.8pt; line-height: 115%; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Diện tích / Total area</span></strong></p>
                    </td>
                    <td style="width: 73.85pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0.2pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 9.5pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin-top: 0.05pt; margin-left: 17.85pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Đơn giá/<?php if(!empty($serviceBuildingConfig) && $serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_APARTMENT){ echo 'Căn hộ</span></strong>';}else{ echo 'm</span></strong>2';} ?></p>
                    </td>
                    <td style="width: 58.25pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0.2pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 9.5pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin-top: 0.05pt; margin-left: 6pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Unit Price/<?php if(!empty($serviceBuildingConfig) && $serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_APARTMENT){ echo 'Apartment</span></strong>';}else{ echo 'm</span></strong>2';} ?></p>
                    </td>
                    <td style="width: 87.15pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                        <p style="margin: 4.7pt 0.8pt 0pt 33.75pt; text-indent: -29.4pt; line-height: 115%; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Thành tiền/Amount - ( VND)</span></strong></p>
                    </td>
                    <td style="width: 56.1pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                        <p style="margin: 5.15pt 0.2pt 0pt 14.05pt; text-indent: -11.2pt; line-height: 115%; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">CÁC KHOẢN NỢ PHÍ</span></strong></p>
                    </td>
                    <td style="width: 93.75pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0.55pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8.5pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin-top: 0pt; margin-left: 24.25pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Ghi chú/Note</span></strong></p>
                    </td>
                </tr>
                <?php $total_building_fee = 0; if(!empty($servicePaymentFee)){ $total_building_fee += $servicePaymentFee->price; ?>
                    <tr style="height: 14.95pt;">
                        <td style="width: 49.4pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                            <p style="margin-top: 4.7pt; margin-left: 1.5pt; margin-bottom: 0pt; text-align: center; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">1</span></p>
                        </td>
                        <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                            <p style="margin-top: 4.7pt; margin-left: 48.95pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?= $apartment->capacity ?></span></p>
                        </td>
                        <td style="width: 133.1pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                            <p style="margin: 4.7pt 61.6pt 0pt 46.35pt; text-align: center; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice($serviceBuildingConfig->price) ?></span></p>
                        </td>
                        <td style="width: 87.15pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                            <p style="margin-top: 4.7pt; margin-left: 50.35pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice($servicePaymentFee->price) ?></span></p>
                        </td>
                        <td style="width: 56.1pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                            <p style="margin-top: 4.7pt; margin-right: 11.55pt; margin-bottom: 0pt; text-align: right; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">-</span></p>
                        </td>
                        <td style="width: 93.75pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                            <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
                        </td>
                    </tr>
                <?php } ?>
                <?php if(!empty($total_servicePaymentFee)){ $total_building_fee += (int)$total_servicePaymentFee->more_money_collecte; ?>
                    <tr style="height: 14.95pt;">
                        <td style="width: 125.85pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; vertical-align: top;" colspan="2">
                            <p style="margin-top: 4.7pt; margin-left: 18.05pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Nợ cũ</span></strong></p>
                        </td>
                        <td style="width: 278.35pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; vertical-align: top;" colspan="4">
                            <p style="margin-top: 4.7pt; margin-left: 154.8pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice((int)$total_servicePaymentFee->more_money_collecte) ?></span></strong></p>
                        </td>
                        <td style="width: 93.75pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; vertical-align: top;">
                            <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
                        </td>
                    </tr>
                <?php } ?>
                <tr style="height: 14.95pt;">
                    <td style="width: 125.85pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; vertical-align: top;" colspan="2">
                        <p style="margin-top: 4.7pt; margin-left: 18.05pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Tổng cộng/Total payment</span></strong></p>
                    </td>
                    <td style="width: 278.35pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; vertical-align: top;" colspan="4">
                        <p style="margin-top: 4.7pt; margin-left: 154.8pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice($total_building_fee) ?></span></strong></p>
                    </td>
                    <td style="width: 93.75pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
                    </td>
                </tr>
                </tbody>
            </table>
        <?php $total_price_all += $total_building_fee; } ?>

        <?php $total_water_fee = 0; if(!empty($servicePaymentFeeWater) || !empty($total_servicePaymentFeeWater)){
        $json_desc = [];
        if(!empty($servicePaymentFeeWater)){
            if(!empty($servicePaymentFeeWater->json_desc)){
                $json_desc = json_decode($servicePaymentFeeWater->json_desc, true);
            }
            $json_desc['price'] = $servicePaymentFeeWater->more_money_collecte;
            $total_water_fee += $servicePaymentFeeWater->more_money_collecte;
        }
        ?>
            <ol style="margin: 0pt; padding-left: 0pt;" start="2" type="I">
                <li style="margin-top: 7.25pt; margin-left: 22.05pt; widows: 0; orphans: 0; font-family: 'Times New Roman'; font-size: 11pt; font-weight: bold; letter-spacing: -0.05pt;"><span style="letter-spacing: normal;">Tiền nước/</span><span style="letter-spacing: 0.1pt;">&nbsp;</span><span style="letter-spacing: normal;">Water</span><span style="letter-spacing: 0.05pt;">&nbsp;</span><span style="letter-spacing: normal;">cost:</span><span style="width: 152.39pt; display: inline-block;">&nbsp;</span><span style="font-size: 8pt; letter-spacing: normal;">Từ/from</span><span style="font-size: 8pt; letter-spacing: normal;">&nbsp;&nbsp;</span><span style="font-size: 8pt; letter-spacing: normal;">25/06/2019</span><span style="font-size: 8pt; letter-spacing: normal;">&nbsp;&nbsp;</span><span style="font-size: 8pt; letter-spacing: normal;">đến/to</span><span style="font-size: 8pt; letter-spacing: 0.8pt;">&nbsp;</span><span style="font-size: 8pt; letter-spacing: normal;">25/07/2019</span></li>
            </ol>
            <p style="margin-top: 0.2pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 5.5pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
            <table style="margin-left: 5.65pt; border-collapse: collapse;" cellspacing="0" cellpadding="0">
                <tbody>
                <tr style="height: 29.7pt;">
                    <td style="width: 49.4pt; border-style: solid; border-width: 1pt; vertical-align: top;" rowspan="2">
                        <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 9pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin-top: 7.55pt; margin-left: 6.2pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">1.Phí nước</span></strong></p>
                    </td>
                    <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin: 5.15pt 0.35pt 0pt 21.55pt; text-indent: -18.4pt; line-height: 115%; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Chỉ số cũ / Previous Indicator</span></strong></p>
                    </td>
                    <td style="width: 73.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 5.15pt; margin-left: 5.25pt; margin-bottom: 0pt; text-indent: 11pt; line-height: 115%; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Chỉ số mới / Current Indicator</span></strong></p>
                    </td>
                    <td style="width: 203.65pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                        <p style="margin-top: 0.05pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8.5pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin-top: 0pt; margin-left: 51.25pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Tiêu thụ / Consumption (m3)</span></strong></p>
                    </td>
                    <td style="width: 93.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0.55pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8.5pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin-top: 0pt; margin-left: 24.1pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Ghi chú/Note</span></strong></p>
                    </td>
                </tr>
                <tr style="height: 14.95pt;">
                    <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 3.7pt; margin-right: 3.3pt; margin-bottom: 0pt; text-align: right; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo $json_desc['month']['start_index']; } ?></span></p>
                    </td>
                    <td style="width: 73.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 3.7pt; margin-right: 3.3pt; margin-bottom: 0pt; text-align: right; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo $json_desc['month']['end_index']; } ?></span></p>
                    </td>
                    <td style="width: 203.65pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                        <p style="margin-top: 2.75pt; margin-left: 6.5pt; margin-bottom: 0pt; text-align: center; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo $json_desc['month']['total_index']; } ?></span></p>
                    </td>
                    <td style="width: 93.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
                    </td>
                </tr>
                <tr style="height: 40.4pt;">
                    <td style="width: 49.4pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 9pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin-top: 5.35pt; margin-left: 11.5pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">STT/No</span></strong></p>
                    </td>
                    <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 5.15pt; margin-left: 5pt; margin-bottom: 0pt; text-indent: 4.9pt; line-height: 115%; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Tiêu thụ thực tế Consumption (m3)</span></strong></p>
                    </td>
                    <td style="width: 73.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 5.15pt; margin-left: 21.45pt; margin-bottom: 0pt; text-indent: -4.7pt; line-height: 115%; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Đơn giá/m3 Price/m3</span></strong></p>
                    </td>
                    <td style="width: 116.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0.3pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 13pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin-top: 0pt; margin-left: 9.1pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Thành tiền / Amount – VND</span></strong></p>
                    </td>
                    <td style="width: 180.65pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                        <p style="margin-top: 0.3pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 13pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
                        <p style="margin: 0pt 64.55pt 0pt 66.2pt; text-align: center; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Ghi chú/Note</span></strong></p>
                    </td>
                </tr>
                <tr style="height: 14.95pt;">
                    <td style="width: 49.4pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 4.7pt; margin-right: 0.65pt; margin-bottom: 0pt; text-align: right; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">1</span></p>
                    </td>
                    <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 4.7pt; margin-right: 3.4pt; margin-bottom: 0pt; text-align: right; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo $json_desc['month']['total_index']; } ?></span></p>
                    </td>
                    <td style="width: 73.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 4.7pt; margin-right: 3.4pt; margin-bottom: 0pt; text-align: right; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">Định mức</span></p>
                    </td>
                    <td style="width: 116.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 4.7pt; margin-right: 3.4pt; margin-bottom: 0pt; text-align: right; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo CUtils::formatPrice($json_desc['price']); } ?></span></p>
                    </td>
                    <td style="width: 180.65pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                        <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo implode("<br>", $json_desc['dm']); } ?></span></p>
                    </td>
                </tr>
                <?php if(!empty($total_servicePaymentFeeWater)){ $total_water_fee += (int)$total_servicePaymentFeeWater->more_money_collecte; ?>
                    <tr style="height: 14.95pt;">
                        <td style="width: 125.85pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                            <p style="margin: 4.7pt 49.1pt 0pt 50.7pt; text-align: center; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Nợ cũ:</span></strong></p>
                        </td>
                        <td style="width: 278.5pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="3">
                            <p style="margin-top: 2.3pt; margin-left: 78.5pt; margin-bottom: 0pt; text-align: center; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice((int)$total_servicePaymentFeeWater->more_money_collecte) ?></span></p>
                        </td>
                        <td style="width: 93.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                            <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
                        </td>
                    </tr>
                <?php } ?>
                <tr style="height: 14.95pt;">
                    <td style="width: 125.85pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                        <p style="margin-top: 2.5pt; margin-left: 18.05pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Tổng cộng/Total payment</span></strong></p>
                    </td>
                    <td style="width: 278.5pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="3">
                        <p style="margin-top: 2.5pt; margin-left: 165.25pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice($total_water_fee) ?></span></strong></p>
                    </td>
                    <td style="width: 93.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                        <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
                    </td>
                </tr>
                <tr style="height: 0pt;">
                    <td style="width: 50.4pt;">&nbsp;</td>
                    <td style="width: 76.45pt;">&nbsp;</td>
                    <td style="width: 74.85pt;">&nbsp;</td>
                    <td style="width: 117.85pt;">&nbsp;</td>
                    <td style="width: 86.8pt;">&nbsp;</td>
                    <td style="width: 94.85pt;">&nbsp;</td>
                </tr>
                </tbody>
            </table>
        <?php $total_price_all += $total_water_fee;} ?>
        <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 10pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
        <p style="margin-top: 0pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 10pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
        <p style="margin-top: 0.45pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 11.5pt;"><strong><span style="font-family: 'Times New Roman';">&nbsp;</span></strong></p>
        <ol style="margin: 0pt; padding-left: 0pt;" start="3" type="I">
            <li style="margin-left: 21.35pt; widows: 0; orphans: 0; font-family: 'Times New Roman'; font-size: 8pt; font-weight: bold; letter-spacing: -0.05pt;">
                <span style="letter-spacing: normal;">Số thanh toán/</span><span style="letter-spacing: -0.35pt;">&nbsp;</span><span style="letter-spacing: normal;">Payment</span>
            </li>
        </ol>
        <p style="margin: 6.8pt 54.25pt 0pt 78.7pt; text-align: center; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">Tổng thanh toán /Total payment : <?= CUtils::formatPrice($total_price_all) ?></span></strong></p>
        <p style="margin-top: 6.75pt; margin-left: 7.55pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">Hạn thanh toán / Payment due date : 10/08/2019 (AUGUST 10st 08).</span></p>
        <p style="margin-top: 6.75pt; margin-left: 7.55pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">Xin vui lòng thanh toán số tiền trên cho Ban Quản lý tại sảnh T1 hoặc chuyển vào tài khoản công ty.</span></p>
        <p style="margin-top: 6.75pt; margin-left: 7.55pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 8pt;"><span style="font-family: 'Times New Roman';">You are kindly requested to finalize the above payment for Management Unit or transfer your payment to our active bank account.</span></p>
        <p style="margin-top: 5.25pt; margin-left: 7.7pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 9pt;"><span style="font-family: 'Times New Roman';">Tên người nhận/At :&nbsp;</span><strong><span style="font-family: 'Times New Roman';">CÔNG TY TNHH TM DV PJK ONE</span></strong></p>
        <p style="margin-top: 6.2pt; margin-left: 7.7pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 9pt;"><span style="font-family: 'Times New Roman';">Số tài khoản / Account No. : 10220583560015 – Tại Ngân Hàng: Techcombank Chi Nhánh Phú Mỹ Hưng – TP.HCM</span></p>
        <p style="margin: 5.6pt 22.8pt 0pt 7.7pt; line-height: 154%; widows: 0; orphans: 0; font-size: 9pt;"><strong><span style="font-family: 'Times New Roman';">Vui</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">lòng</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.4pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">ghi</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">đầy</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.45pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">đủ</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.5pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">khi</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.5pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">nộp</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.5pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">tiền</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.5pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">qua</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.45pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">tài</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">khoản</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.5pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">với</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">nội</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">dung:</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.5pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">Mã</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.45pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">căn:</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.5pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">……nộp</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.45pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">Phí</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">QL</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">tháng</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.4pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">…….</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">+</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.45pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">Phí</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">nước</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.55pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">tháng...</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.5pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">) Ghi chú /</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.45pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">Note:</span></strong></p>
        <p style="margin-top: 0.05pt; margin-left: 19.2pt; margin-bottom: 0pt; text-indent: -6.95pt; widows: 0; orphans: 0; font-size: 9pt;"><em><span style="font-family: 'Times New Roman';">*</span></em><span style="font: 7pt 'Times New Roman';">&nbsp;&nbsp;</span><em><span style="font-family: 'Times New Roman';">Nếu</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: 1.8pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">sau</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">ngày</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.3pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">16</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">của</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">tháng</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.3pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">mà</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">Quý</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.3pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">Cư</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">Dân</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">vẫn</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">chưa</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">thanh</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">toán</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">tiền</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">thì</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.35pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">BQL</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">sẽ</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.3pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">tạm</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">ngưng</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">cung</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">cấp</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">dịch</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">vụ</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">tiện</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">ích.</span></em></p>
        <p style="margin-top: 5.05pt; margin-left: 16.9pt; margin-bottom: 0pt; text-indent: -6.95pt; widows: 0; orphans: 0; font-size: 9pt;"><em><span style="font-family: 'Times New Roman';">*</span></em><span style="font: 7pt 'Times New Roman';">&nbsp;&nbsp;</span><em><span style="font-family: 'Times New Roman';">The</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.2pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">Building</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.15pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">managerment</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.2pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">will</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">cut</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">service</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.2pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">the</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.2pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">nonpayment</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">Apartment</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.2pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">after</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.2pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">16</span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.45pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman'; font-size: 6pt;"><sup>th</sup></span></em><em><span style="font-family: 'Times New Roman'; letter-spacing: -0.85pt;">&nbsp;</span></em><em><span style="font-family: 'Times New Roman';">.</span></em></p>
        <p style="margin-top: 0.3pt; margin-bottom: 0pt; widows: 0; orphans: 0; font-size: 16.5pt;"><em><span style="font-family: 'Times New Roman';">&nbsp;</span></em></p>
        <p style="margin-top: 0pt; margin-right: 54.25pt; margin-bottom: 0pt; text-align: center; widows: 0; orphans: 0; font-size: 8pt;">
            <strong><span style="font-family: 'Times New Roman';">BAN QUẢN LÝ</span></strong><strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.8pt;">&nbsp;</span></strong><strong><span style="font-family: 'Times New Roman';">TÒA</span></strong>
            <strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.25pt;">&nbsp;</span></strong>
            <strong><span style="font-family: 'Times New Roman';">NHÀ</span></strong><span style="width: 196.33pt; display: inline-block;">&nbsp;</span>
            <strong><span style="font-family: 'Times New Roman';">NGƯỜI</span></strong>
            <strong><span style="font-family: 'Times New Roman'; letter-spacing: -0.1pt;">&nbsp;</span></strong>
            <strong><span style="font-family: 'Times New Roman';">LẬP</span></strong>
            <img src="https://myfiles.space/user_files/35115_50076e3403dd91bd/1566660913_thong-bao-phi-1/1566660913_thong-bao-phi-1.002.png" alt="" width="657" height="92"> <br>
        </p>
        <p style="margin-top: 0pt; margin-right: 103.85pt; margin-bottom: 0pt; text-align: right; widows: 0; orphans: 0; font-size: 8pt;"><strong><span style="font-family: 'Times New Roman';">MAI THỊ THÚY</span></strong></p>
    </div>
</div>