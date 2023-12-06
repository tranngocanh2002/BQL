<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backend', 'Update Auth Group');
?>
<div class="user-role-update">

    <?= $this->render('_form', [
        'model' => $model,
        'allRoles' => $allRoles,
        'permissionChild' => $permissionChild
    ]) ?>

</div>
