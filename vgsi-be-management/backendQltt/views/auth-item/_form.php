<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use common\models\rbac\AuthItemWeb;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .div-per{
        border: solid 1px #ddd;
        padding: 20px;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'tag')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                <hr>
                <?php
                echo '<label class="control-label" style="font-size: 25px;color:#000">Permission Api</label><br>';
                echo '<div class="div-per">';
                $key = [];
                foreach ($allPermission as $permission) {
                    list($a, $b, $c) = explode('/', $permission->name);
                    if(empty($key[$b])){
                        if(!empty($key)){
                            echo "<br><br>";
                        }
                        $key[$b] = $b;
                        echo '<label class="control-label" style="font-size: 18px;color:#989898">' . $b . '</label><br>';
                    }
                    if (in_array($permission->name, $permissionChild)) {
                        echo ' <label> <input type="checkbox" name="permission[]" value="' . $permission->name . '" checked> ' . $c . ' </label> ';
                    } else {
                        echo ' <label> <input type="checkbox" name="permission[]" value="' . $permission->name . '"> ' . $c . ' </label> ';
                    }
                }
                echo '</div>'
                ?>
                <br>
                <hr>
                <?php
                echo '<label class="control-label" style="font-size: 25px;color:#000">Permission Web</label><br>';
                echo '<div class="div-per">';
                $allRoleWeb = AuthItemWeb::find()->all();
                $arrDateWeb = (!empty($model->data_web)) ? Json::decode($model->data_web) : [];
                foreach ($allRoleWeb as $role_web) {
                    if (in_array($role_web->code, $arrDateWeb)) {
                        echo ' <label> <input type="checkbox" name="permission_web[]" value="' . $role_web->code . '" checked> ' . $role_web->code . ' </label> ';
                    } else {
                        echo ' <label> <input type="checkbox" name="permission_web[]" value="' . $role_web->code . '"> ' . $role_web->code . ' </label> ';
                    }
                }
                echo '</div>';
                ?>

                <div class="form-group" style="margin-top: 10px;">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

