<?php

use common\models\BuildingCluster;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserRoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$buildingClusters = BuildingCluster::find()->where(['is_deleted'=> BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);

$this->title = Yii::t('backend', 'Auth Groups');
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
                        'name',
                        'description',
                        [
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

                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

