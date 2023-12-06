<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\User $model
 */

$this->title = Yii::t('backendQltt', 'Chỉnh sửa người dùng');
?>
<div class="user-update">
    <div class="panel panel-info">
        <div class="box ">
            <div class="box-body">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
