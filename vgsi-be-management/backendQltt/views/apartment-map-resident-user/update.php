<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ApartmentMapResidentUser */

$this->title = Yii::t('backendQltt', 'Update {modelClass}: ', [
    'modelClass' => 'Apartment Map Resident User',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backendQltt', 'Apartment Map Resident Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backendQltt', 'Update');
?>
<div class="apartment-map-resident-user-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
