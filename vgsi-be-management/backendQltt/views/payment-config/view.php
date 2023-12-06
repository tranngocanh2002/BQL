<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\PaymentConfig;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentConfig */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Payment Configs'), 'url' => ['index']];
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
                'attribute' => 'gate',
                'value' => function ($model) {
                    return \common\models\PaymentConfig::$gate_lst[$model->gate];
                }
            ],
            'receiver_account',
            'merchant_name',
            'partner_code',
            'access_key',
            'secret_key',
            'merchant_id',
            'merchant_pass',
            'checkout_url',
            'checkout_url_old',
            'return_url',
            'cancel_url',
            'notify_url',
            'note',
//            'status',
            [
                'attribute' => 'status',
                'value' => ($model->status) ? Yii::t('backend', 'Active') : Yii::t('backend', 'InActive'),
            ],
            'created_at:datetime',
            'updated_at:datetime',
//            'created_by',
//            'updated_by',
        ],
    ]) ?>
            </div>
        </div>
    </div>
</div>
