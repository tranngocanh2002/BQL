<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Help */

$this->title = Yii::t('backend', 'Create Help');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Helps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
