<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementCampaign */

$this->title = Yii::t('backend', 'Chỉnh sửa tin tức');
// $this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Tin tức'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
// $this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="announcement-campaign-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
