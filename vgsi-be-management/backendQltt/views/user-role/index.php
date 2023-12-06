<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserRoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backendQltt', 'Danh sách nhóm quyền');
$totalCount = $dataProvider->getTotalCount();
setcookie("totalCount", $totalCount);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body" style="min-height: 854px;">
                <p class="pull-right">
                    <?= Html::a(Yii::t('backend', 'Thêm nhóm'), ['create'], ['id' => 'add_more', 'class' => 'btn btn-primary']) ?>
                </p>
                </br>
                </br>
                </br>
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
                            ]
                        ],
                        [
                            'attribute' => 'name',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ]
                        ],
                        [
                            'attribute' => 'count_users',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ],
                            'label' => Yii::t('backendQltt', 'Số người dùng'),
                            'value' => function ($model) {
                                                if (is_numeric($model->getUsers()->count())) {
                                                    return $model->getUsers()->count();
                                                }
                                                return '0';
                                            },
                            'format' => 'raw'
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
                                                    return '';
                                                },
                                'update' => function ($url, $model) {
                                                    return Html::a(
                                                        '<i class="fa fa-fw fa-edit fa-lg" style="color: #016343;"></i>',
                                                        Yii::$app->urlManager->createUrl(['user-role/update', 'id' => $model->id]),
                                                        [
                                                            'title' => Yii::t('backend', 'Edit'),
                                                        ]
                                                    );
                                                },
                                'delete' => function ($url, $model) {
                                                    return Html::a(
                                                        '<span style="color: #000; margin-right: 2px">|</span><i class="fa fa-fw fa-trash fa-lg" style="color: #F15A29"></i>',
                                                        Yii::$app->urlManager->createUrl(['user-role/delete', 'id' => $model->id, 'edit' => 't']),
                                                        [
                                                            'title' => Yii::t('backend', 'Delete'),
                                                            'data' => [
                                                                'confirm' => Yii::t('backendQltt', 'Bạn có chắc muốn xóa nhóm quyền này không?'),
                                                            ],
                                                        ]
                                                    );
                                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>


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

    if (getCookie('language') == "vi") {
        const para = document.createElement("p");
        const node = document.createTextNode(`Tổng cộng` + ' ' + getCookie('totalCount') + ' ' + 'nhóm');
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);

    } else {
        const para = document.createElement("p");
        const node = document.createTextNode(`Total` + ' ' + getCookie('totalCount') + ' ' + (Number(getCookie('totalCount')) > 1 ? 'groups' : 'group'));
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);
    }
</script>