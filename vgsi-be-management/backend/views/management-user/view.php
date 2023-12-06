<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = $model->email;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Management User'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <p>
                    <?= Html::a(Yii::t('backend', 'Reset password'), ['reset-password', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
                        'email',
                        'phone',
                        'first_name',
                        'last_name',
                        [
                            'attribute' => 'avatar',
                            'format'    => 'html',
                            'value' => function ($model) {
                                if (!empty($model->avatar)) {
                                    return '<div class="file-default-preview"><img src="' . $model->avatar . '" alt=""></div>';
                                }
                                return '';
                            }
                        ],
                        [
                            'attribute' => 'auth_group_id',
                            'value' => function ($model) {
                                if ($model->authGroup) {
                                    return $model->authGroup->name;
                                }
                                return '';
                            }
                        ],
                        [
                            'attribute' => 'building_cluster_id',
                            'value' => function ($model) {
                                if(!empty($model->buildingCluster)){
                                    return $model->buildingCluster->name;
                                }
                                return '';
                            }
                        ],
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
