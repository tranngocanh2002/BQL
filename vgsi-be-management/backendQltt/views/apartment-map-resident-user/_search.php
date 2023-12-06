<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingCluster;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backendQltt\models\ApartmentMapResidentUserSearch */
/* @var $form yii\widgets\ActiveForm */

$buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');

$typeList = [
    '0' => Yii::t('backendQltt', 'Người không cư trú'),
    '1' => Yii::t('backendQltt', 'Cư dân'),
]
    ?>

<style>
    .form-group {
        margin-right: 20px;
        margin-top: 10px;
    }

    .select2-selection.select2-selection--single {
        min-width: 190px;
    }

    span.select2-selection.select2-selection--single {
        display: flex;
        align-items: center;
    }

    .select2-selection__rendered {
        display: table;
        margin: 0 auto 0 0;
    }


    .select2-container--krajee .select2-selection__clear {
        top: 0.2rem;
    }

    #apartmentmapresidentusersearch-total_apartment {

        min-width: fit-content;
    }


    .input-group .input-group-addon:focus {
        border-color: red;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(' input[name="ApartmentMapResidentUserSearch[resident_user_first_name]"]').attr('placeholder', '<?= Yii::t('backendQltt', 'Họ') ?>');
        $(' input[name="ApartmentMapResidentUserSearch[resident_user_last_name]"]').attr('placeholder', '<?= Yii::t('backendQltt', 'Tên') ?>');
        $(' input[name="ApartmentMapResidentUserSearch[resident_user_phone]"]').attr('placeholder', '<?= Yii::t('backendQltt', 'Số điện thoại') ?>');
        $(' input[name="ApartmentMapResidentUserSearch[total_apartment]"]').attr('placeholder', '<?= Yii::t('backendQltt', 'Số lượng BDS') ?>');
    }, false);
</script>
<div class="apartment-map-resident-user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'type' => ActiveForm::TYPE_INLINE,
    ]); ?>

    <?= $form->field($model, 'resident_user_first_name', [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-info-circle"></i>',
            //     'options' => ['style' => 'border-right: none;'],
            // ],
            'append' => ['content' => '<i class="fa fa-fw fa-search"></i>'],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>

    <?= $form->field($model, 'resident_user_last_name', [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-info-circle"></i>',
            //     'options' => ['style' => 'border-right: none;'],
            // ],
            'append' => ['content' => '<i class="fa fa-fw fa-search"></i>'],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ])->textInput(['placeholder' => Yii::t('backendQltt', Yii::t('backendQltt', 'Tên'))]) ?>

    <?= $form->field($model, 'resident_user_phone', [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-info-circle"></i>',
            //     'options' => ['style' => 'border-right: none;'],
            // ],
            'append' => ['content' => '<i class="fa fa-fw fa-search"></i>'],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ])->textInput(['placeholder' => Yii::t('backendQltt', Yii::t('backendQltt', 'Số điện thoại'))]) ?>

    <?= $form->field($model, 'type')->widget(Select2::class, [
        'data' => $typeList,
        'options' => [
            'placeholder' => Yii::t('backendQltt', 'Loại tài khoản'),
            'style' => 'min-width: 250px'
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>

    <?= $form->field($model, 'building_cluster_id')->widget(Select2::class, [
        'data' => $buildingClusters,
        'options' => [
            'placeholder' => Yii::t('backendQltt', 'Dự án'),
            'style' => 'min-width: 250px'
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>

    <?= $form->field($model, 'total_apartment', [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-info-circle"></i>',
            //     'options' => ['style' => 'border-right: none;'],
            // ],
            'append' => ['content' => '<i class="fa fa-fw fa-search"></i>'],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ])->textInput([
            'placeholder' => Yii::t('backendQltt', 'Số lượng BDS'),
            // 'type' => 'number',
            // 'label' => false,
        ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backendQltt', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>