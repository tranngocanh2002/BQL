<?php
use common\models\UserRole;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use yii\jui\DatePicker;
use common\models\User;

/**
 * @var yii\web\View $this
 * @var common\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
$new_list_role = UserRole::find()->where(['<>','id','1'])->all();
$roles = ArrayHelper::map($new_list_role, 'id', 'name');

$form = ActiveForm::begin([
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => ['/user/profile', 'action' => 'update-profile'],
    'formConfig' => ['labelSpan' => 4],
]);

if ($model->birthday && gettype($model->birthday) === 'string') {
    $model->birthday = strtotime($model->birthday);
}

echo Form::widget([
    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [
        'code_user' => [
            'type' => Form::INPUT_TEXT,
            'options' => ['placeholder' => Yii::t('backendQltt', 'Enter Code User'), 'maxlength' => 10, 'disabled' => $model->code_user]
        ]
    ]
]);

echo Form::widget([
    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [
        'full_name' => [
            'type' => Form::INPUT_TEXT,
            'options' => ['placeholder' => 'Enter Full Name...', 'maxlength' => 100, 'disabled' => $model->full_name]
        ],
        'email' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Email...', 'maxlength' => 100, 'disabled' => true]],
        'phone' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Phone...', 'maxlength' => 10]],
    ]
]);

echo $form
    ->field($model, 'birthday')
    ->widget(DatePicker::class, [
        'dateFormat' => 'dd-MM-yyyy',
        'options' => ['class' => 'form-control'],
        'clientOptions' => [
            'changeYear' => true,
            'changeMonth' => true,
            'yearRange' => '-50:-12',
            'altFormat' => 'yy-mm-dd',
        ]
    ])
    ->textInput([
        'placeholder' => \Yii::t('app', 'dd/mm/yyyy'),
        'value' => $model->birthday ? date('d/m/Y', $model->birthday) : '',
        'disabled' => $model->birthday,
    ]);

echo $form
    ->field($model, 'sex')
    ->dropDownList(User::getSexList(), [
        'prompt' => 'Select an option',
        'disabled' => true,
    ]);

echo $form
    ->field($model, 'role_id')
    ->dropDownList($roles, [
        'prompt' => 'Select an option',
        'disabled' => true,
    ]);

echo '<input type="text" id="avatar" name="User[avatar]" value="" class="form-control" hidden="hidden" />';
echo '<div class="form-group highlight-addon field-user-birthday">
            <label class="control-label has-star col-md-4" for="user-birthday"></label>
            <div class="col-md-8">
                ' . Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) . '
                <div class="help-block"></div>
            </div>
        </div>';
ActiveForm::end();
?>