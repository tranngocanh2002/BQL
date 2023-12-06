<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ApartmentMapResidentUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="apartment-map-resident-user-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'apartment_id')->textInput() ?>

        <?= $form->field($model, 'resident_user_id')->textInput() ?>

        <?= $form->field($model, 'building_cluster_id')->textInput() ?>

        <?= $form->field($model, 'building_area_id')->textInput() ?>

        <?= $form->field($model, 'type')->textInput() ?>

        <?= $form->field($model, 'status')->textInput() ?>

        <?= $form->field($model, 'created_at')->textInput() ?>

        <?= $form->field($model, 'updated_at')->textInput() ?>

        <?= $form->field($model, 'created_by')->textInput() ?>

        <?= $form->field($model, 'updated_by')->textInput() ?>

        <?= $form->field($model, 'apartment_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'apartment_capacity')->textInput() ?>

        <?= $form->field($model, 'apartment_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'resident_user_phone')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'resident_user_email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'resident_user_first_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'resident_user_last_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'resident_user_avatar')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'resident_user_gender')->textInput() ?>

        <?= $form->field($model, 'resident_user_birthday')->textInput() ?>

        <?= $form->field($model, 'apartment_parent_path')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'install_app')->textInput() ?>

        <?= $form->field($model, 'resident_user_is_send_email')->textInput() ?>

        <?= $form->field($model, 'type_relationship')->textInput() ?>

        <?= $form->field($model, 'resident_user_is_send_notify')->textInput() ?>

        <?= $form->field($model, 'apartment_short_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'resident_user_nationality')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'resident_name_search')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cmtnd')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'noi_cap_cmtnd')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ngay_cap_cmtnd')->textInput() ?>

        <?= $form->field($model, 'work')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'so_thi_thuc')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ngay_dang_ky_nhap_khau')->textInput() ?>

        <?= $form->field($model, 'ngay_dang_ky_tam_chu')->textInput() ?>

        <?= $form->field($model, 'ngay_het_han_thi_thuc')->textInput() ?>

        <?= $form->field($model, 'last_active')->textInput() ?>

        <?= $form->field($model, 'is_deleted')->textInput() ?>

        <?= $form->field($model, 'deleted_at')->textInput() ?>

    </div>
    <div class="box-footer">
        <?= Html::submitButton(Yii::t('backendQltt', 'Save'), ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
