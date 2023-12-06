<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementTemplate */

$this->title = Yii::t('backend', 'Update Announcement Template: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Announcement Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="announcement-template-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
