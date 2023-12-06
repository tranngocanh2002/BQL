<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Help */

$this->title = Yii::t('backend', 'Update Help: {name}', [
    'name' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Helps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="help-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
