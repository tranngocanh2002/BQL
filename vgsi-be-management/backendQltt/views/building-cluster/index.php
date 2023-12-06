<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\BuildingCluster;

/* @var $this yii\web\View */
/* @var $searchModel backendQltt\models\BuildingClusterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backendQltt', 'Danh sách dự án');
$totalCount = $dataProvider->getTotalCount();
setcookie("totalCount", $totalCount);
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(' input[name="BuildingClusterSearch[name]"]').attr('placeholder',
            '<?= Yii::t('backendQltt', 'Tên dự án') ?>');
        $(' input[name="BuildingClusterSearch[domain]"]').attr('placeholder',
            '<?= Yii::t('backendQltt', 'Domain') ?>');
        $(' input[name="BuildingClusterSearch[address]"]').attr('placeholder',
            '<?= Yii::t('backendQltt', 'Địa chỉ') ?>');
        $(' input[name="BuildingClusterSearch[email]"]').attr('placeholder',
            '<?= Yii::t('backendQltt', 'Email') ?>');

        if ($('td.centerColumn2 i.fa-eye').length && ($('i.fa-edit').length || $('i.fa-trash').length)) {
            $('i.fa.fa-fw.fa-eye.fa-lg').after('<span class="divider" style="color: #000; margin-left: 2px"> |</span>');
        }
        if ($('td.centerColumn2 i.fa-edit').length && ($('i.fa-trash').length)) {
            $('i.fa.fa-fw.fa-edit.fa-lg').after('<span class="divider" style="color: #000; margin-left: 2px"> |</span>');
        }
        $('#refresh_link').click(function() {
            const url = location.href
            location.href = url
        })
    }, false);
</script>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body" style="min-height: 80vh;">
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
                <p style="margin-top: 20px;">
                    <?= Html::a('<img class="icon"  width="36" src="/images/reload.png" alt="" >', ['index'], ['title' => Yii::t('backendQltt', 'Refresh'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) ?>   
                    <?= $this->context->checkPermission('building-cluster', 'create')
                        ? Html::a('<img class="icon" src="/images/plus.png" alt="" width="36">', ['create'], ['id' => 'add_more', 'style' => 'margin-left: 10px;', 'title' => Yii::t('backendQltt', 'Add'), 'data-toggle' => 'tooltip', 'data-placement' => 'top'])
                        : '' ?>
                </p>
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
                            'attribute' => 'name',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ]
                        ],
                        [
                            'attribute' => 'domain',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ]
                        ],
                        [
                            'attribute' => 'email',
                            'value' => function ($model) {
                        if (!empty($model->building_cluster_id)) {
                            $buildingCluster = BuildingCluster::findOne($model->building_cluster_id);
                            return $buildingCluster->email;
                        } else if (!empty($model->email)) {
                            return $model->email;
                        }

                        return '';
                    },
                            'format' => 'raw',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ]
                        ],
                        [
                            'attribute' => 'address',
                            'value' => function ($model) {
                        if (!empty($model->address)) {
                            return $model->address;
                        }

                        return '';
                    },
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
                            return $this->context->checkPermission('building-cluster', 'view')
                                ? Html::a('<i class="fa fa-fw fa-eye fa-lg" style="color: #000000a6"></i>', Yii::$app->urlManager->createUrl(['building-cluster/view', 'id' => $model->id]), ['title' => Yii::t('yii', 'View'),])
                                : '';
                        },
                                'update' => function ($url, $model) {
                            return $this->context->checkPermission('building-cluster', 'update')
                                ? Html::a('<i class="fa fa-fw fa-edit fa-lg" style="color: #016343;"></i>', Yii::$app->urlManager->createUrl(['building-cluster/update', 'id' => $model->id]), ['title' => Yii::t('backendQltt', 'Edit'),])
                                : '';
                        },
                                'delete' => function ($url, $model) {
                            if ($model->id == 1)
                                return Html::a('<i class="fa fa-fw fa-trash fa-lg" style="color: #CCCCCC; cursor: not-allowed"></i>');
                            return $this->context->checkPermission('building-cluster', 'delete')
                                ? Html::a(
                                    '<i class="fa fa-fw fa-trash fa-lg" style="color: #F15A29"></i>',
                                    Yii::$app->urlManager->createUrl(['building-cluster/delete', 'id' => $model->id, 'edit' => 't']),
                                    [
                                        'title' => Yii::t('backendQltt', 'Delete'),
                                        'class' => 'abc',
                                        'data' => [
                                            'confirm' => Yii::t('backendQltt', 'Bạn có chắc muốn xóa dự án này không?'),
                                        ],
                                    ]
                                ) : Html::a('<i class="fa fa-fw fa-trash fa-lg" style="color: #CCCCCC; cursor: not-allowed"></i>');
                        },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
<style>
    ul {
        list-style-type: disc;
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
        const node = document.createTextNode('Tổng số' + ' ' + getCookie('totalCount') + ' dự án');
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);

    } else {
        const para = document.createElement("p");
        const node = document.createTextNode(`Total` + ' ' + getCookie('totalCount') + ' ' + (Number(getCookie('totalCount')) > 1 ? 'projects' : 'project'));
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);
    }

</script>