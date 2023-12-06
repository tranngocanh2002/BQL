<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backendQltt\models\BuildingClusterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
    .form-group {
        margin-right: 20px;
        margin-top: 10px;
    }

    .form-control:focus {
        border-color: #ccc;
    }

    .form-control {
        display: block;
        width: 100%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-top-color: rgb(204, 204, 204);
        border-right-color: rgb(204, 204, 204);
        border-right-style: solid;
        border-right-width: 1px;
        border-bottom-color: rgb(204, 204, 204);
        border-left-color: rgb(204, 204, 204);
        border-left-style: solid;
        border-left-width: 1px;
        border-radius: 4px;
    }

    .input-group {
        width: 200px;
    }
</style>
<div class="building-cluster-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'type' => ActiveForm::TYPE_INLINE,
    ]); ?>

    <?= $form->field($model, 'name', [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-search"></i>',
            //     'options' => ['style' => 'border-radius: 4px 0 0 4px !important;'],
            // ],
            'append' => [
                'content' => '<i class="fa fa-fw fa-search" title="' . Yii::t('backendQltt', 'Tìm kiếm theo Tên dự án') . '"></i>',
                'options' => ['style' => 'border-radius: 0px 4px 4px 0px !important;'],
            ],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>

    <?= $form->field($model, 'domain', [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-search"></i>',
            //     'options' => ['style' => 'border-radius: 4px 0 0 4px !important;'],
            // ],
            'append' => [
                'content' => '<i class="fa fa-fw fa-search" title="' . Yii::t('backendQltt', 'Tim kiếm theo Domain') . '"></i>',
                'options' => ['style' => 'border-radius: 0px 4px 4px 0px !important;']
            ],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>

    <?= $form->field($model, 'email', [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-search"></i>',
            //     'options' => ['style' => 'border-radius: 4px 0 0 4px !important;'],
            // ],
            'append' => [
                'content' => '<i class="fa fa-fw fa-search" title="' . Yii::t('backendQltt', 'Tìm kiếm theo Email') . '"></i>',
                'options' => ['style' => 'border-radius: 0px 4px 4px 0px !important;']
            ],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>

    <?= $form->field($model, 'address', [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-search"></i>',
            //     'options' => ['style' => 'border-radius: 4px 0 0 4px !important;'],
            // ],
            'append' => [
                'content' => '<i class="fa fa-fw fa-search" title="' . Yii::t('backendQltt', 'Tìm kiếm theo Địa chỉ') . '"></i>',
                'options' => ['style' => 'border-radius: 0px 4px 4px 0px !important;']
            ],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>