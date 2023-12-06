<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backendQltt', 'Thêm mới nhóm quyền');
?>
<div class="user-role-create">

    <?= $this->render('_form', [
        'model' => $model,
        'allController' => $allController
    ]) ?>

</div>
