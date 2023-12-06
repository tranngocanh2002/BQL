<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;
use yii\bootstrap\Alert as BootstrapAlert;

?>
<div class="content-wrapper">
    <section class="content-header">
        <?php if (isset($this->blocks['content-header'])) { ?>
        <h1><?= $this->blocks['content-header'] ?></h1>
        <?php } else { ?>
        <h1>
            <?php
                if ($this->title !== null) {
                    echo \yii\helpers\Html::encode($this->title);
                } else {
                    echo \yii\helpers\Inflector::camel2words(
                        \yii\helpers\Inflector::id2camel($this->context->module->id)
                    );
                    echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
                } ?>
        </h1>
        <?php } ?>

        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?php
            if(Yii::$app->session->hasFlash('message')) {
                echo BootstrapAlert::widget([
                    'options' => [
                        'class' => 'alert-success',
                    ],
                    'body' => Yii::$app->session->getFlash('message'),
                ]);
            }
        ?>
        <?= $content ?>
    </section>
</div>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b><?= Yii::t('common', 'Version') ?></b> 1.2
    </div>
    <strong><?= Yii::t('common','Copyright') ?> &copy; <?= date('Y') ?> <a href="http://luci.vn">Luci</a>.</strong>
    <?= Yii::t('common', 'All rights reserved') ?>
</footer>