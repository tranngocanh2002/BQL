<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

?>

<style>
    .input-group {
        width: 100%;
    }

    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    .form-group {
        padding-bottom: 8px;
    }

    .modal-dialog {
        min-width: 400px;
        width: 600px !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(document).ready(function () {
            $('#toggle-password').click(function () {
                var passwordField = $('#user-password');
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
                var passwordField = $('#user-confirm_password');
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

<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'action' => ['/user/reset-password', 'id' => $modelResetPassword->id]
            ]); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">
                    <?= Yii::t('backendQltt', 'Đặt lại mật khẩu') ?>
                </h4>
            </div>
            <div class="modal-body">
                <?= $form->field($modelResetPassword, 'password', [
                    'addon' => [
                        'append' => ['content' => '<span id="toggle-password" class="toggle-password"><i class="fa fa-eye-slash"></i></i></span>', 'class' => 'aa'],
                    ],
                ])->textInput([
                            'type' => 'password',
                            'maxlength' => 20,
                            'value' => '',
                            'style' => 'border-right: 0;',
                        ])->label(Yii::t('backendQltt', 'Mật khẩu')) ?>
                <?= $form->field($modelResetPassword, 'confirm_password', [
                    'addon' => [
                        'append' => ['content' => '<span id="toggle-confirm-password" class="confirm-password-toggle"><i class="fa fa-eye-slash"></i></i></span>', 'class' => 'aa'],
                    ],
                ])->textInput([
                            'type' => 'password',
                            'maxlength' => 20,
                            'value' => '',
                            'style' => 'border-right: 0;',
                        ])->label(Yii::t('backendQltt', 'Nhập lại mật khẩu mới')) ?>
                <small>
                    <?= Yii::t('backendQltt', '*Mật khẩu từ 8 đến 20 ký tự') ?>
                </small>
                <div class="checkbox">
                    <label><input type="checkbox" name="send_email" checked>
                        <?= Yii::t('backendQltt', 'Gửi email thông báo cho người dùng') ?>
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="text-align: right;">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?= Yii::t('backendQltt', 'Cancel') ?>
                </button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                <?= Html::submitButton(Yii::t('backendQltt', 'Đặt lại'), ['class' => 'btn btn-primary']); ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>