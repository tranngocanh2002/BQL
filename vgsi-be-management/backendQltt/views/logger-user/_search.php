<?php

use common\models\User;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backendQltt\models\AnnouncementCampaignSearch */
/* @var $form yii\widgets\ActiveForm */
$userManagement = User::findAll(['status' => 10]);
$userManagement = ArrayHelper::map($userManagement, 'management_user_id', 'full_name');
$action = [
    'Kích hoạt' => Yii::t('backendQltt', 'Kích hoạt'),
    'Dừng hoạt động' => Yii::t('backendQltt', 'Dừng hoạt động'),
    'Thêm mới' => Yii::t('backendQltt', 'Thêm mới'),
    'Chỉnh sửa' => Yii::t('backendQltt', 'Chỉnh sửa'),
    'Xóa' => Yii::t('backendQltt', 'Xóa'),
    'Tải lên danh sách' => Yii::t('backendQltt', 'Tải lên danh sách'),
    'Xuất danh sách' => Yii::t('backendQltt', 'Xuất danh sách'),
    'Đặt lại mật khẩu' => Yii::t('backendQltt', 'Đặt lại mật khẩu'),
    'Quên mật khẩu' => Yii::t('backendQltt', 'Quên mật khẩu'),
    'Đổi mật khẩu' => Yii::t('backendQltt', 'Đổi mật khẩu'),
];
$object = [
    'Xác thực' => Yii::t('backendQltt', 'Xác thực'),
    'Mẫu tin tức' => Yii::t('backendQltt', 'Mẫu tin tức'),
    'Nhóm quyền' => Yii::t('backendQltt', 'Nhóm quyền'),
    'Người dùng' => Yii::t('backendQltt', 'Người dùng'),
    'Dự án' => Yii::t('backendQltt', 'Dự án'),
    'Tin tức' => Yii::t('backendQltt', 'Tin tức'),
    'Tài khoản' => Yii::t('backendQltt', 'Tài khoản'),
];
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

    .form-control:focus {
        border-color: #ccc;
    }
</style>

<div class="user-logger-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'type' => ActiveForm::TYPE_INLINE,
    ]); ?>
    <?= $form->field($model, 'created_at')->widget(\kartik\date\DatePicker::className(), [
        'options' => ['placeholder' => Yii::t('backendQltt', 'Từ ngày') . '...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>

    <?= $form->field($model, 'request')->widget(\kartik\date\DatePicker::className(), [
        'options' => ['placeholder' => Yii::t('backendQltt', 'Đến ngày') . '...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>


    <?= $form->field($model, 'management_user_id', [
        'addon' => [

            'append' => ['content' => '<i class="fa fa-fw fa-search"></i>'],
        ],
        'inputOptions' => [
            'style' => ' border-right: 0;',
            'maxlength' => 255,
        ],
    ]) ?>
    <?= $form->field($model, 'object')->widget(Select2::class, [
        'data' => $object,
        'options' => [
            'placeholder' => Yii::t('backendQltt', 'Đối tượng'),
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
    <?= $form->field($model, 'action')->widget(Select2::class, [
        'data' => $action,
        'options' => [
            'placeholder' => Yii::t('backendQltt', 'Thao tác'),
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
    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>