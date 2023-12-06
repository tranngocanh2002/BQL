<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $buildingCluster common\models\BuildingCluster */
/* @var $maintenanceDevices[] common\models\ManagementUser */
/* @var $end_time integer */

?>
<td style="width: 612px;">
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Dear User,</p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Một vài thiết bị trong tòa nhà sẽ đến lịch bảo trì vào ngày&nbsp;&nbsp;<strong><?= date('d/m/Y', $end_time); ?></strong></p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Vui lòng kiểm tra và sếp lịch bảo trì đúng hạn.</p>
    <figure class="table" style="text-align: center;"></figure>
    <p style="text-align: center; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 20px; color: #4b4f56; word-break: break-word;">Xin cảm ơn</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
</td>
