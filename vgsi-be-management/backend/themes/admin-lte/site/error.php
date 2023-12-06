<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<section class="content">

    <div class="error-page">
        <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i><?= $name ?></h2>

        <div class="error-content">
            <h3>
                <?= Yii::t('backend', 'Warning! Something went wrong. ') . $message ?>
            </h3>

            <a class="btn btn-success btn-go-back" href="<?= Yii::$app->homeUrl ?>"><?= Yii::t('backend', 'go back') ?></a>
        </div>
    </div>

</section>
