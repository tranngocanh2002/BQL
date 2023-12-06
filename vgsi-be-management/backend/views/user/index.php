<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\User;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\UserSearch $searchModel
 */
$this->title = Yii::t('backend', 'Admin');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'width' => '200px',
                'attribute' => 'username',
                'value' => function($model, $key, $index, $widget) {
                    return Html::a($model->username, ['user/view', 'id' => $model->id]);
                },
                'format' => 'raw'
            ],
            [
                'width' => '200px',
                'attribute' => 'phone',
            ],
            [
                'width' => '200px',
                'attribute' => 'email',
                'format' => 'email'
            ],
            [
                'width' => '200px',
                'attribute' => 'status',
                'value' => function ($model, $key, $index, $widget) {
                    return ($model->status == User::STATUS_ACTIVE) ? Yii::t('backend', 'Active') : Yii::t('backend', 'InActive');
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => [User::STATUS_ACTIVE => Yii::t('backend', 'Active'), User::STATUS_DELETED => Yii::t('backend', 'InActive')],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Any status'],
                'format' => 'raw'
            ],
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['user/view', 'id' => $model->id, 'edit' => 't']), ['title' => Yii::t('yii', 'Edit'),]
                        );
                    },
                    'delete' => function ($url, $model) {
                        if ($model->id == 1)
                            return '';
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Yii::$app->urlManager->createUrl(['user/delete', 'id' => $model->id, 'edit' => 't']), ['title' => Yii::t('yii', 'Edit'),]
                        );
                    }
                ],
            ],
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . ' </h3>',
            'type' => 'info',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('backend', 'Create'), ['create'], ['class' => 'btn btn-success']),
            'showFooter' => false
        ],
    ]);
    Pjax::end();
    ?>

</div>
