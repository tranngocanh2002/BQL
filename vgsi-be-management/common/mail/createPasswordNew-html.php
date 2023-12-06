<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $buildingCluster common\models\BuildingCluster */
/* @var $user common\models\ManagementUser */
/* @var $password string */

?>
<td style="width: 612px;">
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Dear <?= $user->first_name . ' ' . $user->last_name ?>,</p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Admin đã thay đổi mật khẩu của bạn:
    </p>
    <p style="margin-left:10%; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 24px; line-height: 24px; color: #1d2129; word-break: break-word;"><strong>Tài khoản</strong> = <?= $user->email ?></p>
    <p style="margin-left:10%; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 24px; line-height: 24px; color: #1d2129; word-break: break-word;"><strong>Mật khẩu</strong> = <?= $password ?></p>
    <figure class="table" style="text-align: center;"></figure>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 24px; line-height: 24px; color: #1d2129; word-break: break-word;">Vui lòng copy và dán đường dẫn bên dưới lên trình duyệt và đăng nhập hệ thống: <?= $buildingCluster->domain .'/user/login' ?></p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 20px; color: #4b4f56; word-break: break-word;">Trân trọng</p>
</td>
