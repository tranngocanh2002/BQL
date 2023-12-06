<?php
/* @var $this yii\web\View */

use yii\web\View;

/* @var $paymentGenCode common\models\PaymentGenCode */
/* @var $paymentGenCodeItems common\models\PaymentGenCodeItem */
if(empty($paymentGenCode)){
    die;
}
?>

<div class="container">
    <div class="jumbotron mt-3">
        <h1>
            <?php
            if($paymentGenCode->apartment){
                echo $paymentGenCode->apartment->name .'/'. trim($paymentGenCode->apartment->parent_path,'/');
            }
            ?>
        </h1>
        <p class="lead">Chi tiết phí:</p>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Loại phí</th>
                <th scope="col">Phí của tháng</th>
                <th scope="col">Số tiền (VND)</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1;$total_amount = 0; foreach ($paymentGenCodeItems as $paymentGenCodeItem){ ?>
                <tr>
                    <th scope="row"><?= $i ?></th>
                    <td><?= \common\models\ServicePaymentFee::$typeList[$paymentGenCodeItem->servicePaymentFee->type] ?></td>
                    <td><?= date('m/Y', $paymentGenCodeItem->servicePaymentFee->fee_of_month) ?></td>
                    <td><?= \common\helpers\CUtils::formatPrice($paymentGenCodeItem->amount) ?></td>
                </tr>
            <?php $i++;$total_amount += $paymentGenCodeItem->amount; } ?>
            <tr>
                <td colspan="3">Tổng phí</td>
                <td><?= \common\helpers\CUtils::formatPrice($total_amount) ?></td>
            </tr>
            </tbody>
        </table>
        <form action="create" method="post">
            <input name="call_by_web" type="hidden" value="1">
            <input name="payment_code" type="hidden" value="<?= $_GET['code'] ?>">
            <button type="submit" class="btn btn-primary">Thanh toán</button>
        </form>
    </div>
</div>
