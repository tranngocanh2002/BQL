<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backendQltt', 'Chỉnh sửa nhóm quyền');
?>
<div class="user-role-update">

    <?= $this->render('_form', [
        'model' => $model,
        'allController' => $allController,
        'permission' => $permission
    ]) ?>

</div>
