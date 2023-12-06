<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backend', 'Update Management: ') . $model->email;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Management User'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->email, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="user-role-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
