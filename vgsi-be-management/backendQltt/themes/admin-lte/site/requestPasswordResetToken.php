<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backendQltt\models\PasswordResetRequestForm */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('backendQltt', 'Quên mật khẩu');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-box">
    <!-- /.login-logo -->
    <div class="login-box-body">
        <div class="login-logo">
            <a href="#"><b>
                    <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/logo3.jpg" alt="" height="60"
                        width="280">
                </b></a>
        </div>
        <div class="row">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
            <?=
                $form->field(
                    $model,
                    'email',
                    [
                        'addon' => [
                            'prepend' => [
                                'content' => '<i class="fa fa-user" style="color: #00000040"></i>',
                                'options' => ['style' => 'border-radius: 4px 0 0 4px !important;']
                            ]
                        ],
                        'inputOptions' => [
                            'style' => 'border-left: 0;',
                        ],
                    ]
                )
                    ->textInput(['autofocus' => true])
                    ->label(false)
                    ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>
            <div class="row no-print">
                <div class="col-xs-12">
                    <div class="col-xs-8">
                        <a href="<?= Url::toRoute(['site/login']); ?>" style="color: #016343"><i
                                class="fa fa-angle-left"></i>
                            <?= Yii::t('backendQltt', 'Quay lại') ?>
                        </a>
                    </div>
                    <div class="col-xs-4">
                        <?= Html::submitButton(Yii::t('backendQltt', 'Tiếp tục'), ['class' => 'btn btn-primary pull-right btn-block btn-flat']) ?>
                    </div>
                </div>
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

    .title {
        text-align: center;
        color: #000;
        font-size: 16px;
        font-weight: bold;
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

    .btn.btn-flat {
        border-radius: 4px;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
        border-width: 0px;
    }

    .col-xs-12 {
        padding: 0px;
    }

    .form-group {
        margin-bottom: 60px
    }

    .login-logo img {
        width: 45%;
        height: fit-content;
    }
</style>