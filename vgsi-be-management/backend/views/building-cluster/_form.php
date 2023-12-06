<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use kartik\editors\Summernote;
use kartik\editors\Codemirror;

/* @var $this yii\web\View */
/* @var $allTagRoles */
/* @var $authItemTags */
/* @var $model common\models\BuildingCluster */
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]); ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'domain')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'hotline')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'one_signal_app_id')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'one_signal_api_key')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'bank_account')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'bank_holders')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'description')->textarea() ?>

                <?= $form->field($model, 'service_bill_template')->widget(Codemirror::class, [
                    'preset' => Codemirror::PRESET_HTML,
                    'options' => ['placeholder' => 'Edit your code here...']
                ]); ?>

                <?= $form->field($model, 'service_bill_invoice_template')->widget(Codemirror::class, [
                    'preset' => Codemirror::PRESET_HTML,
                    'options' => ['placeholder' => 'Edit your code here...']
                ]); ?>

                <?= $form->field($model, 'tax_code')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'tax_info')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'limit_sms')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'sms_price')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'limit_email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'limit_notify')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'link_whether')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'email_account_push')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'email_password_push')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'sms_brandname_push')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'sms_account_push')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'sms_password_push')->textInput(['maxlength' => true]) ?>

                <div class="form-group highlight-addon field-buildingcluster-auth_item_tags">
                    <label class="control-label has-star col-md-2" for="buildingcluster-auth_item_tags">Auth Item Tags</label>
                    <div class="col-md-10">
                    <?php
                    foreach ($allTagRoles as $role){
                        if($role->tag == 'FULL'){ continue; }
                        if (in_array($role->tag, $authItemTags)) {
                            echo ' <label> <input type="checkbox" name="auth_item_tags[]" value="' . $role->tag . '" checked> ' . $role->tag . ' </label> ';
                        } else {
                            echo ' <label> <input type="checkbox" name="auth_item_tags[]" value="' . $role->tag . '"> ' . $role->tag . ' </label> ';
                        }
                    }
                    ?>
                    <div class="help-block"></div>
                    </div>
                </div>

                <?= $form->field($model, 'security_mode')->checkbox() ?>

                <?= $form->field($model, 'status')->checkbox() ?>

                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

