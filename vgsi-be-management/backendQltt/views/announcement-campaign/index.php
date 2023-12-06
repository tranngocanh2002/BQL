<?php

use common\models\AnnouncementCampaign;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backendQltt\models\AnnouncementCampaignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Danh sách tin tức');

$status = [
    AnnouncementCampaign::STATUS_ACTIVE => Yii::t('backendQltt', 'Công khai'),
    AnnouncementCampaign::STATUS_UNACTIVE => Yii::t('backendQltt', 'Nháp'),
    AnnouncementCampaign::STATUS_PUBLIC_AT => Yii::t('backendQltt', 'Hẹn giờ'),
];
$totalCount = $dataProvider->getTotalCount();
setcookie("totalCount", $totalCount);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
                <p style="margin-top: 20px;">
                    <?= Html::a('<img class="icon"  width="36" src="/images/reload.png" alt="" >', ['index'], ['title' => Yii::t('backendQltt', 'Refresh'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) ?>
                    <?= $this->context->checkPermission('announcement-campaign', 'create')
                        ? Html::a('<img class="icon"  width="36" src="/images/plus.png" alt="">', ['create'], ['id' => 'add_more', 'style' => 'margin-left: 10px;', 'title' => Yii::t('backendQltt', 'Add'), 'data-toggle' => 'tooltip', 'data-placement' => 'top'])
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
                                'class' => 'centerColumn2'
                            ],
                        ],
                        [
                            'attribute' => function ($model) {
                        if (isset($_COOKIE['language']) && $_COOKIE['language'] === 'vi') {
                            return $model->title;
                        } else {
                            return $model->title_en;
                        }
                    },
                            'label' => Yii::t('backendQltt', 'Tiêu đề'),
                            'headerOptions' => [
                                'class' => 'centerColumn2'
                            ],
                        ],
                        [
                            'attribute' => 'app',
                            'label' => Yii::t('backendQltt', 'App'),
                            'value' => function ($model) {
                        return "{$model->total_app_open}/{$model->total_app_send}";
                    },
                            'headerOptions' => [
                                'class' => 'centerColumn2'
                            ],
                        ],
                        [
                            'attribute' => 'status',
                            'label' => Yii::t('backendQltt', 'Tình trạng'),
                            'value' => function ($model, $key, $index, $widget) use ($status) {
                        return $status[$model->status];
                    },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => $status,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => 'Trạng thái'],
                            'format' => 'raw',
                            'headerOptions' => [
                                'class' => 'centerColumn2'
                            ],
                        ],
                        [
                            'attribute' => 'send_event_at',
                            'label' => Yii::t('backendQltt', 'Công khai'),
                            'value' => function ($model, $key, $index, $widget) {
                        return date('H:i, d/m/Y', $model->send_event_at);
                    },
                            'filter' => false,
                            'headerOptions' => [
                                'class' => 'centerColumn2'
                            ],
                        ],
                        [
                            'attribute' => 'updated_at',
                            'label' => Yii::t('backendQltt', 'Cập nhật'),
                            'value' => function ($model, $key, $index, $widget) {
                        return date('H:i, d/m/Y', $model->updated_at);
                    },
                            'filter' => false,
                            'headerOptions' => [
                                'class' => 'centerColumn2'
                            ],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => Yii::t('backendQltt', "Thao tác"),
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ],
                            'contentOptions' => ['class' => 'centerColumn'],
                            'buttons' => [
                                'view' => function ($url, $model) {
                            return $this->context->checkPermission('announcement-campaign', 'view')
                                ? Html::a('<i class="fa fa-fw fa-eye fa-lg" style="color: #016343"></i>', Yii::$app->urlManager->createUrl(['announcement-campaign/view', 'id' => $model->id]), ['title' => Yii::t('backendQltt', 'View'),])
                                : '';
                        },
                                'update' => function ($url, $model) {
                            return $this->context->checkPermission('announcement-campaign', 'update')
                                ? Html::a('<i class="fa fa-fw fa-edit fa-lg" style="color: #016343;"></i>', Yii::$app->urlManager->createUrl(['announcement-campaign/update', 'id' => $model->id]), ['title' => Yii::t('backendQltt', 'Chỉnh sửa'),])
                                : '';
                        },
                                'delete' => function ($url, $model) {
                            // if ($model->id == 1)
                            //     return '';

                            $style = ($model->status === AnnouncementCampaign::STATUS_UNACTIVE || $model->send_event_at >= time()) ? 'color: #f15a29' : ' cursor: not-allowed; text-decoration: none; color: #ababab;';
                            return $this->context->checkPermission('announcement-campaign', 'delete')
                                ? Html::a(
                                    '<i class="fa fa-fw fa-trash fa-lg" ></i>',
                                    Yii::$app->urlManager->createUrl(['announcement-campaign/delete', 'id' => $model->id, 'edit' => 't']),
                                    [
                                        'title' => Yii::t('yii', 'Delete'),
                                        'data' => [
                                            'confirm' => Yii::t('backendQltt', 'Bạn có chắc muốn xóa tin tức này không?'),
                                        ],
                                        'style' => $style,
                                    ]
                                )
                                : '';
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
    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        border-left: 0px;
    }
    a{
        cursor: pointer;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('input[name="AnnouncementCampaignSearch[title]"]').attr('placeholder',
            "<?= Yii::t('backendQltt', 'Tiêu đề') ?>");
        $('input[name="AnnouncementCampaignSearch[title]"]').attr('placeholder',
            "<?= Yii::t('backendQltt', 'Tiêu đề') ?>");

        if ($('td.centerColumn i.fa-eye').length && ($('i.fa-edit').length || $('i.fa-trash').length)) {
            $('i.fa.fa-fw.fa-eye.fa-lg').after('<span class="divider" style="color: #000; margin-left: 2px"> |</span>');
        }
        if ($('td.centerColumn i.fa-edit').length && ($('i.fa-trash').length)) {
            $('i.fa.fa-fw.fa-edit.fa-lg').after('<span class="divider" style="color: #000; margin-left: 2px"> |</span>');
        }
        $('#refresh_link').click(function() {
            const url = location.href
            location.href = url
        })

    }, false);

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
        const node = document.createTextNode('Tổng số' + ' ' + getCookie('totalCount') + ' tin tức');
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);

    } else {
        const para = document.createElement("p");
        const node = document.createTextNode(`Total` + ' ' + getCookie('totalCount') + ' ' + (Number(getCookie('totalCount')) > 1 ? 'news' : 'new'));
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);
    }
</script>