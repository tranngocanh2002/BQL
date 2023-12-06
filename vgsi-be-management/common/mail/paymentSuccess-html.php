<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $apartment common\models\Apartment */
/* @var $serviceBill common\models\ServiceBill */

?>
<td style="width: 612px;">
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;
    font-size: 16px;
    line-height: 24px;
    color: #4b4f56;
    word-break: break-word;">Xin ch&agrave;o <?= $apartment->name .'/'.trim($apartment->parent_path, '/') ?>!</p>
    <p style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;
    font-size: 16px;
    line-height: 24px;
    color: #4b4f56;
    word-break: break-word;">Xác nhận giao dịch thanh toán thành công với bill code: <?= $serviceBill->code ?>.&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
</td>
