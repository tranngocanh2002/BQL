<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\ManagementUser */
/* @var $linkWeb string */

//$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/reset-password', 'code' => $verifyCode->code]);
$resetLink = $linkWeb;
?>
<td style="width: 612px;">
    <p style="font-family: -apple-system, BlincMacSystemFont, 'Helvetica Neue', Helvetica, 'Lucida Grande', tahoma, verdana, arial, sans-serif; font-size: 24px; line-height: 24px; color: #4b4f56; word-break: break-word; text-align: center; font-weight: bold;"><?= $user->buildingCluster->name ?></p>
    <p style="font-family: -apple-system, BlincMacSystemFont, 'Helvetica Neue', Helvetica, 'Lucida Grande', tahoma, verdana, arial, sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word; text-align: center;">Xin chào <strong><?= $user->first_name ?></strong>!</p>
    <p style="font-family: -apple-system, BlincMacSystemFont, 'Helvetica Neue', Helvetica, 'Lucida Grande', tahoma, verdana, arial, sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word; text-align: center;">Chúc mừng bạn được tham gia hệ thống quản trị <strong><?= $user->buildingCluster->name ?></strong> dựa trên nền tảng Luci Management.</p>
    <p style="font-family: -apple-system, BlincMacSystemFont, 'Helvetica Neue', Helvetica, 'Lucida Grande', tahoma, verdana, arial, sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word; text-align: center;"><a style="background-color: #4688F1; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 14px; font-weight: bold; margin: 4px 2px; cursor: pointer; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;" href="<?= $linkWeb ?>" target="_blank" rel="noopener">Bắt đầu</a></p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Luci Management gi&uacute;p bạn v&agrave; đội ngũ của bạn quản l&yacute; v&agrave; giao tiếp với cư d&acirc;n một c&aacute;ch hiệu quả.</p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;"><strong>Th&ocirc;ng b&aacute;o nhanh ch&oacute;ng</strong></p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Với một v&agrave;i thao t&aacute;c tr&ecirc;n hệ thống, bạn c&oacute; thể gửi th&ocirc;ng b&aacute;o cần thiết đến cư d&acirc;n một c&aacute;ch nhanh ch&oacute;ng với nội dung sinh động.</p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;"><strong>Xử l&yacute; y&ecirc;u cầu kịp thời</strong></p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">Khi cư d&acirc;n c&oacute; bất kỳ vấn đề g&igrave;, hệ thống sẽ gửi l&ecirc;n y&ecirc;u cầu để xử l&yacute; kịp thời. Đồng thời c&oacute; thể theo d&otilde;i v&agrave; trao đổi với cư d&acirc;n một c&aacute;ch nhanh ch&oacute;ng.</p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;"><strong>Gửi v&agrave; thống k&ecirc; thanh to&aacute;n c&aacute;c ph&iacute; dịch vụ</strong></p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">C&aacute;c ph&iacute; dịch vụ khi khai b&aacute;o l&ecirc;n hệ thống sẽ được gửi chi tiết c&aacute;c ph&iacute; thanh to&aacute;n đến cho cư d&acirc;n. Việc thanh to&aacute;n v&agrave; quản l&yacute; thống k&ecirc; gi&uacute;p bạn theo d&otilde;i v&agrave; kiểm so&aacute;t một c&aacute;ch hiệu quả.</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
</td>
