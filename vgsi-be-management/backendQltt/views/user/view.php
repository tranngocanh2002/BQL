<?php

use common\models\User;
use common\models\UserRole;
use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var common\models\User $model
 */
$this->title = Yii::t('backendQltt', 'Chi tiết người dùng');
$new_category = UserRole::find()->where(['<>','id','1'])->all();
$list_role = ArrayHelper::map($new_category, 'id', 'name');

$avatar = $model->avatar;
$messageActive = $model->status == User::STATUS_INACTIVE ? 'kích hoạt' : 'dừng kích hoạt';
$message = Yii::t('backendQltt', "Bạn có chắc chắn muốn {$messageActive} tài khoản người dùng này không?");
if (is_null($avatar) || $avatar == "") {
    $avatar = '/images/avatar.png';
}

$status = [
    User::STATUS_INACTIVE => Yii::t('backendQltt', 'Dừng hoạt động'),
    User::STATUS_ACTIVE => Yii::t('backendQltt', 'Đang hoạt động'),
]

    ?>

<style>
    .file-default-preview img {
        height: 200px;
        width: 200px;
        object-fit: cover;
    }

    table th {
        text-align: left !important;
        background-color: #FFF;
        border: none;
    }

    .table-bordered>thead>tr>th,
    .table-bordered>tbody>tr>th,
    .table-bordered>tfoot>tr>th,
    .table-bordered>thead>tr>td,
    .table-bordered>tbody>tr>td,
    .table-bordered>tfoot>tr>td,
    .table-bordered {
        background-color: #FFF;
        border: none;
    }

    select {
        color: #aaa !important;
    }

    option:not(first-child) {
        color: #000;
    }

    #w2>tbody>tr>th {

        color: rgb(164, 164, 170);
        width: 30% !important;
        font-weight: 100;
        height: 40px;

    }



    #w2>tbody>tr>td {
        color: #000;
        font-weight: bold;
    }

    .box {
        border-top: 0px;
    }

    .box-body {
        padding: 20px;
        min-height: 80vh
    }

    h4 {
        display: inline-block;
        float: left;
    }

    .form-control {
        border-radius: 4px;
        box-shadow: none;
        border-color: #d2d6de
    }

    label {

        font-weight: 100;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $("#action").change(function () {
            let actionSelected = $("#action").val();
            if (actionSelected == 1) {
                window.location.href = "/user/update?id=<?= $model->id ?>";
            } else if (actionSelected == 2) {
                // JS Code
                krajeeDialog.confirm("<?= $message ?>", function (result) {
                    if (result) { // ok button was pressed
                        window.location.href = "/user/inactive?id=<?= $model->id ?>";
                    } else { // dialog dialog was cancelled
                        // execute your code for cancellation
                    }
                });
            } else if (actionSelected == 3) {
                $('#modal-default').modal('toggle');
                $("#action").val("");
            } else if (actionSelected == 4) {
                // JS Code
                krajeeDialog.confirm("<?= Yii::t('backendQltt', 'Bạn có chắc muốn xóa người dùng này không?') ?>", function (result) {
                    if (result) { // ok button was pressed
                        window.location.href = "/user/delete?id=<?= $model->id ?>";
                    } else { // dialog dialog was cancelled
                        // execute your code for cancellation
                    }
                });
                $("#action").val("");
            }
        });

        $("select").change(function () {
            if ($(this).val() == "") $(this).css({ color: "#aaa" });
            else $(this).css({ color: "#000" });
        });
    }, false);
</script>

<?= $this->render('_reset_password', ['modelResetPassword' => $modelResetPassword]) ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">
                <div class="row" style="padding: 20px;">
                    <h4><strong>
                            <?= Yii::t('backend', 'Thông tin') ?>
                        </strong></h4>
                    <?php if ($this->context->checkPermission('user', 'update') || $this->context->checkPermission('user', 'inactive') || $this->context->checkPermission('user', 'reset-password') || $this->context->checkPermission('user', 'delete')) { ?>
                        <div class="action" style="height: 50px;">
                            <select class="form-control col-xs-12 pull-right" id="action" style="max-width: 140px; ">
                                <option value="" disable hidden>
                                    <?= Yii::t('backend', 'Thao tác') ?>
                                </option>
                                <?php if ($this->context->checkPermission('user', 'update')) { ?>
                                    <option value="1">
                                        <?= Yii::t('backend', 'Chỉnh sửa') ?>
                                    </option>
                                <?php } ?>
                                <?php if ($this->context->checkPermission('user', 'inactive')) { ?>
                                    <option value="2">
                                        <?= $model->status == User::STATUS_ACTIVE ? Yii::t('backend', 'Dừng kích hoạt') : Yii::t('backend', 'Kích hoạt') ?>
                                    </option>
                                <?php } ?>
                                <?php if ($this->context->checkPermission('user', 'reset-password')) { ?>
                                    <option value="3">
                                        <?= Yii::t('backend', 'Đặt lại mật khẩu') ?>
                                    </option>
                                <?php } ?>
                                <?php if ($this->context->checkPermission('user', 'delete')) { ?>
                                    <option value="4">
                                        <?= Yii::t('backend', 'Xóa người dùng') ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-xs-6">

                    <?=
                        DetailView::widget([
                            'model' => $model,
                            'condensed' => false,
                            'bootstrap' => true,
                            'hover' => true,
                            'mode' => Yii::$app->request->get('edit') == 't' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
                            'attributes' => [
                                [
                                    'attribute' => 'code_user',
                                    'value' => $model->code_user,
                                    'displayOnly' => true
                                ],
                                'full_name',
                                [
                                    'attribute' => 'email',
                                    'value' => $model->email,
                                    'displayOnly' => true
                                ],
                                'phone',
                                [
                                    'attribute' => 'birthday',
                                    'value' => $model->birthday,
                                    'type' => DetailView::INPUT_DATE,
                                    'format' => ['date', 'php:d/m/Y'],
                                ],
                                [
                                    'attribute' => 'sex',
                                    'value' => User::getSexList()[$model->sex],
                                    'type' => DetailView::INPUT_SELECT2,
                                    'widgetOptions' => [
                                        'data' => User::$sex,
                                        'options' => ['placeholder' => 'Select ...'],
                                        'pluginOptions' => ['allowClear' => true, 'width' => '100%'],
                                    ],
                                ],
                                [
                                    'attribute' => 'role_id',
                                    'value' => UserRole::findOne($model->role_id)->name,
                                    'type' => DetailView::INPUT_SELECT2,
                                    'widgetOptions' => [
                                        'data' => $list_role,
                                        'options' => ['placeholder' => 'Select ...'],
                                        'pluginOptions' => ['allowClear' => true, 'width' => '100%'],
                                    ],
                                ],
                                [
                                    'attribute' => 'status',
                                    'value' => $status[$model->status],
                                    'type' => DetailView::INPUT_SELECT2,
                                    'widgetOptions' => [
                                        'data' => $status,
                                        'options' => ['placeholder' => 'Select ...'],
                                        'pluginOptions' => ['allowClear' => true, 'width' => '100%'],
                                    ],
                                ],
                            ],
                            'deleteOptions' => [
                                'url' => ['delete', 'id' => $model->id],
                            ],
                            'enableEditMode' => true,
                            'alertWidgetOptions' => false
                        ])
                        ?>
                </div>


                <div class="file-default-preview"><img src="<?= $avatar ?>" alt=""></div>

            </div>
        </div>
    </div>
</div>