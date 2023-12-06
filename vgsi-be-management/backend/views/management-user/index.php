<?php

use common\models\BuildingCluster;
use common\models\User;
use common\models\rbac\AuthGroup;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

$buildingClusters = BuildingCluster::find()->where(['is_deleted'=> BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);

$authGroups = AuthGroup::find()->all();
$authGroups = ArrayHelper::map($authGroups, 'id', 'name');
asort($authGroups);

$this->title = Yii::t('backend', 'Management User');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">
                <p>
                    <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('backend', 'Create'), ['create'], ['id' => 'add_more', 'class' => 'btn btn-primary']) ?>
                </p>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

//                        'id',
                        [
                            'width' => '200px',
                            'attribute' => 'building_cluster_id',
                            'value' => function ($model) {
                                if(!empty($model->buildingCluster)){
                                    return $model->buildingCluster->name;
                                }
                                return '';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => $buildingClusters,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => 'Any status'],
                            'format' => 'raw'
                        ],
                        'email',
                        'phone',
                        'first_name',
                        [
                            'width' => '200px',
                            'attribute' => 'auth_group_id',
                            'value' => function ($model) {
                                if($model->authGroup){
                                    return $model->authGroup->name;
                                }
                                return '';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => $authGroups,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => 'Any status'],
                            'format' => 'raw'
                        ],

                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

