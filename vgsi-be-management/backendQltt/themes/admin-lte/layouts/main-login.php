<?php
use dmstr\widgets\Alert;
use yii\bootstrap\Alert as BootstrapAlert;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

dmstr\web\AdminLteAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>
        <?= Html::encode($this->title) ?>
    </title>
    <?php $this->head() ?>
</head>
<style>
    .footer {
        text-align: center;
        position: fixed;
        bottom: 15px;
        width: 100%;
    }

    #change_language {
        width: 140px;
        margin-right: 20px;
        margin-left: auto;
        margin-top: 20px;
        display: flex;
        flex-direction: row;
    }

    .login-logo img {
        width: 50%;
        height: auto;
    }
</style>

<script>
    const changeLanguage = () => {
        var language = document.getElementById("change_language").value;
        if (language == 1) {
            document.cookie = 'language=en; path=/'
        } else {
            document.cookie = 'language=vi; path=/'
        }

        location.reload();
    };
</script>

<body class="login-page <?= \dmstr\helpers\AdminLteHelper::skinClass() ?>">
    <?php if (strpos(Yii::$app->request->url, 'login')) { ?>
        <select id="change_language" class="form-control" name="change_language" aria-invalid="false"
            onchange="changeLanguage()">
            <option value="1" <?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'en')
                echo 'selected'; ?>>
                🇺🇸
                <?= Yii::t('backend', 'English') ?>
            </option>
            <option value="2" <?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'vi')
                echo 'selected'; ?>>
                🇻🇳
                <?= Yii::t('backend', 'Vietnamese') ?>


            </option>


        </select>
    <?php } ?>
    <?php $this->beginBody() ?>
    <?= Alert::widget() ?>
    <?php
    if (Yii::$app->session->hasFlash('error')) {
        echo BootstrapAlert::widget([
            'options' => [
                'class' => 'alert-error',
            ],
            'body' => Yii::$app->session->getFlash('error'),
        ]);
    }
    ?>
    <?= $content ?>
    <div class="footer">
        <?= Yii::t('backendQltt', 'Bản quyền @  2022 Luci') ?>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
<style>
    .login-page {
        background-color: #016343;
        background-image: url("images/lc2222.svg");
        background-repeat: repeat;
        background-size: 39px;
        height: 100%;
    }

    .form-control {
        border-radius: 4px;
    }

    .footer {
        color: #fff;
    }
</style>