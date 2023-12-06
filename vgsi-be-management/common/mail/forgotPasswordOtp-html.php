<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $buildingCluster common\models\BuildingCluster */
/* @var $user common\models\ManagementUser */
/* @var $otp string */

?>
<td style="width: 612px;">
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Dear <?= $user->first_name . ' ' . $user->last_name ?>,</p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Bạn đang thực hiện khôi phục mật khẩu trên hệ thống quản lý dự án <?= $buildingCluster->name; ?>. Vui lòng nhập mã xác thực bên dưới để khôi phục mật khẩu cho tài khoản của bạn. Mã xác thực có hiệu lực trong 15 phút:
    </p>
    <p style="margin-left:10%; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 24px; line-height: 24px; color: #1d2129; word-break: break-word;"><strong>Mã xác thực</strong> = <?= $otp ?></p>
    <figure class="table" style="text-align: center;"></figure>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 20px; color: #4b4f56; word-break: break-word;">Trân trọng</p>
</td>
