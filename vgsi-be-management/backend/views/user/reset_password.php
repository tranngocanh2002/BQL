<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;

/**
 * @var yii\web\View $this
 * @var common\models\User $model
 * @var yii\widgets\ActiveForm $form
 */

$this->title = Yii::t('backend', 'Reset password {modelClass}: ', [
        'modelClass' => 'User',
    ]) . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Reset password');
?>

<div class="user-create">
    <div class="panel panel-info">
        <div class="panel-heading"><h3 class="panel-title"><?= $this->title ?></h3></div>
        <div class="box ">

            <div class="box-body">

                <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]);
                echo Form::widget([
                    'model' => $model,
                    'form' => $form,
                    'columns' => 1,
                    'attributes' => [
                        'password' => ['type' => Form::INPUT_PASSWORD, 'options' => ['placeholder' => 'Enter password...', 'maxlength' => 100]],
                        'confirm_password' => ['type' => Form::INPUT_PASSWORD, 'options' => ['placeholder' => 'Enter confirm password...', 'maxlength' => 100]],
                    ]
                ]);
                echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Reset') : Yii::t('backend', 'Reset'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
