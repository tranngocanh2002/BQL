<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = Yii::t('backend', 'Create Management User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Management User'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-role-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
