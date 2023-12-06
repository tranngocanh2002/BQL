<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use common\models\VerifyCode;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backendQltt\models\CheckOtpTokenForm */

$this->title = Yii::t('backendQltt', 'Xác thực OTP');
$email = Yii::$app->getRequest()->getQueryParam('email');
$timeOtpExpired = Yii::$app->session->get('time_otp_expired');

if (!$email || ($timeOtpExpired < time())) {
    Yii::$app->response->redirect(['/site/login']);
}

$time = VerifyCode::TIME_RESEND_OTP;

?>

<style>
    .disable {
        pointer-events: none;
        color: #666;
    }
</style>

<script>
    function startTimer(duration, display) {
        var timer = duration,
            minutes, seconds;
        setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                timer = 0;
                let reSendOtp = document.getElementById('reSendOtp');
                reSendOtp.classList.remove("disable");
            }
        }, 1000);
    }

    window.onload = function () {
        var timeCountdown = document.getElementById('timeCountdown').value,
            display = document.querySelector('#countdown');
        startTimer(timeCountdown, display);
    };
</script>

<div class="login-box">
    <!-- /.login-logo -->
    <div class="login-box-body">
        <div class="login-logo">
            <a href="#"><b>
                    <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/logo3.jpg" alt="">
                </b></a>
        </div>
        <h5 class="text-center" style="font-weight: 600; color: rgba(0,0,0,0.85); margin-bottom: 20px">
            <?= Html::encode($this->title) ?>
        </h5>
        <h6 class="text-center" style=" margin-bottom: 20px">
            <?= Yii::t('backendQltt', 'Vui lòng nhập mã xác nhận được gửi qua email.') ?>
        </h6>
        <h6 class="text-center pb-3" style="font-weight: 600; color: rgba(0,0,0,0.85);  margin-bottom: 20px">
            <strong>
                <?= $email; ?>
            </strong>
        </h6>
        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
        <input type="hidden" value="<?= $time ?>" id="timeCountdown" />
        <div class="row">
            <div class="col-12">
                <div class="col-xs-2">
                    <?= $form->field($model, 'text_1')->label(false)->textInput(['maxlength' => 1, 'class' => 'form-control input-field', 'id' => 'checkotptokenform_text_1']) ?>
                </div>
                <div class="col-xs-2">
                    <?= $form->field($model, 'text_2')->label(false)->textInput(['maxlength' => 1, 'class' => 'form-control input-field', 'id' => 'checkotptokenform_text_2']) ?>
                </div>
                <div class="col-xs-2">
                    <?= $form->field($model, 'text_3')->label(false)->textInput(['maxlength' => 1, 'class' => 'form-control input-field', 'id' => 'checkotptokenform_text_3']) ?>
                </div>
                <div class="col-xs-2">
                    <?= $form->field($model, 'text_4')->label(false)->textInput(['maxlength' => 1, 'class' => 'form-control input-field', 'id' => 'checkotptokenform_text_4']) ?>
                </div>
                <div class="col-xs-2">
                    <?= $form->field($model, 'text_5')->label(false)->textInput(['maxlength' => 1, 'class' => 'form-control input-field', 'id' => 'checkotptokenform_text_5']) ?>
                </div>
                <div class="col-xs-2">
                    <?= $form->field($model, 'text_6')->label(false)->textInput(['maxlength' => 1, 'class' => 'form-control input-field', 'id' => 'checkotptokenform_text_6']) ?>
                </div>
            </div>
            <div id="error"></div>
        </div>
        <div style="text-align:center">
            <label for="" id="error_item_otp" style="color:red;font-size:13px"></label>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h5 class="text-center">
                    <a href="<?= Url::toRoute(['site/request-password-reset', 'email' => $email]); ?>" class="disable"
                        id="reSendOtp">
                        <?= Yii::t('backendQltt', 'Gửi lại mã xác thực') ?>
                    </a>
                    (<span id="countdown">0</span>s)
                </h5>
            </div>
        </div>
        <div class="row no-print">
            <div class="col-xs-12">
                <a href="<?= Url::toRoute(['site/request-password-reset']); ?>"
                    style="color: #016343; padding-top: 2px"><i class="fa fa-angle-left"></i>
                    <?= Yii::t('backendQltt', 'Quay lại') ?>
                </a>
                <?= Html::submitButton(Yii::t('backendQltt', 'Next'), ['class' => 'btn btn-primary pull-right', 'id' => 'btn_submit_otp']) ?>
            </div>
        </div>
        <div class="col-12" style="padding-top: 20px">

        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
<script>
    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
    var btnSubmitOTP = document.getElementById('btn_submit_otp');
    btnSubmitOTP.addEventListener('click', function (event) {
        let text_1 = document.getElementById('checkotptokenform_text_1');
        let text_2 = document.getElementById('checkotptokenform_text_2');
        let text_3 = document.getElementById('checkotptokenform_text_3');
        let text_4 = document.getElementById('checkotptokenform_text_4');
        let text_5 = document.getElementById('checkotptokenform_text_5');
        let text_6 = document.getElementById('checkotptokenform_text_6');
        text_1.style.borderColor = "unset";
        text_2.style.borderColor = "unset";
        text_3.style.borderColor = "unset";
        text_4.style.borderColor = "unset";
        text_5.style.borderColor = "unset";
        text_6.style.borderColor = "unset";
        // kiểm tra nếu có 1 otp null thì sẽ show error
        if ("" == text_1.value || "" == text_2.value || "" == text_3.value || "" == text_4.value || "" == text_5.value || "" == text_6.value) {
            event.preventDefault();
            if ("" == text_1.value) {
                text_1.style.borderColor = "red";
            }
            if ("" == text_2.value) {
                text_2.style.borderColor = "red";
            }
            if ("" == text_3.value) {
                text_3.style.borderColor = "red";
            }
            if ("" == text_4.value) {
                text_4.style.borderColor = "red";
            }
            if ("" == text_5.value) {
                text_5.style.borderColor = "red";
            }
            if ("" == text_5.value) {
                text_5.style.borderColor = "red";
            }
            if ("" == text_6.value) {
                text_6.style.borderColor = "red";
            }
            return document.getElementById('error_item_otp').innerHTML = getCookie('language') == "vi" ? "Mã xác thực không được để trống" : "Verification code is not empty";
        }
        document.getElementById('error_item_otp').innerHTML = "";
        document.getElementById('request-password-reset-form').submit();
    });

    // Lấy danh sách các input field cần xử lý
    const inputFields = document.querySelectorAll('.input-field');

    // Gắn sự kiện "input" cho tất cả input fields
    inputFields.forEach((input, index) => {
        input.addEventListener('input', function () {
            // Khi nhập dữ liệu vào input, tự động focus vào input kế tiếp (nếu có)
            if (index + 1 < inputFields.length && input.value !== "") {
                inputFields[index + 1].focus();
            }
        });

        // Gắn sự kiện "keydown" để xử lý việc nhấn phím Backspace hoặc Delete
        input.addEventListener('keydown', function (event) {
            // Kiểm tra nếu là phím Backspace hoặc Delete và input hiện tại rỗng
            if ((event.key === "Backspace" || event.key === "Delete") && input.value === "") {
                // Tự động focus vào input trước đó (nếu có)
                if (index > 0) {
                    inputFields[index - 1].focus();
                }
            }
        });
    });

    // Tự động focus vào input đầu tiên
    inputFields[0].focus();
</script>
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

    .row {
        margin-right: 35px;
        margin-left: 35px;
    }

    .row.no-print {
        margin: 0;
    }

    .form-control {
        border-color: rgba(0, 0, 0, 0.85);
    }

    .login-logo img {
        width: 45%;
        height: fit-content;
    }
</style>