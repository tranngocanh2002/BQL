<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backend', 'Update Building Cluster: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Building Cluster'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="user-role-update">

    <?= $this->render('_form', [
        'model' => $model,
        'allTagRoles' => $allTagRoles,
        'authItemTags' => $authItemTags
    ]) ?>

</div>
