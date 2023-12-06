<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use common\models\AnnouncementCampaign;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backendQltt\models\AnnouncementCampaignSearch */
/* @var $form yii\widgets\ActiveForm */

$status = [
    AnnouncementCampaign::STATUS_ACTIVE => Yii::t('backendQltt', 'Công khai'),
    AnnouncementCampaign::STATUS_UNACTIVE => Yii::t('backendQltt', 'Nháp'),
    AnnouncementCampaign::STATUS_PUBLIC_AT => Yii::t('backendQltt', 'Hẹn giờ'),
];
?>

<style>
    .form-group {
        margin-right: 20px;
        margin-top: 10px;
    }

    .form-control:focus {
        border-color: #ccc;
    }

    .select2-selection.select2-selection--single {
        min-width: 190px;
    }

    span.select2-selection.select2-selection--single {
        display: flex;
        align-items: center;
    }

    span#select2-announcementcampaignsearch-status-container {
        display: table;
        margin: 0 auto 0 0;
    }
</style>

<div class="announcement-campaign-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'type' => ActiveForm::TYPE_INLINE,
    ]); ?>
    <?php 
    if (isset($_COOKIE['language']) && $_COOKIE['language'] === 'vi') {
        $title_search = 'title'; 
    } else {
        $title_search = 'title_en'; 
    }
    ?>
    <?= $form->field($model, $title_search, [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-search"></i>',
            //     'options' => ['style' => 'border-right: none;'],
            // ],
            'append' => ['content' => '<i class="fa fa-fw fa-search"></i>'],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>
    <?= $form->field($model, 'status')->widget(Select2::class, [
        'data' => $status,
        'options' => [
            'placeholder' => Yii::t('backendQltt', 'Trạng thái'),
            'style' => 'min-width: 250px'
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        'pluginEvents' => [
            'change' => 'function() { console.log("Option selected"); }',
        ],
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>