<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backend', 'Tạo mới dự án');
?>
<div class="user-role-create">

    <?= $this->render('_form', [
        'model' => $model,
        'allTagRoles' => $allTagRoles,
        'authItemTags' => $authItemTags,
        'managementUserModel' => $managementUserModel,
    ]) ?>

</div>
