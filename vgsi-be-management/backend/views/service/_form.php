<?php

use common\models\Service;
use dosamigos\ckeditor\CKEditor;
use kartik\widgets\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Service */
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

                <?= $form->field($model, 'description')->widget(CKEditor::className(), [
                    'options' => ['rows' => 6],
                    'preset' => 'basic'
                ]) ?>

                <div class="form-group">
                    <label class="control-label col-sm-3">
                        <?php echo $model->getAttributeLabel('logo'); ?>
                    </label>
                    <div class="col-sm-9" id="uploadArticle2">
                        <?= Html::activeHiddenInput($model, 'logo', ['id' => 'ArticleImage']); ?>
                        <div class="well-small">
                            <?php
                            $logo = ($model->logo) ? $model->logo : '/images/imageDefault.jpg';
                            ?>
                            <?= FileInput::widget([
                                'name' => 'UploadForm[files][]',
                                'pluginOptions' => [
                                    'showCaption' => false,
                                    'showRemove' => true,
                                    'showClose' => false,
                                    'showUpload' => true,
                                    'browseClass' => 'btn btn-primary',
                                    'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                                    'browseLabel' => 'Select Photo',
                                    'uploadUrl' => Url::toRoute(['upload/tmp']),
                                    'defaultPreviewContent' => Html::img($logo),
                                    'maxFileSize' => 1500,
                                    'minImageWidth' => 200,
                                    'minImageHeight' => 200,
                                ],
                                'options' => [
                                    'accept' => 'image/*',
                                    'allowedFileExtensions' => ['jpg', 'gif', 'png']
                                ],
                                'pluginEvents' => [
                                    'fileuploaded' => 'function(event, data, previewId, index){
                                         var response = data.response;
                                         if(response.file_name!=""){
                                            $("#ArticleImage").val(response.file_name);
                                            $(".kv-upload-progress").remove();
                                            $(".file-thumb-progress").remove();
                                            $(".file-default-preview img").src(response.file_name);
                                         }else{
                                            alert(response.message);
                                         }
                                         return false;
                                     }',
                                    'filesuccessremove' => 'function(event, id){
                                     }'
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                </div>

                <?= $form->field($model, 'color')->widget(\kartik\color\ColorInput::classname(), [
                    'options' => ['placeholder' => 'Select color ...'],
                ]); ?>

                <?= $form->field($model, 'icon_name')->dropDownList(Service::$icon_list) ?>

                <?= $form->field($model, 'service_type')->dropDownList(Service::$service_type_list) ?>

                <?= $form->field($model, 'type')->dropDownList(Service::$type_list) ?>

                <?= $form->field($model, 'type_target')->dropDownList(Service::$type_target_list) ?>

                <?= $form->field($model, 'status')->checkbox() ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>