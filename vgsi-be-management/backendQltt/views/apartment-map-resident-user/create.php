<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ApartmentMapResidentUser */

$this->title = Yii::t('backendQltt', 'Create Apartment Map Resident User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backendQltt', 'Apartment Map Resident Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apartment-map-resident-user-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
