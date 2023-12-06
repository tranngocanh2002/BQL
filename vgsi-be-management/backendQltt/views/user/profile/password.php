<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

?>

<script>
    window.onload = function () {
        document.getElementById("change-password").addEventListener("click", function (event) {
            event.preventDefault()
        });

        $("#modal-default").on("hidden.bs.modal", function () {
            logout()
            setTimeout(() => {
                window.location.href = '/site/login'
            }, 2000);
        });
    }

    function logout() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/site/logout');
        // Set the CSRF token as a header
        xhr.setRequestHeader('X-Csrf-Token', "<?= Yii::$app->request->getCsrfToken() ?>");

        xhr.send();
    }

    function changePassword() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/user/profile?action=change-password');

        var formData = new FormData();
        formData.append('UserChangePasswordForm[old_password]', document.getElementById('userchangepasswordform-old_password').value);
        formData.append('UserChangePasswordForm[password]', document.getElementById('userchangepasswordform-password').value);
        formData.append('UserChangePasswordForm[confirm_password]', document.getElementById('userchangepasswordform-confirm_password').value);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (!JSON.parse(xhr.response).status) {
                    window.location.href = '/user/profile?tab=password';
                }

                if (xhr.status === 200) {
                    console.log('success');
                    $("#modal-default").modal()
                } else {

                }
            }
        };

        // Set the CSRF token as a header
        xhr.setRequestHeader('X-Csrf-Token', "<?= Yii::$app->request->getCsrfToken() ?>");

        xhr.send(formData);
    }

    document.addEventListener('DOMContentLoaded', function () {
        $(document).ready(function () {
            $('#toggle-old-password').click(function () {
                var passwordField = $('#userchangepasswordform-old_password');
                var passwordFieldType = passwordField.attr('type');

                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).html('<i class="fa fa-eye"></i>');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).html('<i class="fa fa-eye-slash"></i>');
                }
            });

            $('#toggle-password').click(function () {
                var passwordField = $('#userchangepasswordform-password');
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
                var passwordField = $('#userchangepasswordform-confirm_password');
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
            <!-- <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Thông báo</h4>
            </div> -->
            <div class="modal-body" style="text-align: center;">
                <img style="margin-bottom: 8px" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/check.png"
                    alt="" height="50">
                <p>
                    <?= Yii::t('backend', 'Mật khẩu của bạn đã được thay đổi thành công.') ?>
                </p>
                <p>
                    <?= Yii::t('backendQltt', 'Hệ thống sẽ yêu cầu bạn đăng nhập lại khi thông báo này được đóng') ?>
                </p>
                <div class="div-center" style="margin-top: 14px">
                    <button type="button" style="width: 140px;" class="btn btn-primary pull-center"
                        data-dismiss="modal"> <?= Yii::t('backendQltt', 'Close') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="menu1" class="tab-pane fade <?= $tab == 'password' ? 'active in' : '' ?>">
    <div class="col-md-12">
        <h4 style="margin-bottom: 24px;">
            <strong>
                <?= Yii::t('backendQltt', 'Đổi mật khẩu') ?>
            </strong>
        </h4>
    </div>
    <div class="col-md-7 col-md-offset-2">
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'action' => ['/user/profile', 'action' => 'change-password'],
            'id' => 'change-password',
            'formConfig' => ['labelSpan' => 4],
        ]); ?>
        <?= $form
            ->field($model, 'old_password', [
                'addon' => [
                    'append' => ['content' => '<span id="toggle-old-password" class="password-toggle"><i class="fa fa-eye-slash"></i></span>', 'class' => 'aa'],
                ],
            ])
            ->passwordInput([
                'autofocus' => true,
                'placeholder' => $model->getAttributeLabel('old_password'),
                'type' => 'password',
                'style' => 'border-right: 0;',
            ])
            ?>
        <?= $form
            ->field($model, 'password', [
                'addon' => [
                    'append' => ['content' => '<span id="toggle-password" class="password-toggle"><i class="fa fa-eye-slash"></i></span>', 'class' => 'aa'],
                ],
            ])
            ->passwordInput([
                'autofocus' => true,
                'placeholder' => $model->getAttributeLabel('password'),
                'type' => 'password',
                'style' => 'border-right: 0;',
            ])
            ?>

        <?= $form
            ->field($model, 'confirm_password', [
                'addon' => [
                    'append' => ['content' => '<span id="toggle-confirm-password" class="password-toggle"><i class="fa fa-eye-slash"></i></span>', 'class' => 'aa'],
                ],

            ])
            ->passwordInput([
                'autofocus' => true,
                'placeholder' => $model->getAttributeLabel('confirm_password'),
                'type' => 'password',
                'style' => 'border-right: 0;',
            ])
            ?>

        <div class="form-group highlight-addon field-user-birthday">
            <label class="control-label has-star col-md-4" for="user-birthday"></label>
            <div class="col-md-8">
                <?= Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'onclick' => 'changePassword()']) ?>
                <div class="help-block"></div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<style>
    .input-group {
        width: 100%;
    }
</style>