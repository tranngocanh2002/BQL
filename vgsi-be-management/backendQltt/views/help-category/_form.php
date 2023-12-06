<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HelpCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
    .sp-krajee.sp-replacer {
        margin: 0;
        padding: 0;
        border: 0;
        width: 45px !important;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'color')->widget(\kartik\color\ColorInput::classname(), [
                    'options' => ['placeholder' => 'Select color ...'],
                ]); ?>

                <?= $form->field($model, 'order')->textInput(['type' => 'Number']) ?>

                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
