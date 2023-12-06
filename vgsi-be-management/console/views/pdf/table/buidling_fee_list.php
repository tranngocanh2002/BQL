<?php
use common\helpers\CUtils;
use common\models\Apartment;
use common\models\ServiceBuildingConfig;
/* @var $apartment common\models\Apartment */
?>
<?php if(!empty($servicePaymentFee) || !empty($total_servicePaymentFee)){ ?>
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
<?php } ?>