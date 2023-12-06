<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserRoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Management User Roles');
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
                        'description',
                        'tag',

                        [
                            'class' => 'yii\grid\ActionColumn',
//                            'template' => '{view}'
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

