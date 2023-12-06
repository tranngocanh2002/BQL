<?php

use common\models\UserRole;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;

/**
 * @var yii\web\View $this
 * @var common\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
$new_list_role = UserRole::find()->where(['<>','id','1'])->all();
$list_role = ArrayHelper::map($new_list_role, 'id', 'name');


\insolita\wgadminlte\LteBox::begin([
    'type' => \insolita\wgadminlte\LteConst::TYPE_DEFAULT,
    'isSolid' => true,
    'tooltip' => 'this tooltip description',
    'title' => 'Manage users',
]);


$form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_HORIZONTAL
        ]);

echo Form::widget([
    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [
        'email' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Email...', 'maxlength' => 100]],
        'username' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Username...', 'maxlength' => 100]],
        'phone' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Phone...', 'maxlength' => 100]],
    ]
]);
if ($model->isNewRecord) {
    echo $form->field($model, 'password')->passwordInput(['placeholder' => 'Enter password...', 'maxlength' => 100])->label(Yii::t('backend', 'Password'));
    echo $form->field($model, 'confirm_password')->passwordInput(['placeholder' => 'Enter confirm password...', 'maxlength' => 100]);
    echo $form->field($model, 'role_id')->dropDownList($list_role, ['prompt' => 'Select....']);
}

echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
ActiveForm::end();

\insolita\wgadminlte\LteBox::end();
?>
