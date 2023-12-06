<?php

use common\models\BuildingCluster;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;

$buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\PaymentConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Payment Configs');
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

                        'id',
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
                        [
                            'width' => '200px',
                            'attribute' => 'gate',
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => \common\models\PaymentConfig::$gate_lst,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'value' => function ($model, $key, $index, $widget) {
                                $gate_lst = \common\models\PaymentConfig::$gate_lst;
                                return (!empty($gate_lst[$model->gate])) ? $gate_lst[$model->gate] : '';
                            },
                            'filterInputOptions' => ['placeholder' => 'Any status'],
                            'format' => 'raw'
                        ],
                        'receiver_account',
                        'merchant_id',
                        'merchant_name',
                        'partner_code',
                        //'merchant_pass',
                        //'checkout_url:url',
                        //'return_url:url',
                        //'cancel_url:url',
                        //'notify_url:url',
                        //'note',
                        //'status',
                        //'created_at',
                        //'updated_at',
                        //'created_by',
                        //'updated_by',

                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
