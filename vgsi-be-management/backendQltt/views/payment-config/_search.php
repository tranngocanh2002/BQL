<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\PaymentConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'building_cluster_id') ?>

    <?= $form->field($model, 'gate') ?>

    <?= $form->field($model, 'receiver_account') ?>

    <?= $form->field($model, 'merchant_id') ?>

    <?php // echo $form->field($model, 'merchant_pass') ?>

    <?php // echo $form->field($model, 'checkout_url') ?>

    <?php // echo $form->field($model, 'return_url') ?>

    <?php // echo $form->field($model, 'cancel_url') ?>

    <?php // echo $form->field($model, 'notify_url') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
