<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */
/* @var $form yii\widgets\ActiveForm */
?>

<script>
    function selectAll(tagName = 'help-category') {
        let inputSelectAll = document.getElementById(tagName);

        let inputs = document.querySelectorAll(`[data-role=${tagName}]`)
        console.log(inputs)
        inputs.forEach(e => {
            if (!inputSelectAll.checked) {
                e.checked = !e.checked
            } else {
                e.checked = true;
            }
        })
    }

    function checkInput(element, tagName = 'help-category') {
        let inputSelectAll = document.querySelector(`[data-role-select-all=${tagName}]`)
        let inputs = document.querySelectorAll(`[data-role=${tagName}]`)
        let totalChecked = 0;

        inputs.forEach(e => {
            if (e.checked) totalChecked++
        })

        inputSelectAll.checked = inputs.length == totalChecked
    }
</script>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 3]
                ]); ?>
                <div class="col-sm-12">
                    <h4 style="margin-bottom: 40px;"><strong>
                            <?= Yii::t('backend', 'Thông tin') ?>
                        </strong></h4>
                </div>
                <div class="col-xs-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-xs-6">
                    <?= $form->field($model, 'description')->textarea(['rows' => 4, 'maxlength' => true]) ?>
                </div>

                <?php
                foreach ($allController as $key => $value) {
                    $checked = !empty($permission[$key]) && count($permission[$key]) == count($value) ? 'checked' : '';

                    if (in_array($key, ['building-cluster', 'user'])) {
                        $checked = !empty($permission[$key]) && count($permission[$key]) - count($value) == 1 ? 'checked' : '';
                    }

                    echo "<div class='col-xs-12'>
                        <label class='control-label' style='font-size: 18px; font-weight: bold;'>" . Yii::t('backendQltt', $key) . "</label>
                        <div style='flex-direction: row;float: right;display: flex; align-items: center;padding-top: 0px;'><label class='control-label pull-right'> <input type='checkbox' id='$key'" . $checked . " data-role-select-all='" . $key . "' onchange='selectAll(`" . $key . "`)'></label><span style='padding-top: 6px;padding-left: 6px;'> " . Yii::t('backendQltt', 'Tất cả') . "</span></div>
                    </div>";

                    foreach ($value as $keyRole => $role) {
                        echo $form->field($model, 'permission', [
                            'options' => [
                                'class' => "col-xs-3",

                            ]
                        ])->checkbox([
                                    'class' => 'my-checkbox',
                                    'value' => $keyRole,
                                    'data-role' => $key,
                                    'name' => 'permission[' . $key . '][]',
                                    'label' => Yii::t('backendQltt', "{$role}"),
                                    'checked' => !empty($permission[$key]) ? in_array($keyRole, $permission[$key]) : false,
                                    'onchange' => "checkInput(this,'" . $key . "')"
                                ]);
                    }
                }
                ?>
                <div class="form-group col-xs-12 text-center">
                    <a href="/user-role" class="btn btn-default2">
                        <?= Yii::t('backendQltt', 'Cancel') ?>
                    </a>
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backendQltt', 'Thêm nhóm quyền') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
<style>
    .control-label.has-star.col-md-3 {
        text-align: left;
    }

    .box-body {
        padding: 10px 40px;
    }

    .form-group.highlight-addon.field-userrole-description label {
        padding-left: min(97px, 15%);
    }

    .form-control {
        border-radius: 4px;
        color: #555555;
        border: 1px solid #c4c4c4;
    }

    .form-control:focus {
        border: 1px solid #016343;
    }

    .form-control:hover {
        border: 1px solid #016343;
    }

    input[type="checkbox"] {
        margin-left: -25px !important;
    }

    .form-group.has-success .form-control,
    .form-group.has-success .input-group-addon {
        border-color: #c4c4c4;
    }

    .form-group.has-success label {
        color: #333333;
    }

    .btn-default2 {
        background-color: #fff;
        border-color: #ffa67d;
        color: #ffa67d;
    }

    .btn-default2:hover {
        background-color: #fff;
        border-color: #ffa67d;
        color: #ffa67d;
    }

    .btn-default2:focus,
    .btn-default2.focus {
        color: #ffa67d !important;
    }

    .btn-primary {
        background-color: #fff !important;
        border-color: #016343 !important;
        color: #016343;
        margin-left: 10px;
    }

    .btn-primary:hover {
        background-color: #fff;
        border-color: #016343;
        color: #016343;
    }

    .btn-primary:focus,
    .btn-primary.focus {
        color: #016343 !important;
        background-color: #286090;
        border-color: #122b40;
    }

    .btn-success {
        background-color: #fff;
        border-color: #016343;
        color: #016343;
    }

    .btn-success:hover {
        background-color: #fff;
        border-color: #016343;
        color: #016343;
    }

    .btn-success:focus,
    .btn-success.focus {
        color: #016343;
        background-color: transparent;
        border-color: #016343;
    }

    .btn-success:hover,
    .btn-success:active,
    .btn-success.hover {
        color: #016343;
        background-color: transparent;
        border-color: #016343;
    }

    .btn-success:active:hover,
    .btn-success.active:hover,
    .open>.dropdown-toggle.btn-success:hover,
    .btn-success:active:focus,
    .btn-success.active:focus,
    .open>.dropdown-toggle.btn-success:focus,
    .btn-success:active.focus,
    .btn-success.active.focus,
    .open>.dropdown-toggle.btn-success.focus {
        color: #016343;
        background-color: transparent;
        border-color: #016343;
    }

    .col-xs-12 {
        padding-bottom: 16px;
        padding-top: 20px;
        margin-bottom: 8px;
        border-bottom: 1px #f4eeeea6 solid;
    }

    .has-success .checkbox {
        color: #555555
    }

    .form-group.col-xs-12.text-center {
        border-bottom: 0;
    }

    label {
        font-weight: 400
    }
</style>