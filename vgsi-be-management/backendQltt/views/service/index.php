<?php

use common\models\User;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Service;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Services');
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

                        'name',
                        [
                            'attribute' => 'description',
                            'format' => 'html',
                            'value' => function ($data) {
                                return '<span>'.$data->description.'</span>';

                            }
                        ],
                        'base_url',
                        [
                            'attribute' => 'service_type',
                            'format' => 'raw',
                            'filter' => Service::$service_type_list,
                            'value' => function ($data) {
                                return Service::$service_type_list[$data->service_type];

                            }
                        ],
                        [
                            'attribute' => 'type',
                            'format' => 'raw',
                            'filter' => Service::$type_list,
                            'value' => function ($data) {
                                return Service::$type_list[$data->type];

                            }
                        ],
                        [
                            'attribute' => 'type_target',
                            'format' => 'raw',
                            'filter' => Service::$type_target_list,
                            'value' => function ($data) {
                                return Service::$type_target_list[$data->type_target];

                            }
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'filter' => Service::$status_list,
                            'value' => function ($data) {
                                return Service::$status_list[$data->status];

                            }
                        ],
//                        'created_at:datetime',
                        //'updated_at',

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view} {update}'
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
