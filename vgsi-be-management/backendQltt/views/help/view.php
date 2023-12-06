<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Help */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Helps'), 'url' => ['index']];
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
                        'title',
                        'title_en',
                        'content:ntext',
//                        'medias:ntext',
                        [
                            'attribute' => 'help_category_id',
                            'filter' => false,
                            'format' => 'raw',
                            'value' => function ($data) {
                                if ($data->helpCategory) {
                                    return $data->helpCategory->name;
                                }
                                return '';
                            },
                        ],
                        'order',
//                        'is_deleted',
//                        'created_at',
//                        'updated_at',
//                        'created_by',
//                        'updated_by',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
