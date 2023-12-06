<?php
use common\helpers\CUtils;
use common\models\Apartment;
use common\models\ServiceBuildingConfig;
/* @var $apartment common\models\Apartment */
/* @var $i_count */
?>
<style>
    td p {
        margin: 3px;
    }
</style>
<?php $total_water_fee = 0; if(!empty($servicePaymentFeeWater) || (!empty($total_servicePaymentFeeWater) && $total_servicePaymentFeeWater->more_money_collecte > 0)){
    $json_desc = [];
    if(!empty($servicePaymentFeeWater)){
        if(!empty($servicePaymentFeeWater->json_desc)){
            $json_desc = json_decode($servicePaymentFeeWater->json_desc, true);
        }
        $json_desc['price'] = $servicePaymentFeeWater->more_money_collecte;
        $total_water_fee += $servicePaymentFeeWater->more_money_collecte;
    }
    ?>
    <p><span style="font-size:14px"><strong><span style="font-family:times new roman">&nbsp; &nbsp;<?= $i_count ?>.&nbsp;</span>Tiền nước/&nbsp;Water&nbsp;cost:</strong></span></p>
    <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 8pt;" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td style="width: 49.4pt; border-style: solid; border-width: 1pt; vertical-align: top;" rowspan="2">
                <p><strong><span style="font-family: 'Times New Roman';">1.Phí nước</span></strong></p>
            </td>
            <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Chỉ số cũ / Previous Indicator</span></strong></p>
            </td>
            <td style="width: 73.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Chỉ số mới / Current Indicator</span></strong></p>
            </td>
            <td style="width: 203.65pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                <p><strong><span style="font-family: 'Times New Roman';">Tiêu thụ / Consumption (m3)</span></strong></p>
            </td>
            <td style="width: 93.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Ghi chú/Note</span></strong></p>
            </td>
        </tr>
        <tr>
            <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo $json_desc['month']['start_index']; } ?></span></p>
            </td>
            <td style="width: 73.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo $json_desc['month']['end_index']; } ?></span></p>
            </td>
            <td style="width: 203.65pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                <p><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo $json_desc['month']['total_index']; } ?></span></p>
            </td>
            <td style="width: 93.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
            </td>
        </tr>
        <tr>
            <td style="width: 49.4pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">STT/No</span></strong></p>
            </td>
            <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Tiêu thụ thực tế Consumption (m3)</span></strong></p>
            </td>
            <td style="width: 73.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Đơn giá/m3 Price/m3</span></strong></p>
            </td>
            <td style="width: 116.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Thành tiền / Amount – VND</span></strong></p>
            </td>
            <td style="width: 180.65pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                <p><strong><span style="font-family: 'Times New Roman';">Ghi chú/Note</span></strong></p>
            </td>
        </tr>
        <tr>
            <td style="width: 49.4pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><span style="font-family: 'Times New Roman';">1</span></p>
            </td>
            <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo $json_desc['month']['total_index']; } ?></span></p>
            </td>
            <td style="width: 73.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><span style="font-family: 'Times New Roman';">Định mức</span></p>
            </td>
            <td style="width: 116.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo CUtils::formatPrice($json_desc['price']); } ?></span></p>
            </td>
            <td style="width: 180.65pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                <p><span style="font-family: 'Times New Roman';"><?php if(!empty($json_desc)){ echo implode("<br>", $json_desc['dm']); } ?></span></p>
            </td>
        </tr>
        <?php if(!empty($total_servicePaymentFeeWater)){ $total_water_fee += (int)$total_servicePaymentFeeWater->more_money_collecte; ?>
            <tr>
                <td style="width: 125.85pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                    <p><strong><span style="font-family: 'Times New Roman';">Nợ cũ:</span></strong></p>
                </td>
                <td style="width: 278.5pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="3">
                    <p><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice((int)$total_servicePaymentFeeWater->more_money_collecte) ?></span></p>
                </td>
                <td style="width: 93.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                    <p><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td style="width: 125.85pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                <p><strong><span style="font-family: 'Times New Roman';">Tổng cộng/Total payment</span></strong></p>
            </td>
            <td style="width: 278.5pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="3">
                <p><strong><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice($total_water_fee) ?></span></strong></p>
            </td>
            <td style="width: 93.85pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                <p><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
            </td>
        </tr>
        </tbody>
    </table>
<?php } ?>