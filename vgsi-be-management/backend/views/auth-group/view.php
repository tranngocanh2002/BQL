<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Auth Group'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <p>
                    <?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'name',
                        'description',
                        [
                            'attribute' => 'building_cluster_id',
                            'value' => function ($model) {
                                if(!empty($model->buildingCluster)){
                                    return $model->buildingCluster->name;
                                }
                                return '';
                            }
                        ],
                        [
                            'attribute' => 'data_role',
                            'format'    => 'html',
                            'value' => function ($model) {
                                $per_stirng = '';
                                $roles = (!empty($model->data_role)) ? \yii\helpers\Json::decode($model->data_role) : [];
                                foreach ($roles as $per) {
                                    $per_stirng .= Html::tag('span', $per, ['style' => 'color:#989898', 'class' => 'fa fa-check']) .'<br>';
                                }
                                return $per_stirng;
                            }
                        ],
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
