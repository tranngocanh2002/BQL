<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\BuildingCluster;
use yii\helpers\ArrayHelper;
use common\models\rbac\AuthGroup;

$buildingClusters = BuildingCluster::find()->where(['is_deleted'=> BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'building_cluster_id')->dropDownList($buildingClusters) ?>

                <?= $form->field($model, 'type')->dropDownList(AuthGroup::$type_list) ?>

                <?php
                $tag = [];
                foreach ($allRoles as $role){
                    $tagname = ($role->tag) ? $role->tag : 'DEFAULT';
                    if(empty($tag[$tagname])){
                        if(!empty($tag)){
                            echo "<br><br>";
                        }
                        $tag[$tagname] = $tagname;
                        echo '<label class="control-label" style="font-size: 18px;color:#989898">' . $tagname . '</label><br>';
                    }
                    if (in_array($role->name, $permissionChild)) {
                        echo ' <label> <input type="checkbox" name="permission[]" value="' . $role->name . '" checked> ' . $role->name . ' </label> ';
                    } else {
                        echo ' <label> <input type="checkbox" name="permission[]" value="' . $role->name . '"> ' . $role->name . ' </label> ';
                    }
                }
                ?>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

