<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementCategory */

$this->title = Yii::t('backend', 'Create Announcement Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Announcement Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="announcement-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
