<?php

use common\models\BuildingCluster;
use dosamigos\ckeditor\CKEditor;
use kartik\widgets\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

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


<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'building_cluster_id')->dropDownList($buildingClusters, ['prompt' => 'Building Cluster ...', 'id' => 'building_cluster_id']) ?>

                <?= $form->field($model, 'type')->dropDownList($type_arr, ['prompt' => 'Type ...', 'id' => 'type']) ?>

                <?= $form->field($model, 'content_email')->widget(CKEditor::className(), [
                    'options' => ['rows' => 6],
                    'preset' => 'basic'
                ]) ?>

                <?= $form->field($model, 'content_sms')->textarea(['rows' => 6]) ?>

                <div class="form-group">
                    <label class="control-label col-sm-3">
                        <?php echo $model->getAttributeLabel('image'); ?>
                    </label>
                    <div class="col-sm-9" id="uploadArticle2">
                        <?= Html::activeHiddenInput($model, 'image', ['id' => 'ArticleImage']); ?>
                        <div class="well-small">
                            <?php
                            $logo = ($model->image) ? $model->image : '/images/imageDefault.jpg';
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

                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>