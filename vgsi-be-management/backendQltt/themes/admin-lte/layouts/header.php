<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $directoryAsset string */
/* @var $username string */

$avatar = $directoryAsset . "/img/avatar5.png";

if (Yii::$app->user->identity->avatar) {
    $avatar = Yii::$app->user->identity->avatar;
}
?>

<header class="main-header">

    <!-- <?= Html::a('<span class="logo-mini">LUC</span><span class="logo-lg" >' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?> -->
    <a class="logo" href="<?= Yii::$app->homeUrl ?>" style="background-color: #016343; ">
        <span class="logo-mini">
            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/logo5.png" alt="">
        </span>
        <span class="logo-lg">
            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/logo6.png" alt="">
        </span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation" style="background-color: white ;">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" style="color: black; ">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <!-- <li id="language">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" style="color: black; " type="button"
                            id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <a href="javascript:void(0)">
                                <i class="fa fa-fw fa-bell" style="color: black; "></i>
                            </a>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <span class="dropdown-header">
                                <?= Yii::t('backendQltt', 'Thông báo') ?>
                            </span>

                            <div class="dropdown-body">
                                <img src="/images/empty.png" alt="">
                            </div>
                            <div class="dropdown-footer">
                                <?= Yii::t('backendQltt', 'No data') ?>
                            </div>

                        </div>
                    </div>
                </li> -->

                <li class="dropdown user user-menu" style="min-width: 180px;">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                        style="display: flex; flex-direction: row; align-items: center; justify-content: end">
                        <div class="user-name">
                            <div class="hidden-xs" style="font-size: 18px;">
                                <span style="color:#a0a2a3;">Hi,</span>
                                <?= $username ?>
                            </div>
                            <div class="hidden-xs"
                                style="font-size: 10px;background-color:#ffe58f;width: fit-content;float: right;">
                                <?= $permissionName ?>
                            </div>
                        </div>
                        <img src="<?= $avatar ?>" class="user-image" alt="User Image" style="object-fit: cover;" />
                    </a>
                    <ul class="dropdown-menu custom-drop">
                        <li>
                            <a href="<?= Url::to(['/user/profile']) ?>" class="btn btn-default btn-flat"
                                style="text-align: left;">
                                <i class="fa fa-fw fa-user"></i>
                                <?= Yii::t('backendQltt', 'Profile') ?>
                            </a>
                        </li>
                        <li>
                            <?= Html::a('<i class="fa fa-fw fa-sign-out"></i>' . ' ' . Yii::t('backendQltt', 'Sign out'), ['/site/logout'], ['data-method' => 'post', 'class' => 'btn btn-default btn-flat', 'style' => 'text-align: left;']) ?>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>
    </nav>
</header>
<script>
    function changeLanguage(language) {
        if (language == 1) {
            document.cookie = 'language=en; path=/'
        } else {
            document.cookie = 'language=vi; path=/'
        }

        location.reload();
    }
</script>
<style>
    .navbar-nav>.user-menu>.dropdown-menu {
        max-width: 180px;
    }

    .logo-lg img {
        width: 80%;
    }

    .navbar-nav>.user-menu .user-image {
        width: 45px;
        height: 45px;
    }

    /* .main-sidebar {
        padding-top: 70px;
    } */

    .main-header {
        min-height: 73px;
    }

    .main-header .logo .logo-lg {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .main-header .sidebar-toggle {
        padding: 25px 15px;
    }

    .user-name {
        margin-right: 10px;
        text-align: right;
        color: #000;
    }

    .dropdown {
        min-height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-mini.sidebar-collapse .main-header .logo {
        width: 100px !important;
        align-items: center;
        display: flex;
        justify-content: center;
    }

    .sidebar-mini.sidebar-collapse .main-header .navbar {
        margin-left: 100px !important;
    }

    .logo {
        position: fixed !important;
        top: 0;
        left: 0;
    }

    .sidebar-mini:not(.sidebar-mini-expand-feature).sidebar-collapse .sidebar-menu>li:hover>a>.pull-right-container {

        background-color: transparent;
    }

    .sidebar-mini.sidebar-collapse .sidebar-menu>li>a>span {
        border-radius: 4px;
        padding-left: 0px !important;
        padding-right: 0px !important;
    }

    .modal-backdrop {
        z-index: -1;
    }

    div.dropdown-menu.custom-drop {
        right: 0;
        left: auto;
        min-height: 200px;
        min-width: 336px;
    }


    .dropdown-header {

        font-size: 14px;
        text-align: center;
        border: 0px;
        border-bottom: 1px;
        border-bottom-style: solid;
        padding-bottom: 8px;
        border-bottom-color: #d2d6de;

    }

    .dropdown-body {
        padding-top: 12px;
        text-align: center;
        align-items: center;
        display: flex;
        justify-content: center;
        min-height: 180px;

    }

    .dropdown-menu {
        position: absolute;
        top: 110%;
        left: 0;
        z-index: 1000;
        display: none;
        float: left;
        min-width: 180px;
        padding: 5px 0;
        margin: 2px 0 0;
        font-size: 14px;
        text-align: left;
        list-style: none;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ccc;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
    }

    @media (max-width: 991px) {
        .navbar-custom-menu>.navbar-nav>li>.dropdown-menu {
            position: absolute;
            right: 0;
            left: auto;
            border: 1px solid #ddd;
            background: #f4f4f4;
        }
    }

    .dropdown-footer {
        text-align: center;
        align-items: center;
        display: flex;
        font-weight: 200;
        justify-content: center;
        padding-bottom: 12px;
    }

    .btn.btn-flat {
        border: 0px;
        padding-top: 10px;
        padding-bottom: 10px;
        background-color: transparent;
    }

    .skin-green-light .main-header .navbar .nav>li>a:hover,
    .skin-green-light .main-header .navbar .nav>li>a:active,
    .skin-green-light .main-header .navbar .nav>li>a:focus,
    .skin-green-light .main-header .navbar .nav .open>a,
    .skin-green-light .main-header .navbar .nav .open>a:hover,
    .skin-green-light .main-header .navbar .nav .open>a:focus,
    .skin-green-light .main-header .navbar .nav>.active>a {
        background: transparent;
        color: transparent;
    }

    .skin-green-light .main-header .navbar .nav>li>a:hover,
    .skin-green-light .main-header .navbar .nav>li>a:active,
    .skin-green-light .main-header .navbar .nav>li>a:focus,
    .skin-green-light .main-header .navbar .nav .open>a,
    .skin-green-light .main-header .navbar .nav .open>a:hover,
    .skin-green-light .main-header .navbar .nav .open>a:focus,
    .skin-green-light .main-header .navbar .nav>.active>a {
        background: transparent;
        color: transparent;
    }

    .nav .open>a,
    .nav .open>a:hover,
    .nav .open>a:focus {
        background: transparent;
        border-color: #337ab7;
    }

    .nav>li>a:hover,
    .nav>li>a:focus {
        background: transparent;
    }

    .nav>li>a:hover,
    .nav>li>a:active,
    .nav>li>a:focus {
        background: transparent;

    }

    .main-header .logo .logo-lg img {
        width: 120px;
        height: 100px;
        max-width: 120px;
        max-height: 100px;
    }

    .main-header .logo .logo-mini img {
        max-width: 60px;
        max-height: 50px;
    }

    .main-sidebar {

        margin-top: 70px;
    }

    .skin-green-light .main-header .logo {
        min-height: 120px
    }

    .main-sidebar {

        background-color: #016343;
    }
</style>