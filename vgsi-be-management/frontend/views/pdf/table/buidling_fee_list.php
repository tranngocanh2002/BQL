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
<?php if(!empty($servicePaymentFee) || (!empty($total_servicePaymentFee) && $total_servicePaymentFee->more_money_collecte > 0)){ ?>
    <p><span style="font-size:14px">&nbsp;<strong><span style="font-family:times new roman">&nbsp; <?= $i_count ?>.&nbsp;</span>Ph&iacute; quản l&yacute;/&nbsp;Management&nbsp;Cost:</strong></span></p>
    <table style="width: 100%; border: 1pt solid #000000; border-collapse: collapse; text-align: center; font-size: 8pt;" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td style="width: 49.4pt; border-right-style: solid; border-right-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">STT/No</span></strong></p>
            </td>
            <td style="width: 75.45pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Diện tích / Total area</span></strong></p>
            </td>
            <td style="width: 73.85pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Đơn giá/<?php if(!empty($serviceBuildingConfig) && $serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_APARTMENT){ echo 'Căn hộ</span></strong>';}else{ echo 'm</span></strong>2';} ?></p>
            </td>
            <td style="width: 58.25pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Unit Price/<?php if(!empty($serviceBuildingConfig) && $serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_APARTMENT){ echo 'Apartment</span></strong>';}else{ echo 'm</span></strong>2';} ?></p>
            </td>
            <td style="width: 87.15pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Thành tiền/Amount - ( VND)</span></strong></p>
            </td>
            <td style="width: 56.1pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">CÁC KHOẢN NỢ PHÍ</span></strong></p>
            </td>
            <td style="width: 93.75pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                <p><strong><span style="font-family: 'Times New Roman';">Ghi chú/Note</span></strong></p>
            </td>
        </tr>
        <?php $total_building_fee = 0; if(!empty($servicePaymentFee)){ $total_building_fee += $servicePaymentFee->price; ?>
            <tr>
                <td style="width: 49.4pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                    <p><span style="font-family: 'Times New Roman';">1</span></p>
                </td>
                <td style="width: 75.45pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                    <p><span style="font-family: 'Times New Roman';"><?= $apartment->capacity ?></span></p>
                </td>
                <td style="width: 133.1pt; border-style: solid; border-width: 1pt; vertical-align: top;" colspan="2">
                    <p><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice($serviceBuildingConfig->price) ?></span></p>
                </td>
                <td style="width: 87.15pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                    <p><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice($servicePaymentFee->price) ?></span></p>
                </td>
                <td style="width: 56.1pt; border-style: solid; border-width: 1pt; vertical-align: top;">
                    <p><span style="font-family: 'Times New Roman';">-</span></p>
                </td>
                <td style="width: 93.75pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; vertical-align: top;">
                    <p><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
                </td>
            </tr>
        <?php } ?>
        <?php if(!empty($total_servicePaymentFee)){ $total_building_fee += (int)$total_servicePaymentFee->more_money_collecte; ?>
            <tr>
                <td style="width: 125.85pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; vertical-align: top;" colspan="2">
                    <p><strong><span style="font-family: 'Times New Roman';">Nợ cũ</span></strong></p>
                </td>
                <td style="width: 278.35pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; vertical-align: top;" colspan="4">
                    <p><strong><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice((int)$total_servicePaymentFee->more_money_collecte) ?></span></strong></p>
                </td>
                <td style="width: 93.75pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; vertical-align: top;">
                    <p><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td style="width: 125.85pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; vertical-align: top;" colspan="2">
                <p><strong><span style="font-family: 'Times New Roman';">Tổng cộng/Total payment</span></strong></p>
            </td>
            <td style="width: 278.35pt; border-top-style: solid; border-top-width: 1pt; border-right-style: solid; border-right-width: 1pt; border-left-style: solid; border-left-width: 1pt; vertical-align: top;" colspan="4">
                <p><strong><span style="font-family: 'Times New Roman';"><?= CUtils::formatPrice($total_building_fee) ?></span></strong></p>
            </td>
            <td style="width: 93.75pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; vertical-align: top;">
                <p><span style="font-family: 'Times New Roman';">&nbsp;</span></p>
            </td>
        </tr>
        </tbody>
    </table>
<?php } ?>