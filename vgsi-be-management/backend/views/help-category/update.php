<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HelpCategory */

$this->title = Yii::t('backend', 'Update Help Category: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Help Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="help-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
