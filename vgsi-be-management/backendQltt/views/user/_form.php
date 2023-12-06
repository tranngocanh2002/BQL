<?php

use common\models\User;
use common\models\UserRole;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;

/**
 * @var yii\web\View $this
 * @var common\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
$new_list_role = UserRole::find()->where(['<>','id','1'])->all();
$list_role = ArrayHelper::map($new_list_role, 'id', 'name');

$sexList = User::getSexList();

$birthday = $model->birthday && is_numeric($model->birthday) ? date('d/m/Y', $model->birthday) : $model->birthday;

?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#user-email').change(function () {
            $('#user-username').val($('#user-email').val());
        });

        const sanitizeInput = (input) => {
            let sanitizedValue = input.value.replace(/[^\w\s]/gi, '');
            input.value = sanitizedValue;
        }
    }, false);
</script>

<style>
    .form-horizontal .control-label {
        text-align: left;
    }

    .form-control[disabled],
    .form-control[readonly],
    fieldset[disabled] .form-control {
        background-color: #fff;
    }

    span#select2-role_id-container {
        display: table;
        margin: 0 auto 0 0;
    }

    .box {
        border-top: 0px;
        box-shadow: 0 0px 0px rgba(0, 0, 0, 0.05);
    }

    .box-body {
        padding: 20px;
        min-height: 80vh
    }


    .panel-info {
        border-color: transparent;
    }

    .panel {
        border: 0px solid transparent;
        border-radius: 0px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    }

    .well-small {
        border: solid 0px #ddd;
    }

    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        border-left: 0px solid #aaa;
        border: none;
    }

    .file-preview {
        border: 0px solid #ddd;
    }

    .file-default-preview img {
        border-radius: 16px;
        max-width: 280px;
        float: unset;
    }

    .form-horizontal .form-group {
        margin-left: 0;
    }

    label {
        font-weight: 100;
    }

    .col-md-3 {
        width: 30%;
    }

    .col-md-9 {
        width: 70%;
    }

    .form-control {
        border-radius: 4px;
        color: gray;
        font-weight: 100;
    }

    .btn-primary {
        background-color: white;
        border-color: lightgray;
        color: grey;
        border-radius: 4px;
    }

    .btn-default:active:hover,
    .btn-default.active:hover,
    .open>.dropdown-toggle.btn-default:hover,
    .btn-default:active:focus,
    .btn-default.active:focus,
    .open>.dropdown-toggle.btn-default:focus,
    .btn-default:active.focus,
    .btn-default.active.focus,
    .open>.dropdown-toggle.btn-default.focus {
        background-color: white;
        border-color: #ffa67d;
        color: #ffa67d;
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
        border-color: #016343;
        background-color: white;
    }

    .btn-success:focus,
    .btn-success.focus {
        color: #016343;
        border-color: #016343;
        background-color: white;
    }

    .button>a:nth-child(1) {
        background-color: white;
        border-color: #ffa67d;
        color: #ffa67d;
        margin-right: 12px;
    }

    button.btn-primary:nth-child(2) {

        color: #016343 !important;
        border-color: #016343 !important;
        background-color: transparent !important;
    }

    button.btn-success {
        width: auto;
        color: #016343;
        border-color: #016343;
        background-color: white;
    }

    /* div.col-sm-12:nth-child(8) {
        width: 86%;
    } */




    .btn-success:hover,
    .btn-success:active,
    .btn-success.hover {
        color: #016343;
        background-color: white;
        border-color: #016343;
    }



    .btn-primary:active:hover,
    .btn-primary.active:hover,
    .open>.dropdown-toggle.btn-primary:hover,
    .btn-primary:active:focus,
    .btn-primary.active:focus,
    .open>.dropdown-toggle.btn-primary:focus,
    .btn-primary:active.focus,
    .btn-primary.active.focus,
    .open>.dropdown-toggle.btn-primary.focus {
        color: #016343;
        background-color: white;
        border-color: #016343;
    }

    .krajee-default.file-preview-frame:not(.file-preview-error) {

        float: unset;
        display: block;
        margin-right: auto;
        margin-left: auto;
        border-radius: 16px;

    }

    .krajee-default.file-preview-frame .kv-file-content {
        align-items: center;
        display: block;
        margin: auto;
        margin-top: 12px;
    }

    .addImage {
        margin-top: 40%;
        color: grey;
    }

    .select2-container--krajee .select2-selection--single .select2-selection__rendered {
        color: grey;
        font-weight: 100;
    }

    .form-group.has-success .form-control,
    .form-group.has-success .input-group-addon {
        border-color: #d2d6de;
        box-shadow: none
    }

    .form-group.has-success label {
        color: black;
    }

    .btn.btn-primary2 {
        color: white;
    }

    .form-control[disabled],
    .form-control[readonly],
    fieldset[disabled] .form-control {
        background-color: #fff;
        border-radius: 4px;
    }



    .form-control:focus {
        border-color: #016343 !important;

    }

    .has-success.select2-container--krajee .select2-dropdown,
    .has-success .select2-container--krajee .select2-selection {
        border-color: #016343;
    }

    .select2-container--krajee.select2-container--open .select2-selection,
    .select2-container--krajee .select2-selection:focus {
        /*! -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px rgba(102, 175, 233, 0.6); */
        /*! box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px rgba(102, 175, 233, 0.6); */
        -webkit-transition: none;
        -o-transition: none;
        transition: none;
        border-color: #016343;
    }

    .btn:active {
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
    }

    .btn:active,
    .btn.active {

        -webkit-box-shadow: none;
        box-shadow: none;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 3]

                ]); ?>

                <div class="col-sm-6">
                    <h4 style="margin-bottom: 50px;"><strong>
                            <?= Yii::t('backend', 'Thông tin') ?>
                    </h4>
                    <?= $form->field($model, 'code_user')->textInput(['maxlength' => 10]) ?>

                    <?= $form->field($model, 'full_name', ['options' => ['class' => 'required highlight-addon form-group']])->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'username', ['options' => ['style' => 'display: none']])->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'disabled' => !$model->isNewRecord]) ?>

                    <?= $form->field($model, 'phone')->textInput(['maxlength' => 10]) ?>

                    <?= $form
                        ->field($model, 'birthday')
                        ->widget(DatePicker::class, [
                            'dateFormat' => 'dd/MM/yyyy',
                            'options' => ['class' => 'form-control'],
                            'clientOptions' => [
                                'changeYear' => true,
                                'changeMonth' => true,
                                'yearRange' => '1900:' . date('Y'),
                                'altFormat' => 'yy-mm-dd',
                                'maxDate' => 0,
                            ]
                        ])
                        ->textInput([
                            'placeholder' => Yii::t('backend', 'dd/mm/yyyy'),
                            'value' => $birthday,
                            'readonly' => true,
                        ]);
                    ?>
                    <!-- loại bỏ thằng user role id là 1 vì nó là quyền root của dự án -->
                    <?php unset($list_role[1]); ?>
                    <?= $form->field($model, 'role_id')->widget(Select2::class, [
                        'data' => $list_role,
                        'options' => [
                            'placeholder' => Yii::t('backendQltt', 'Chọn nhóm quyền'),
                            // 'style' => 'min-width: 210px',
                            'id' => 'role_id'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'pluginEvents' => [
                            // 'change' => 'function() { console.log("Option selected"); }',
                        ],
                    ]) ?>
                    <!-- <?= $form->field($model, 'role_id')->dropDownList($list_role, ['id' => 'role_id', 'prompt' => Yii::t('backendQltt', 'Chọn nhóm quyền')]) ?> -->

                </div>
                <div class="col-sm-6" style="display: flex; justify-content: center; align-items: center;">
                    <div class="form-group" style="max-width: 470px; min-width: 370px;">
                        <div class="col-sm-12" id="uploadArticle2">
                            <?= Html::activeHiddenInput($model, 'avatar', ['id' => 'ArticleImage']); ?>
                            <div class="well-small">
                                <?php
                                $avatar = ($model->avatar) ? $model->avatar : '/images/avatar.png';
                                ?>
                                <?= FileInput::widget([
                                    'name' => 'UploadForm[files][]',
                                    'pluginOptions' => [
                                        'showCaption' => false,
                                        'showRemove' => false,
                                        'showClose' => false,
                                        'showUpload' => false,

                                        'browseClass' => 'btn btn-primary btn-primary2',
                                        'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                                        'browseLabel' => Yii::t('backendQltt', 'Select Photo'),
                                        'uploadUrl' => Url::toRoute(['upload/tmp']),
                                        'defaultPreviewContent' => Html::img($avatar),
                                        'maxFileSize' => 10240,
                                        'minImageWidth' => 200,
                                        'minImageHeight' => 200,
                                    ],
                                    'options' => [
                                        'accept' => 'image/*',
                                        'allowedFileExtensions' => ['jpg', 'gif', 'png']
                                    ],
                                    'pluginEvents' => [
                                        'fileuploaded' => 'function(event, data, previewId, index){
                                            var response = data.response;
                                            if(response.file_name!=""){
                                                $("#ArticleImage").val(response.file_name);
                                                $(".kv-upload-progress").remove();
                                                $(".file-thumb-progress").remove();
                                                $(".file-default-preview img").src(response.file_name);
                                            }else{
                                                alert(response.message);
                                            }
                                            return false;
                                        }',
                                        'filesuccessremove' => 'function(event, id){
                                        }'
                                    ]
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 ">
                    <div class="col-sm-3"></div>
                    <div class="button group">
                        <a href="/user" class="btn btn-default">
                            <?= Yii::t('backendQltt', 'Cancel') ?>
                        </a>
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('backendQltt', 'Create User') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>