<?php

use common\models\BuildingCluster;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $allTagRoles */
/* @var $authItemTags */
/* @var $model common\models\BuildingCluster */
/* @var $managementUserModel common\models\ManagementUser */

$isUpdateAccountAdmin = Yii::$app->getRequest()->getQueryParam('update-admin');
?>

<style>
    .line {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-top: 2px solid #e8e8e8;
    }

    label.control-label.has-star.col-md-3 {
        text-align: left;
        padding-left: 32px;
    }

    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    .form-control {
        border-radius: 4px;
    }

    .btn-primary {
        background-color: #fff;
        border-color: #c4c4c4;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #fff;
        border-color: #016343;
        color: #fff;
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

    .btn-primary2 {
        background-color: #fff;
        border-color: #016343;
        color: #016343;
        margin-left: 10px;
    }

    .btn-primary2:hover {
        background-color: #fff;
        border-color: #016343;
        color: #016343;
    }

    .box {
        border-top: 0;
    }

    .well-small {
        border: 0;
    }

    .file-preview {
        border: 0;
    }

    .file-default-preview img {
        border-radius: 16px;
        min-width: 300px;
        float: unset;

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

    @media (min-width: 992px) {
        .col-md-6 {
            width: 100%;
        }
    }

    @media (min-width: 1088px) {
        .col-md-6 {
            width: 50%;
        }
    }

    @media (min-width: 992px) {
        .col-md-4 {
            width: 90%;
        }
    }

    @media (min-width: 1088px) {
        .col-md-4 {
            width: 33.33333333%;
        }
    }

    .form-group {
        margin-bottom: 24px;
    }

    .btn-inside {
        color: #016343;
        padding: 5px 8px;
        border: solid 1px #016343;
        border-radius: 5px;
    }

    .line {
        margin-bottom: 2rem;
    }

    .input-group {
        width: 100%;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(document).ready(function () {
            $('#toggle-password').click(function () {
                var passwordField = $('#password');
                var passwordFieldType = passwordField.attr('type');

                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).html('<i class="fa fa-eye"></i>');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).html('<i class="fa fa-eye-slash"></i>');
                }
            });

            $('#toggle-confirm-password').click(function () {
                var passwordField = $('#confirm-password');
                var passwordFieldType = passwordField.attr('type');

                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).html('<i class="fa fa-eye"></i>');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).html('<i class="fa fa-eye-slash"></i>');
                }
            });
        });
    }, false);
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
                <div class="col-xs-12">
                    <div class="col-md-6">
                        <h4 style="margin-bottom: 32px;"><strong>
                                <?= Yii::t('backend', 'Thông tin dự án') ?>
                            </strong></h4>
                    </div>
                    <?php if (!$model->isNewRecord && $isUpdateAccountAdmin) { ?>
                        <div class="col-md-6">
                            <p class="text-right">
                                <a href="<?= Url::toRoute(['building-cluster/update', 'id' => $model->id]); ?>"><span
                                        class="btn-inside">
                                        <?= Yii::t('backendQltt', 'Chỉnh sửa') ?>
                                    </span></a>
                            </p>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4"
                        style="display: flex; align-items: center; justify-content: center; margin-left: 64px; margin-right: 64px">
                        <div class="form-group" style="max-width: 500px; min-width: 420px;">
                            <!-- <label class="control-label col-sm-3"><?php echo $model->getAttributeLabel('avatar'); ?></label> -->
                            <div class="col-sm-12" id="uploadArticle2">
                                <?= Html::activeHiddenInput($model, 'avatar', ['id' => 'ArticleImage']); ?>
                                <div class="well-small">
                                    <?php
                                    $urlAvartar = json_decode($model->medias, true);
                                    $avatar = $urlAvartar['avatarUrl'] ? $urlAvartar['avatarUrl'] : '/images/imageDefault.jpg';
                                    if (!empty($model->avatar)) {
                                        $avatar = $model->avatar;
                                    }
                                    ?>
                                    <?= FileInput::widget([
                                        'name' => 'UploadForm[files][]',
                                        'pluginOptions' => [
                                            'showCaption' => false,
                                            'showRemove' => false,
                                            'showClose' => false,
                                            'showUpload' => false,
                                            'browseClass' => 'btn btn-primary',
                                            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                                            'browseLabel' => Yii::t('backendQltt', 'Select Photo'),
                                            'uploadUrl' => Url::toRoute(['upload/tmp']),
                                            'defaultPreviewContent' => Html::img($avatar),
                                            'maxFileSize' => 10000,
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
                    <div class="col-md-6">
                        <?= $form->field($model, 'name')
                            ->textInput(['maxlength' => true, 'disabled' => !$model->isNewRecord && $isUpdateAccountAdmin])
                            ->label(Yii::t('backendQltt', 'Tên dự án')) ?>
                        <?= $form->field($model, 'domain')->textInput(['maxlength' => true, 'disabled' => !$model->isNewRecord && $isUpdateAccountAdmin]) ?>
                        <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'disabled' => !$model->isNewRecord && $isUpdateAccountAdmin]) ?>
                        <?= $form->field($model, 'description')
                            ->textarea([
                                'disabled' => !$model->isNewRecord && $isUpdateAccountAdmin,
                                'maxlength' => true,
                                'style' => 'resize: vertical;',
                                'rows' => 6,
                            ])
                            ->label(Yii::t('backendQltt', 'Giới thiệu')) ?>
                    </div>

                </div>
                <div class="col-md-12">
                    <div class="clearfix line"></div>
                    <!-- <h4><strong><?= Yii::t('backendQltt', 'Thông tin tài khoản Admin*') ?></strong></h4> -->
                    <div class="col-xs-12">
                        <div class="col-md-6" style="padding-left: 0;">
                            <h4><strong>
                                    <?= Yii::t('backendQltt', 'Thông tin tài khoản Admin*') ?>
                                </strong></h4>
                        </div>
                        <?php if (!$model->isNewRecord && !$isUpdateAccountAdmin) { ?>
                            <div class="col-md-6">
                                <p class="text-right">
                                    <a
                                        href="<?= Url::toRoute(['building-cluster/update', 'id' => $model->id, 'update-admin' => 1]); ?>"><span
                                            class="btn-inside">
                                            <?= Yii::t('backendQltt', 'Chỉnh sửa') ?>
                                        </span>
                                    </a>
                                </p>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6" style="margin-bottom: 16px;">
                            <span><strong>
                                    <?= Yii::t('backendQltt', 'Ghi chú') ?>
                                </strong></span><br>
                            <small><i>-
                                    <?= Yii::t('backendQltt', 'Đây là tài khoản dành cho cấp quản lý tập trung. Một số dự án chỉ có thể có 1 tài khoản admin') ?>
                                </i></small><br>
                            <small><i>-
                                    <?= Yii::t('backendQltt', 'Bắt buộc phải nhập thông tin khi tạo dự án') ?>
                                </i></small><br>
                        </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $buildingCluster = BuildingCluster::findOne($managementUserModel->building_cluster_id);

                        echo $form->field($managementUserModel, 'email')
                            ->textInput([
                                'placeholder' => $managementUserModel->getAttributeLabel('email'),
                                'disabled' => !$model->isNewRecord && !$isUpdateAccountAdmin,
                                'maxlength' => 50,
                                'value' => $model->email
                            ])

                            ?>

                        <?=
                            $form
                                ->field($managementUserModel, 'password', [
                                    'addon' => [
                                        'append' => ['content' => '<span id="toggle-password" class="password-toggle"><i class="fa fa-eye-slash"></i></span>', 'class' => 'aa', 'options' => ['style' => 'border-radius: 0 4px 4px 0 !important;']],

                                    ],
                                ])
                                ->passwordInput([
                                    'placeholder' => $managementUserModel->getAttributeLabel('password'),
                                    'disabled' => !$model->isNewRecord && !$isUpdateAccountAdmin,
                                    'value' => !$model->isNewRecord && !$isUpdateAccountAdmin ? '********' : '',
                                    'id' => 'password',
                                    'style' => 'border-right: 0;',
                                    'maxlength' => 20,
                                ])
                            ?>
                        <?php
                        if ((!$model->isNewRecord && $isUpdateAccountAdmin) || $model->isNewRecord) {
                            echo $form
                                ->field($managementUserModel, 'confirm_password', [
                                    'addon' => [
                                        'append' => ['content' => '<span id="toggle-confirm-password" class="confirm-password-toggle"><i class="fa fa-eye-slash"></i></span>', 'class' => 'aa', 'options' => ['style' => 'border-radius: 0 4px 4px 0 !important;']],
                                    ],
                                ])
                                ->passwordInput([
                                    'placeholder' => $managementUserModel->getAttributeLabel('confirm_password'),
                                    'id' => 'confirm-password',
                                    'style' => 'border-right: 0;',
                                    'maxlength' => 20,
                                ]);
                        }
                        ?>
                        <div class="form-group col-xs-12 text-center">
                            <?php
                            $urlRedirect = $model->isNewRecord ? '/building-cluster' : '/building-cluster/view?id=' . $model->id;
                            ?>
                            <a href="<?= $urlRedirect ?>" class="btn btn-default2"><?= Yii::t('backendQltt', 'Cancel') ?></a>
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('backendQltt', 'Tạo dự án') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary2']) ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>