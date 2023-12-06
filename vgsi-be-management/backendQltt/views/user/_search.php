<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use common\models\UserRole;
use yii\helpers\ArrayHelper;
use common\models\User;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var common\models\UserSearch $model
 * @var yii\widgets\ActiveForm $form
 */

$roleList = UserRole::find()->where(['<>','id','1'])->all();
$roles = ArrayHelper::map($roleList, 'id', 'name');
$typeList = User::getStatusList();

?>

<style>
    .form-group {
        margin-right: 20px;
        margin-top: 10px;
    }

    .select2-selection.select2-selection--single {
        min-width: 190px;
    }

    .select2-selection.select2-selection--single {
        display: flex;
        align-items: center;
    }

    span#select2-usersearch-role_id-container {
        display: table;
        margin: 0 auto 0 0;
    }

    span#select2-usersearch-status-container {
        display: table;
        margin: 0 auto 0 0;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-right: inherit;
    }
</style>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'type' => ActiveForm::TYPE_INLINE,
    ]); ?>

    <?= $form->field($model, 'full_name', [
        'addon' => [

            'append' => ['content' => '<i class="fa fa-fw fa-search"></i>'],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>
    <?= $form->field($model, 'email', [
        'addon' => [
            // 'prepend' => [
            //     'content' => '<i class="fa fa-fw fa-info-circle"></i>',
            //     'options' => ['style' => 'border-right: none;'],
            // ],
            'append' => [
                'content' => '<i class="fa fa-fw fa-search"></i>',
                'options' => ['style' => ''],
            ],
        ],
        'inputOptions' => [
            'style' => 'border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>
    <?= $form->field($model, 'role_id')->widget(Select2::class, [
        'data' => $roles,
        'options' => [
            'placeholder' => Yii::t('backendQltt', 'Nhóm quyền'),
            'style' => 'min-width: 200px'
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        'pluginEvents' => [
            'change' => 'function() { console.log("Option selected"); }',
        ],
    ])
        ?>
    <?= $form->field($model, 'status')->widget(Select2::class, [
        'data' => $typeList,
        'options' => [
            'placeholder' => Yii::t('backendQltt', 'Trạng thái'),
            'style' => 'min-width: 200px'
        ],

        'pluginOptions' => [
            'allowClear' => true,

        ],
        'pluginEvents' => [
            'change' => 'function() { console.log("Option selected"); }',
        ],
    ])
        ?>
    <?= $form->field($model, 'code_user', [
        'addon' => [

            'append' => ['content' => '<i class="fa fa-fw fa-search"></i>'],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>