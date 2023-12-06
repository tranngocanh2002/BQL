<?php

use common\models\BuildingCluster;
use dosamigos\ckeditor\CKEditor;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementTemplate */
/* @var $form yii\widgets\ActiveForm */


//$type_arr = [
//        1 => 'Nhắc nợ lần 1',
//        2 => 'Nhắc nợ lần 2',
//        3 => 'Nhắc nợ lần 3'
//]

$type_arr = \common\models\AnnouncementTemplate::$type_list;
unset($type_arr[0]);
?>

<style>
    .form-horizontal .control-label {
        text-align: left;
    }

    .box-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        s
    }

    .form-group.col-xs-12 {
        display: flex;
        justify-content: center;
        gap: 18px;
    }

    .file-default-preview>img:nth-child(1) {
        display: flex;
        justify-content: center;
        width: 100%;
        margin: 0;
    }

    .btn-success {
        background-color: transparent;
        border-color: #016343;
        color: #016343;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <div class="col-md-8" style="padding-top: 20px;">
                    <?php $form = ActiveForm::begin([
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                    ]); ?>
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label(Yii::t('backendQltt', 'Tiêu đề')) ?>
                    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true])->label(Yii::t('backendQltt', 'Tiêu đề (EN)')) ?>
                    <?= $form->field($model, 'content_email')->widget(CKEditor::className(), [
                        'options' => ['rows' => 6],
                        'preset' => 'basic'
                    ])->label(Yii::t('backendQltt', 'Nội dung')) ?>
                    <div class="form-group highlight-addon field-announcementtemplateform-image">
                        <label class="control-label has-star col-md-2">
                            <?= Yii::t('backendQltt', 'Ảnh đại diện') ?>
                        </label>
                        <div class="col-md-6">
                            <div class="form-group" style="max-width: 345px;">
                                <div class="col-sm-12" id="uploadArticle2">
                                    <?= Html::activeHiddenInput($model, 'image', ['id' => 'ArticleImage']); ?>
                                    <div class="well-small">
                                        <?php
                                        $logo = ($model->image) ? $model->image : '/images/imageDefault.jpg';
                                        ?>
                                        <?= FileInput::widget([
                                            'name' => 'UploadForm[files][]',
                                            'pluginOptions' => [
                                                'showCaption' => false,
                                                'showRemove' => false,
                                                'showClose' => false,
                                                'showUpload' => false,
                                                'browseClass' => 'btn btn-primary',
                                                'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                                                'browseLabel' => Yii::t('backendQltt', 'Select Photo'),
                                                'uploadUrl' => Url::toRoute(['upload/tmp']),
                                                'defaultPreviewContent' => Html::img($logo),
                                                'maxFileSize' => 10000,
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
                        </div>
                    </div>

                    <div class="form-group col-xs-12">
                        <a href="/announcement-template" class="btn btn-default">
                            <?= Yii::t('backendQltt', 'Cancel') ?>
                        </a>
                        <?= Html::submitButton(!isset($model->id) ? Yii::t('backendQltt', 'Thêm mẫu mới') : Yii::t('backendQltt', 'Update'), ['class' => !isset($model->id) ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>