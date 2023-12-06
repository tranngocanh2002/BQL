<?php

use common\models\User;
use common\models\UserRole;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\UserSearch $searchModel
 */
$this->title = Yii::t('backendQltt', 'Danh sách người dùng');
$userRoles = UserRole::find()->where(['<>','id','1'])->all();
$userRoles = ArrayHelper::map($userRoles, 'id', 'name');
$totalCount = $dataProvider->getTotalCount();
setcookie("totalCount", $totalCount);
?>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#confirm-import').click(function () {
            $('#modal-confirm-import').modal('toggle');
            // $('#modal-import-excel').modal('toggle');
        })

        $('#btn-confirm-import').click(function () {
            $('#modal-confirm-import').modal('hide')
            $('#modal-import-excel').modal('toggle');
        })

        $('#file').change(function () {
            var fileInput = document.getElementById('file');
            var formData = new FormData();
            formData.append("file", fileInput.files[0]);

            $.ajax({
                url: '/user/import-file',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (!response.arrCreateError) {
                        alert(response.message)
                        window.location.reload()
                    } else {
                        $('#modal-import-excel').modal('hide');
                        $('#modal-import-success').modal('toggle');
                        let htmlTableReturn = '<tr>'
                        htmlTableReturn += '<td>' + response.TotalRow + '</td>'
                        htmlTableReturn += '<td>' + response.TotalImport + '</td>'
                        htmlTableReturn += '<td>' + (response.TotalRow - response.TotalImport) + '</td>'
                        htmlTableReturn += '</tr>'
                        $('[data-content=table-return]').append(htmlTableReturn);

                        if (response.arrCreateError.length > 0) {
                            response.arrCreateError.forEach(er => {
                                let htmlTableError = '<tr>'
                                htmlTableError += '<td>' + er.line + '</td>'
                                htmlTableError += '<td>' + er.message + '</td>'
                                htmlTableError += '</tr>'
                                $('[data-content=table-error]').append(htmlTableError);
                            })

                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        })
        $('#refresh_link').click(function() {
            const url = location.href
            location.href = url
        })
        
        $('#modal-import-success').on('hidden.bs.modal', function () {
            window.location.reload()
        });

        $('#download-same-file').click(function () {
            window.location.href = '/user/export-template'
        })

        $('#export-users-list').click(function () {
            window.location.href = '/user/export-file'
        });

        $('input[name="UserSearch[full_name]"]').attr('placeholder', '<?= Yii::t('backendQltt', 'Họ và tên') ?>');
        $('input[name="UserSearch[email]"]').attr('placeholder', '<?= Yii::t('backendQltt', 'Email') ?>');
        $('input[name="UserSearch[code_user]"]').attr('placeholder', '<?= Yii::t('backendQltt', 'Mã nhân viên') ?>');


        if ($('td.centerColumn2 i.fa-eye').length && ($('i.fa-edit').length || $('i.fa-trash').length || $('i.fa-ban').length || $('i.fa-check').length)) {
            $('i.fa.fa-fw.fa-eye.fa-lg').after('<span class="divider" style="color: #000; margin-left: 2px"> |</span>');
        }
        if ($('td.centerColumn2 i.fa-edit').length && ($('i.fa-trash').length || $('i.fa-ban').length || $('i.fa-check').length)) {
            $('i.fa.fa-fw.fa-edit.fa-lg').after('<span class="divider" style="color: #000; margin-left: 2px"> |</span>');
        }
        if ($('td.centerColumn2 i.fa-trash').length && ($('i.fa-ban').length || $('i.fa-check').length)) {
            $('i.fa.fa-fw.fa-trash.fa-lg').after('<span class="divider" style="color: #000; margin-left: 2px"> |</span>');
        }
    }, false);
</script>

<?= $this->render('model_confirm_import') ?>
<?= $this->render('model_import_excel') ?>
<?= $this->render('modal_import_success') ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <?= $this->render('_search', ['model' => $searchModel]); ?>
                <p style="margin-top: 20px;">
                    <?php
                    echo $this->context->checkPermission('user', 'index')
                        ? Html::a('<img class="icon"  width="36" src="/images/reload.png" alt="" >', ['index'], ['style' => 'font-size: 25px; color: #444;', 'title' => Yii::t('backendQltt', 'Refresh'), 'data-toggle' => 'tooltip', 'data-placement' => 'top'])
                        : '';
                    echo $this->context->checkPermission('user', 'create')
                        ? Html::a('<img class="icon"  width="36" src="/images/plus.png" alt="">', ['create'], ['id' => 'add_more', 'style' => 'font-size: 25px; color: #444;', 'title' => Yii::t('backendQltt', 'Add'), 'data-toggle' => 'tooltip', 'data-placement' => 'top'])
                        : '';
                    echo $this->context->checkPermission('user', 'import-file')
                        ? Html::a('<img class="icon"   width="36" src="/images/import.png" alt="">', 'javascript:void(0)', ['id' => 'confirm-import', 'style' => 'font-size: 25px; color: #444;', 'title' => Yii::t('backendQltt', 'Import data'), 'data-toggle' => 'tooltip', 'data-placement' => 'top'])
                        : '';
                    echo Html::a('<img class="icon" width="36" src="/images/export.png" alt="">', 'javascript:void(0)', ['id' => 'download-same-file', 'style' => 'font-size: 25px; color: #444;', 'title' => Yii::t('backendQltt', 'Download the sample import file'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']);
                    echo $this->context->checkPermission('user', 'export-file')
                        ? Html::a('<img class="icon" width="36" src="/images/sign-out.png" alt="">', 'javascript:void(0)', ['id' => 'export-users-list', 'style' => 'font-size: 25px; color: #444;', 'title' => Yii::t('backendQltt', 'Export user list'), 'data-toggle' => 'tooltip', 'data-placement' => 'top'])
                        : '';
                    ?>
                <div class="user-index">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'layout' => "{items}\n{pager}",
                        'pager' => [
                            'options' => ['id' => '123', 'class' => 'pagination'],
                            'prevPageLabel' => Html::tag('i', "", ['class' => 'fa fa-angle-left']),
                            'nextPageLabel' => Html::tag('i', "", ['class' => 'fa fa-angle-right']),
                            'hideOnSinglePage' => false,
                            'maxButtonCount' => 9,
                        ],
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => [
                                    'class' => 'centerColumn'
                                ],
                            ],
                            [
                                'attribute' => 'code_user',
                                'label' => Yii::t('backendQltt', 'Mã nhân viên'),
                                // 'value' => function($model, $key, $index, $widget) {
                                //     return Html::a($model->full_name, ['user/view', 'id' => $model->id]);
                                // },
                                'format' => 'raw',
                                'headerOptions' => [
                                    'class' => 'centerColumn'
                                ]
                            ],
                            [
                                'attribute' => 'full_name',
                                // 'value' => function($model, $key, $index, $widget) {
                                //     return Html::a($model->full_name, ['user/view', 'id' => $model->id]);
                                // },
                                'format' => 'raw',
                                'headerOptions' => [
                                    'class' => 'centerColumn'
                                ]
                            ],
                            [
                                'attribute' => 'email',
                                'format' => 'email',
                                'headerOptions' => [
                                    'class' => 'centerColumn'
                                ],

                            ],
                            [
                                'attribute' => 'status',
                                'label' => Yii::t('backendQltt', 'Trạng thái'),
                                'value' => function ($model, $key, $index, $widget) {
                                    return ($model->status == User::STATUS_ACTIVE) ? Yii::t('backendQltt', 'Đang hoạt động') : Yii::t('backendQltt', 'Dừng hoạt động');
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [User::STATUS_ACTIVE => Yii::t('backendQltt', 'Đang hoạt động'), User::STATUS_DELETED => Yii::t('backendQltt', 'Dừng hoạt động')],
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => 'Trạng thái'],
                                'format' => 'raw',
                                'headerOptions' => [
                                    'class' => 'centerColumn'
                                ]
                            ],
                            [
                                'attribute' => 'role_id',
                                'value' => function ($model, $key, $index, $widget) {
                                    return UserRole::findOne($model->role_id)->name;
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => $userRoles,
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => 'Nhóm quyền'],
                                'format' => 'raw',
                                'headerOptions' => [
                                    'class' => 'centerColumn'
                                ]
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'headerOptions' => [
                                    'class' => 'centerColumn2'
                                ],
                                'contentOptions' => ['class' => 'centerColumn2'],

                                'header' => Yii::t('backendQltt', "Thao tác"),
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return $this->context->checkPermission('user', 'view')
                                            ? Html::a('<i class="fa fa-fw fa-eye fa-lg" style="color: #016343"></i>', Yii::$app->urlManager->createUrl(['user/view', 'id' => $model->id]), ['title' => Yii::t('backendQltt', 'View'),])
                                            : '';
                                    },
                                    'update' => function ($url, $model) {
                                        return $this->context->checkPermission('user', 'update')
                                            ? Html::a('<i class="fa fa-fw fa-edit fa-lg" style="color: #016343;"></i>', Yii::$app->urlManager->createUrl(['user/update', 'id' => $model->id]), ['title' => Yii::t('backendQltt', 'Edit'),])
                                            : "";
                                    },
                                    'delete' => function ($url, $model) {
                                        $message = $model->status == User::STATUS_INACTIVE ? Yii::t('backendQltt', "Bạn có chắc chắn muốn kích hoạt tài khoản người dùng này không?") : Yii::t('backendQltt', 'Are you sure you want to deactivate this user account?');
                                        $viewPermission = $this->context->checkPermission('user', 'delete')
                                            ? Html::a(
                                                '<i class="fa fa-fw fa-trash fa-lg" style="color: #f15a29"></i>',
                                                Yii::$app->urlManager->createUrl(['user/delete', 'id' => $model->id, 'edit' => 't']),
                                                [
                                                    'title' => Yii::t('backendQltt', 'Delete'),
                                                    'data' => [
                                                        'confirm' => Yii::t('backendQltt', 'Bạn có chắc muốn xóa người dùng này không?'),
                                                    ],
                                                ]
                                            ) : '';

                                        $activePermission = $this->context->checkPermission('user', 'inactive')
                                            ? Html::a(
                                                $model->status == User::STATUS_ACTIVE ? '<i class="fa fa-fw fa-ban fa-lg" style="color: #f1292a"></i>' : '<i class="fa fa-fw fa-check fa-lg" style="color: #016343"></i>',
                                                Yii::$app->urlManager->createUrl(['/user/inactive', 'id' => $model->id, 'edit' => 't']),
                                                [
                                                    'data' => [
                                                        'confirm' => Yii::t('backendQltt', $message),
                                                    ],
                                                    'title' => $model->status == User::STATUS_ACTIVE ? Yii::t('backendQltt', 'Dừng kích hoạt') : Yii::t('backendQltt', 'Kích hoạt'),
                                                ]
                                            ) : '';

                                        if ($model->id == 1)
                                            return '';
                                        return $viewPermission . ' ' . $activePermission;
                                    },
                                ],
                            ],
                        ],
                    ])
                        ?>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        border-left: 0px;
    }

    p a {
        margin-right: 10px;
    }
    a{
        cursor: pointer;
    }
</style>
<script>
    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
    const deleteBtn = document.querySelectorAll('.fa.fa-fw.fa-trash.fa-lg');
    const deleteText = document.createTextNode('Xóa');

    const banBtn = document.querySelectorAll('.fa.fa-fw.fa-ban.fa-lg');
    const checkBtn = document.querySelectorAll('.fa.fa-fw.fa-check.fa-lg');

    for (let i = 0; i < deleteBtn.length; i++) {
        deleteBtn[i].addEventListener("click", function () {
            setTimeout(function () {
                if (getCookie('language') == "vi") {
                    const okButton = document.querySelector('button.btn.btn-warning');
                    const deleteButton = document.querySelector('button.btn.btn-default');
                    deleteButton.innerHTML = 'Hủy';
                    okButton.innerHTML = 'Đồng ý';
                }
            }, 200);
        });
    }

    for (let i = 0; i < banBtn.length; i++) {
        banBtn[i].addEventListener("click", function () {
            setTimeout(function () {
                if (getCookie('language') == "vi") {
                    const okButton = document.querySelector('button.btn.btn-warning');
                    const deleteButton = document.querySelector('button.btn.btn-default');
                    deleteButton.innerHTML = 'Hủy';
                    okButton.innerHTML = 'Đồng ý';
                }
            }, 200);
        });
    }

    for (let i = 0; i < checkBtn.length; i++) {
        checkBtn[i].addEventListener("click", function () {
            setTimeout(function () {
                if (getCookie('language') == "vi") {
                    const okButton = document.querySelector('button.btn.btn-warning');
                    const deleteButton = document.querySelector('button.btn.btn-default');
                    deleteButton.innerHTML = 'Hủy';
                    okButton.innerHTML = 'Đồng ý';
                }
            }, 200);
        });
    }

    if (getCookie('language') == "vi") {
        const para = document.createElement("p");
        const node = document.createTextNode('Tổng số' + ' ' + getCookie('totalCount') + ' người dùng');
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);

    } else {
        const para = document.createElement("p");
        const node = document.createTextNode(`Total` + ' ' + getCookie('totalCount') + ' ' + (Number(getCookie('totalCount')) > 1 ? 'users' : 'user'));
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);
    }
</script>