<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ServiceBillTemplate */

$this->title = Yii::t('backend', 'Create Service Bill Template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Service Bill Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-bill-template-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
