<?php

use common\models\BuildingCluster;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\editors\Summernote;
use kartik\editors\Codemirror;

$buildingClusters = BuildingCluster::find()->where(['is_deleted'=> BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);

/* @var $this yii\web\View */
/* @var $model common\models\ServiceBillTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

    <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'building_cluster_id')->dropDownList($buildingClusters, ['prompt' => 'Building Cluster ...', 'id' => 'building_cluster_id']) ?>

                <?= $form->field($model, 'style')->widget(Codemirror::class, [
                    'preset' => Codemirror::PRESET_HTML,
                    'options' => ['placeholder' => 'Edit your code here...']
                ]); ?>

                <?= $form->field($model, 'content')->widget(Codemirror::class, [
                    'preset' => Codemirror::PRESET_HTML,
                    'options' => ['placeholder' => 'Edit your code here...']
                ]); ?>

                <?= $form->field($model, 'sub_content')->widget(Codemirror::class, [
                    'preset' => Codemirror::PRESET_HTML,
                    'options' => ['placeholder' => 'Edit your code here...']
                ]); ?>

                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
