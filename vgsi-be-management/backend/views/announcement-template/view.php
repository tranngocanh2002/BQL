<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementTemplate */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Announcement Templates'), 'url' => ['index']];
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
            'name',
            'name_en',
            [
                'attribute' => 'building_cluster_id',
                'value' => function ($model) {
                    if(!empty($model->buildingCluster)){
                        return $model->buildingCluster->name;
                    }
                    return '';
                }
            ],
            'content_email:html',
            'content_sms:ntext',
//            'content_pdf:html',
            [
                'attribute' => 'image',
                'format'    => 'html',
                'value' => function ($model) {
                    if (!empty($model->image)) {
                        return '<div class="file-default-preview"><img src="' . $model->image . '" alt=""></div>';
                    }
                    return '';
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
