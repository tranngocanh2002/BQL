<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backedQltt\models\ResetPasswordForm */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('backendQltt', 'Change the password');
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(document).ready(function () {
            $('#toggle-password').click(function () {
                var passwordField = $('#resetpasswordform-password');
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
                var passwordField = $('#resetpasswordform-confirm_password');
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
<div class="login-box">
    <!-- /.login-logo -->
    <div class="login-box-body">
        <div class="login-logo">
            <a href="#"><b>
                    <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/logo3.jpg" alt="">
                </b></a>
        </div>
        <h3 class="text-center">
            <?= Html::encode($this->title) ?>
        </h3>

        <div class="row">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
            <?= $form
                ->field($model, 'password', [
                    'addon' => [
                        'append' => ['content' => '<span id="toggle-password" class="toggle-password"><i class="fa fa-eye-slash"></i></i></span>', 'class' => 'aa'],
                    ],
                ])
                ->passwordInput(['autofocus' => true])
                ->label(false)
                ->passwordInput([
                    'placeholder' => $model->getAttributeLabel('password'),
                    'maxlength' => 20,
                    'value' => '',
                    'style' => 'border-right: 0;',
                ])
                ?>
            <?=
                $form->field($model, 'confirm_password', [
                    'addon' => [
                        'append' => ['content' => '<span id="toggle-confirm-password" class="confirm-password-toggle"><i class="fa fa-eye-slash"></i></i></span>', 'class' => 'aa'],
                    ],
                ])
                    ->passwordInput()
                    ->label(false)
                    ->passwordInput([
                        'placeholder' => $model->getAttributeLabel('confirm_password'),
                        'maxlength' => 20,
                        'value' => '',
                        'style' => 'border-right: 0;',
                    ])
                ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('backendQltt', 'Change password'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
<style>
    .login-box {
        width: 600px;
        position: relative;
        margin-top: 190px;
        box-shadow: 0 0 100px var(#016343);
    }

    .login-box-body {
        min-height: 410px;
        padding-top: 24px;
        padding-bottom: 60px;
        padding-right: 70px;
        padding-left: 70px;
        border-radius: 6px
    }

    .login-logo {
        margin-bottom: 32px;
        text-align: center;
    }

    .btn-primary {
        background-color: #016343;
        border-color: #016343;
    }

    .btn-primary:hover {
        background-color: #016343;
    }

    .btn-primary:focus {
        background-color: #016343;
    }

    .btn {

        float: right;
    }

    .title {
        text-align: center;
        color: #000;
        font-size: 16px;
        font-weight: bold;
    }

    .login-logo img {
        width: 45%;
        height: fit-content;
    }
</style>