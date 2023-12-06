<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backend', 'Thông tin chung');
?>
<div class="user-role-update">

    <?= $this->render('_form', [
        'model' => $model,
        'allTagRoles' => $allTagRoles,
        'authItemTags' => $authItemTags,
        'managementUserModel' => $managementUserModel,
    ]) ?>

</div>
