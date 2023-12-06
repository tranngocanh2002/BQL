<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this \yii\web\View */
/* @var $directoryAsset string */
/* @var $username string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">LUC</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>            
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <li id="language">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                            <?php
                            if (isset($_COOKIE['language']) && $_COOKIE['language'] != 'en') {
                                ?>
                                <a href="javascript:void(0)" >
                                    <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/flag_vn.png" alt="<?= Yii::t('backend','Vietnamese') ?>">
                                    <span><?= Yii::t('backend','Vietnamese') ?></span>
                                </a>
                                <?php
                            } else {
                                ?>
                                <a href="javascript:void(0)">
                                    <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/flag_uk.png" alt="<?= Yii::t('backend','English') ?>">
                                    <span><?= Yii::t('backend','English') ?></span>
                                </a>
                                <?php
                            }
                            ?>
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="javascript: changeLanguage(2);">
                                    <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/flag_vn.png" alt="<?= Yii::t('backend','Vietnamese') ?>">
                                    <span><?= Yii::t('backend','Vietnamese') ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript: changeLanguage(1);">
                                    <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/flag_uk.png" alt="<?= Yii::t('backend','English') ?>">
                                    <span><?= Yii::t('backend','English') ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $directoryAsset ?>/img/avatar5.png" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= $username ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $directoryAsset ?>/img/avatar5.png" class="img-circle"
                                 alt="User Image"/>
                            <p>
                                <?= $username ?> - Admin
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= Url::to(['/user/view', 'id' => Yii::$app->user->getId()]) ?>" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <?=
                                Html::a(
                                        'Sign out', ['/site/logout'], ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                )
                                ?>
                            </div>
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
