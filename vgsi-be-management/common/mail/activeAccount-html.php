<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\ManagementUser */
/* @var $linkWeb string */

?>
<td style="width: 612px;">
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;
    font-size: 16px;
    line-height: 24px;
    color: #4b4f56;
    word-break: break-word;">Xin ch&agrave;o <?= $user->first_name ?>!</p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;
    font-size: 16px;
    line-height: 24px;
    color: #4b4f56;
    word-break: break-word;">Bạn được th&ecirc;m v&agrave;o l&agrave;m th&agrave;nh vi&ecirc;n quản trị
        của <?= $user->buildingCluster->name ?>.&nbsp;</p>
    <p style="text-align: center; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 24px; line-height: 24px; color: #1d2129; word-break: break-word;">
        <strong>Th&ocirc;ng tin k&iacute;ch hoạt của bạn</strong></p>
    <p style="text-align: center;"><a class="button"
                                      style="background-color: #4688F1; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 14px; font-weight: bold; margin: 4px 2px; cursor: pointer; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;"
                                      href="<?= $linkWeb ?>" target="_blank" rel="noopener">K&iacute;ch hoạt t&agrave;i
            khoản</a></p>
    <figure class="table" style="text-align: center;"></figure>
    <p style="text-align: center;font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;
    font-size: 16px;
    line-height: 24px;
    color: #4b4f56;
    word-break: break-word; ">Nhấp v&agrave;o n&uacute;t "K&iacute;ch hoạt t&agrave;i khoản" để k&iacute;ch hoạt t&agrave;i
        khoản của bạn.</p>
    <p style="text-align: center;font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;
    font-size: 16px;
    line-height: 24px;
    color: #4b4f56;
    word-break: break-word;">Email n&agrave;y sẽ hết hạn sau 6 giờ</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
</td>
