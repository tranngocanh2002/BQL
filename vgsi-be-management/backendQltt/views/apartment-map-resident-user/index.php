<?php
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\BuildingCluster;
use common\models\Apartment;
use common\models\ResidentUser;
use yii\helpers\ArrayHelper;
use common\models\ApartmentMapResidentUser;
use yii\helpers\VarDumper;
use common\helpers\CUtils;

/* @var $this yii\web\View */
/* @var $searchModel backendQltt\models\ApartmentMapResidentUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backendQltt', 'App cư dân');

$buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);

$apartments = Apartment::find()->all();
$apartments = ArrayHelper::map($apartments, 'id', 'name');
asort($apartments);
$totalCount = $dataProvider->getTotalCount();
setcookie("totalCount", $totalCount);
?>

<div class="row">
    <div class="col-xs-12">
        <div class="apartment-map-resident-user-index box box-primary">

            <div class="box-body table-responsive">
                <?= $this->render('_search', ['model' => $searchModel]); ?>
                <p style="margin-top: 20px;">
                <?= Html::a('<img class="icon"  width="36" src="/images/reload.png" alt="" >', ['index'], ['title' => Yii::t('backendQltt', 'Refresh'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) ?>                </p>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{items}\n{pager}",
                    'pager' => [
                        'options' => [
                            'id' => '123',
                            'class' => 'pagination',
                        ],
                        'prevPageLabel' => Html::tag('i', "", ['class' => 'fa fa-angle-left', 'id' => 'left']),
                        'nextPageLabel' => Html::tag('i', "", ['class' => 'fa fa-angle-right']),
                        'hideOnSinglePage' => false,
                        'maxButtonCount' => 9,
                    ],
                    //'summary' => "<span class='summary'>Tổng số $totalCount người sử dụng</span>",
                
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ],
                        ],
                        [
                            'width' => '250px',
                            'attribute' => 'resident_user_first_name',
                            'label' => Yii::t('backendQltt', 'Họ'),
                            'value' => function ($model) {
                                                if ($model['resident_user_phone']) {
                                                    $residentUser = ResidentUser::findOne(['phone' => $model['resident_user_phone']]);
                                                    if (!empty($residentUser)) {
                                                        return $residentUser->first_name;
                                                    }
                                                }
                                                return $model['resident_user_first_name'] ?? '';
                                            },
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ],
                        ],
                        [
                            'width' => '250px',
                            'attribute' => 'resident_user_last_name',
                            'label' => Yii::t('backendQltt', 'Tên'),
                            'value' => function ($model) {
                                                if ($model['resident_user_phone']) {
                                                    $residentUser = ResidentUser::findOne(['phone' => $model['resident_user_phone']]);
                                                    if (!empty($residentUser)) {
                                                        return $residentUser->last_name;
                                                    }
                                                }
                                                return $model['resident_user_last_name'] ?? '';
                                            },
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ],
                        ],
                        [
                            'width' => '220px',
                            'attribute' => 'resident_user_phone',
                            'label' => Yii::t('backendQltt', 'Số điện thoại'),
                            'value' => function ($model) {
                                                return $model['resident_user_phone'] || $model['resident_user_phone'] != '' ? CUtils::validateMsisdn($model['resident_user_phone'], 1) : '';
                                            },
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ],
                        ],
                        [
                            'width' => '220px',
                            'attribute' => 'type',
                            'label' => Yii::t('backendQltt', 'Loại tài khoản'),
                            'value' => function ($model) {
                                                return ($model['ru'] > 0) ? Yii::t('backendQltt', 'Cư dân') : Yii::t('backendQltt', 'Người không cư chú');
                                            },
                            'headerOptions' => [
                                'class' => 'centerColumn'
                            ],
                        ],
                        [
                            'attribute' => 'apartment_parent_path',
                            'headerOptions' => [
                                'class' => 'centerColumn2'
                            ],
                            'contentOptions' => ['class' => 'centerColumn2'],

                            'label' => Yii::t('backendQltt', 'Tên dự án/ Số lượng bất động sản'),
                            'value' => function ($model) use ($buildingClusters) {
                                                $user = ApartmentMapResidentUser::find()->where(['resident_user_phone' => $model['resident_user_phone'], 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->one();
                                                return $user && !empty($buildingClusters[$user->building_cluster_id]) ? $buildingClusters[$user->building_cluster_id] . "/" . $user->getTotalApartment($user->building_cluster_id, $model['resident_user_phone']) : '';
                                            },
                            'format' => 'raw'
                        ],

                    ],
                ]); ?>
                <!-- <div class="totalItem">
                    <?= $totalCount ?>
                </div> -->
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

    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        border-left: 0px;
    }


    .form-control:focus {
        border-color: #e8e8e8;

    }

    .form-group.has-success .form-control,
    .form-group.has-success .input-group-addon {
        border-color: #e8e8e8;
    }

    .has-success.highlight-addon .input-group-addon {
        color: #555555;
        background-color: white;
        border-color: #e8e8e8;
    }

    .table>tbody>tr>td {
        background-color: #fff;

        vertical-align: middle;
    }
</style>    
<script>
    var refresh = document.getElementById('refresh_link'); 
    refresh.onclick = function() {
         const url = location.href
         location.href = url
    }
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
        const node = document.createTextNode('Tổng số' + ' ' + getCookie('totalCount') + ' tài khoản');
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);

    } else {
        const para = document.createElement("p");
        const node = document.createTextNode(`Total` + ' ' + getCookie('totalCount') + ' ' + (Number(getCookie('totalCount')) > 1 ? 'accounts' : 'account'));
        para.className = 'footerCount';
        para.appendChild(node);
        const element = document.getElementById("123");
        const child = document.getElementsByClassName("prev")[0];
        element.insertBefore(para, child);
    }
</script>