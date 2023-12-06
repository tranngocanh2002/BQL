<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backend', 'Create User Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-role-create">

    <?= $this->render('_form', [
        'model' => $model,
        'allController' => $allController
    ]) ?>

</div>
