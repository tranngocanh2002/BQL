<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Service;
/* @var $this yii\web\View */
/* @var $model common\models\Service */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Services'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <p>
                    <?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'name',
                        'description:html',
                        [
                            'attribute' => 'logo',
                            'format'    => 'html',
                            'value' => function ($model) {
                                if (!empty($model->logo)) {
                                    return '<div class="file-default-preview"><img src="' . $model->logo . '" alt=""></div>';
                                }
                                return '';
                            }
                        ],
                        'color',
                        'base_url',
                        'icon_name',
                        [
                            'attribute' => 'service_type',
                            'filter' => false,
                            'value' => function ($model) {
                                return Service::$service_type_list[$model->service_type];
                            }
                        ],
                        [
                            'attribute' => 'type_target',
                            'filter' => false,
                            'value' => function ($model) {
                                return Service::$type_target_list[$model->type_target];
                            }
                        ],
                        [
                            'attribute' => 'type',
                            'filter' => false,
                            'value' => function ($model) {
                                return Service::$type_list[$model->type];
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'filter' => false,
                            'value' => function ($model) {
                                return Service::$status_list[$model->status];
                            }
                        ],
                        'created_at:datetime',
                        'updated_at:datetime',
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
