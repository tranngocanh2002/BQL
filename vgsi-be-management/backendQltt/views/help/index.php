<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\HelpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Helps');
$this->params['breadcrumbs'][] = $this->title;

$list_data = \common\models\HelpCategory::find()->all();
$list_category = \yii\helpers\ArrayHelper::map($list_data, 'id', 'name');
asort($list_category);
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
                        'title',
                        'title_en',
                        [
                            'attribute' => 'content',
                            'format' => 'html',
                            'value' => function ($data) {
                                return '<span>'.$data->content.'</span>';

                            }
                        ],
//                        'medias:ntext',
//                        'help_category_id',
                        [
                            'attribute' => 'help_category_id',
                            'format' => 'raw',
                            'filter' => $list_category,
                            'value' => function ($data) {
                                if ($data->helpCategory) {
                                    return $data->helpCategory->name;
                                }
                                return '';
                            }
                        ],
                        //'is_deleted',
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
