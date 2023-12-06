<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\BuildingCluster;
use yii\helpers\ArrayHelper;
use common\models\rbac\AuthGroup;
use yii\helpers\BaseVarDumper;
use yii\helpers\VarDumper;

$buildingClusters = BuildingCluster::find()->where(['is_deleted'=> BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);

?>

<style>
    .d-none {
        display: none;
    }
</style>

<script>
    function selectAll(tagName = 'DEFAULT') {
        let inputSelectAll = document.getElementById(tagName);
        
        let inputs = document.querySelectorAll(`[data-role=${tagName}]`)
        inputs.forEach(e => {
            if (!inputSelectAll.checked) {
                e.checked = !e.checked
            } else {
                e.checked = true;
            }
        })
    }

    function checkInput(element, tagName = 'DEFAULT') {
        let inputSelectAll = document.querySelector(`[data-role-select-all=${tagName}]`)
        let inputs = document.querySelectorAll(`[data-role=${tagName}]`)
        let totalChecked = 0;

        inputs.forEach(e => {
            if (e.checked) totalChecked ++
        })

        inputSelectAll.checked = inputs.length == totalChecked
    }
</script>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(); ?>
                <div class="col-sm-12">
                    <h4 style="margin-bottom: 40px;"><strong><?= Yii::t('backend', 'Thông tin') ?></strong></h4>
                </div>
                <div class="col-xs-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-xs-6">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6, 'maxlength' => true]) ?>
                </div>

                <?= $form->field($model, 'building_cluster_id', [
                    'options' => [
                        'class' => 'd-none',
                    ]
                ])->dropDownList($buildingClusters) ?>

                <?= $form->field($model, 'type', [
                    'options' => [
                        'class' => 'd-none',
                    ]
                ])->dropDownList(AuthGroup::$type_list) ?>

                <?php
                    $result = [];
                    foreach ($allRoles as $role) {
                        $type = !empty($role['tag']) ? $role['tag'] : 'DEFAULT';;
                        if (!isset($result[$type])) {
                            $result[$type] = [];
                        }
                        $result[$type][] = $role;
                    }

                    foreach ($result as $tagname => $roles) {
                        echo "<div class='col-xs-12'>
                            <label class='control-label' style='font-size: 18px;'>" . $tagname . "</label>
                            <label class='control-label pull-right'> <input type='checkbox' id='$tagname' data-role-select-all='" . $tagname . "' onchange='selectAll(`" . $tagname . "`)'> " . Yii::t('backend', 'Tất cả') . "</label>
                        </div>";
                        foreach ($roles as $key => $role) {
                            $className = $key === 0 ? 'first' : '';
                            echo $form->field($model, 'data_role', [
                                'options' => [
                                    'class' => "col-xs-3 $className",
                                
                                ]
                            ])->checkbox([
                                'class' => 'my-checkbox',
                                'data-role' => $tagname,
                                'value' => $role['name'],
                                'name' => 'permission[]',
                                'label' => $role['name'],
                                'checked' => in_array($role['name'], $permissionChild),
                                'onchange' => "checkInput(this,'" . $tagname . "')"
                            ]);
                        }
                        
                    }
                ?>
                <div class="form-group col-xs-12 text-center">
                    <a href="/auth-group" class="btn btn-default"><?= Yii::t('backend', 'Cancel') ?></a>
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Thêm nhóm quyền') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

