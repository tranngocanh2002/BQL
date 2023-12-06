<?php
use common\helpers\CUtils;
use common\models\Apartment;
use common\models\ServiceBuildingConfig;
/* @var $apartment common\models\Apartment */
?>
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
        </tbody>
    </table>
<?php } ?>