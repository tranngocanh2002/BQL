<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HelpCategory */

$this->title = Yii::t('backend', 'Create Help Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Help Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
