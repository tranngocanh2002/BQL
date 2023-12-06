<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Building Cluster'), 'url' => ['index']];
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
                        'email',
                        'hotline',
                        'address',
                        'description',
                        'domain',
                        'one_signal_app_id',
                        'one_signal_api_key',
                        'tax_code',
                        'tax_info',
                        'limit_sms',
                        'sms_price',
                        'limit_email',
                        'limit_notify',
                        'link_whether',
                        'email_account_push',
                        'email_password_push',
                        'sms_brandname_push',
                        'sms_account_push',
                        'sms_password_push',
                        'service_bill_template',
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
