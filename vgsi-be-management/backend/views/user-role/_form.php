<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?php
                foreach ($allController as $key=>$value){
                    echo '<label class="control-label">'.$key.'</label><br>';
                    if(isset($permission[$key])){
                        echo Html::checkboxList('permission['.$key.']',$permission[$key], $value).'<br>';
                    }else{
                        echo Html::checkboxList('permission['.$key.']', [], $value).'<br>';
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

