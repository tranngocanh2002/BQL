<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementCampaign */

$this->title = Yii::t('backend', 'Tạo mới tin tức');
// $this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Tin tức'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="announcement-campaign-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
