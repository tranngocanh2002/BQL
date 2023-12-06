<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentConfig */

$this->title = Yii::t('backend', 'Create Payment Config');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Payment Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-config-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
