<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementTemplate */

$this->title = Yii::t('backend', 'Create Announcement Template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Announcement Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="announcement-template-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
