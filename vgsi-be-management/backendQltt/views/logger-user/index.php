<?php

use common\models\User;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backendQltt\models\AnnouncementCampaignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = Yii::t('backend', 'Danh sách tin tức');


$dataArray = ArrayHelper::toArray($dataProvider);
$dataProvider = new ArrayDataProvider([
    'allModels' => $dataArray,
    'pagination' => [
        'defaultPageSize' => 20,
    ],
]);
$totalCount = $dataProvider->getTotalCount();
setcookie("totalCount", $totalCount);
$action = [
    'Kích hoạt' => Yii::t('backendQltt', 'Kích hoạt'),
    'Hủy kích hoạt' => Yii::t('backendQltt', 'Hủy kích hoạt'),
    'Tạo mới' => Yii::t('backendQltt', 'Tạo mới'),
    'Chỉnh sửa' => Yii::t('backendQltt', 'Cập nhật'),
    'Xóa' => Yii::t('backendQltt', 'Xóa'),
    'Tải lên danh sách' => Yii::t('backendQltt', 'Tải lên danh sách'),
    'Tải xuống danh sách' => Yii::t('backendQltt', 'Tải xuống danh sách'),
];
?>

<?php $this->title = Yii::t('backendQltt', 'User history'); ?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
                <p style="margin-top: 20px;">
                <?= Html::a('<img class="icon"  width="36" src="/images/reload.png" alt="" >', ['index'], ['title' => Yii::t('backendQltt', 'Refresh'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) ?>                    <?php
                    // $this->context->checkPermission('announcement-campaign', 'create')
                    //     ? Html::a('<img class="icon"  width="36" src="/images/plus.png" alt="">', ['create'], ['id' => 'add_more', 'style' => 'margin-left: 10px;'])
                    //     : '' 
                    ?>
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
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => Yii::t('backendQltt', 'Time'),
                            'attribute' => 'created_at',
                            'value' => function ($model) {
                            if (!empty($model['created_at'])) {
                                return Yii::$app->formatter->asDatetime($model['created_at'], 'php:Y-m-d H:i');
                            }

                            return "";
                        },
                            'format' => 'raw',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ]
                        ],
                        [
                            'label' => Yii::t('backendQltt', 'Account log'),
                            'attribute' => 'management_user_id',
                            'value' => function ($model) {
                            if (!empty($model['management_user_id'])) {
                                $managementUser = User::findOne($model['management_user_id']);
                                return $managementUser->full_name ?? "";
                            }

                            return "";
                        },
                            'format' => 'raw',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ]
                        ],
                        [
                            'label' => Yii::t('backendQltt', 'Object'),
                            'attribute' => 'object',
                            'value' => function ($model) {

                            if (isset($_COOKIE['language']) && $_COOKIE['language'] === 'vi') {
                                return $model['object'] ?? "";
                            } else {
                                return $model['object_en'] ?? "";
                            }
                        },
                            'format' => 'raw',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ]
                        ],
                        [
                            'label' => Yii::t('backendQltt', 'Thao tác'),
                            'attribute' => 'action',
                            'value' => function ($model) {
                            if (!empty($model['action'])) {

                                if (isset($_COOKIE['language']) && $_COOKIE['language'] === 'vi') {
                                    return $model['action'] ?? "";
                                } else {
                                    return $model['action_en'] ?? "";
                                }
                            }

                            return "";
                        },
                            'format' => 'raw',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ]
                        ]
                    ],
                ]);
                ?>

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
        $('#refresh_link').click(function() {
            const url = location.href
            location.href = url
        })
        // $('input[name="LoggerUser[created_at]"]').attr('placeholder', '<?= Yii::t('backendQltt', 'Time') ?>');
        $('input[name="LoggerUser[management_user_id]"]').attr('placeholder', '<?= Yii::t('backendQltt', 'Account log') ?>');
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

    if (getCookie('language') == "vi") {
        const para = document.createElement("p");
        const node = document.createTextNode('Tổng số' + ' ' + getCookie('totalCount') + ' bản ghi');
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);

    } else {
        const para = document.createElement("p");
        const node = document.createTextNode(`Total` + ' ' + getCookie('totalCount') + ' ' + (Number(getCookie('totalCount')) > 1 ? 'records' : 'record'));
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);
    }
</script>