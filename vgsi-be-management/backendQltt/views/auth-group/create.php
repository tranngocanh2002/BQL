<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backend', 'Create Auth Group');
?>
<div class="user-role-create">

    <?= $this->render('_form', [
        'model' => $model,
        'allRoles' => $allRoles,
        'permissionChild' => $permissionChild
    ]) ?>

</div>
