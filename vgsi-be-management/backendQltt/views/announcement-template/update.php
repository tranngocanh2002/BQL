<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementTemplate */

$this->title = Yii::t('backend', 'Chỉnh sửa mẫu tin tức');
?>
<div class="announcement-template-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
