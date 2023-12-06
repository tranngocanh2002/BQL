<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementTemplate */

$this->title = Yii::t('backendQltt', 'Tạo mới mẫu tin tức');
?>
<div class="announcement-template-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
