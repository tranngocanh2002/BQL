<?php

use common\models\BuildingCluster;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserRoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$buildingClusters = BuildingCluster::find()->where(['is_deleted'=> BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);

$this->title = Yii::t('backend', 'Auth Groups');
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">
                <div class="col-xs-12">
                    <p class="pull-right">
                        <?= Html::a(Yii::t('backend', 'Thêm nhóm'), ['create'], ['id' => 'add_more', 'class' => 'btn btn-primary']) ?>
                    </p>
                </div>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    // 'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

//                        'id',
                        'name',
                        // 'description',
                        // [
                        //     'attribute' => 'building_cluster_id',
                        //     'value' => function ($model) {
                        //         if(!empty($model->buildingCluster)){
                        //             return $model->buildingCluster->name;
                        //         }
                        //         return '';
                        //     },
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filter' => $buildingClusters,
                        //     'filterWidgetOptions' => [
                        //         'pluginOptions' => ['allowClear' => true],
                        //     ],
                        //     'filterInputOptions' => ['placeholder' => 'Any status'],
                        //     'format' => 'raw'
                        // ],
                        [
                            'attribute' => 'count_users',
                            'value' => function ($model) {
                                if(is_numeric($model->getManagementUser()->count())){
                                    return $model->getManagementUser()->count();
                                }
                                return '0';
                            },
                            'format' => 'raw'
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => Yii::t('backendQltt', "Thao tác"),
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    return '';
                                },
                                'update' => function ($url, $model) {
                                    return Html::a(Yii::t('yii', 'Sửa'), Yii::$app->urlManager->createUrl(['auth-group/update', 'id' => $model->id]), ['title' => Yii::t('yii', 'Edit'),]
                                    );
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a(Yii::t('yii', 'Xóa'),
                                        Yii::$app->urlManager->createUrl(['auth-group/delete', 'id' => $model->id, 'edit' => 't']),
                                        [
                                            'title' => Yii::t('yii', 'Delete'),
                                            'data' => [
                                                'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
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

