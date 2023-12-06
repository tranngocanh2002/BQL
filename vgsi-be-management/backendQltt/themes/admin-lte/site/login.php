<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::t('backendQltt', 'Sign In');
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(document).ready(function () {
            $('#toggle-password').click(function () {
                var passwordField = $('#loginform-password');
                var passwordFieldType = passwordField.attr('type');

                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).html('<i class="fa fa-eye"></i>');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).html('<i class="fa fa-eye-slash"></i>');
                }
            });
        })
    }, false);
</script>

<style>
    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }
</style>
<div class="login-box">
    <div class="login-box-body">
        <div class="login-logo">
            <a href="#"><b>
                    <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/logo3.jpg" alt="" height="60">
                </b></a>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
        <?=
            $form
                ->field($model, 'email', [
                    'addon' => [
                        'prepend' => [
                            'content' => '<i class="fa fa-user" style="color: #00000040;"></i>',
                            'options' => ['style' => 'border-radius: 4px 0 0 4px !important;'],
                        ]
                    ],
                    'inputOptions' => [
                        'style' => 'border-left: 0;',
                    ],
                ])
                ->label(false)
                ->textInput(['placeholder' => Yii::t('backendQltt', 'Email đăng nhập'), 'maxlength' => true])
            ?>

        <?=
            $form
                ->field($model, 'password', [
                    'addon' => [
                        'append' => ['content' => '<span id="toggle-password" class="toggle-password"><i class="fa fa-eye-slash"></i></span>', 'class' => 'aa', 'options' => ['style' => 'border-radius: 0 4px 4px 0 !important;']],
                        'prepend' => [
                            'content' => '<i class="fa fa-lock" style="color: #00000040;"></i>
                        ',
                            'options' => ['style' => 'border-radius: 4px 0 0 4px !important;']
                        ]
                    ],
                ])
                ->label(false)
                ->passwordInput([
                    'placeholder' => Yii::t('backendQltt', 'Mật khẩu'),
                    'style' => 'border-right: 0; border-left: 0;',
                    'maxlength' => true
                ])
            ?>

        <div class="row">
            <div class="col-xs-8">
                <a style="color: #016343" href="<?php echo Url::to(['request-password-reset']) ?>">
                    <?= Yii::t('backendQltt', 'Quên mật khẩu?') ?>
                </a><br>
            </div>
            <div class="col-xs-4">
                <?= Html::submitButton(Yii::t('backendQltt', 'Đăng nhập'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<style>
    .login-box {
        width: 600px;
        box-shadow: 0 0 100px var(#016343);
    }

    .btn-primary {
        background-color: #016343;
        border-color: #016343;
    }

    .btn-primary:hover {
        background-color: #016343;
    }

    .btn.btn-flat {
        border-radius: 4px;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
        border-width: 0px;
    }

    .login-box-body {
        height: 350px;
        padding: 60px;
        border-radius: 6px;
        padding-top: 24px;
    }

    .login-logo {
        margin-bottom: 32px;
        text-align: center;
    }

    .login-logo img {
        width: 45%;
        height: fit-content;
    }
</style>