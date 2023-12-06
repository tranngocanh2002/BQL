<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backendQltt\models\AnnouncementCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Announcement Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">
    <p>
        <?= Html::a(Yii::t('backend', 'Create Announcement Category'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'label_color',
            'building_cluster_id',
            'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',
            //'type',
            //'name_en',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
            </div>
        </div>
    </div>
</div>
