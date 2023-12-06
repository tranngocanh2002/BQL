<?php

use common\models\BuildingCluster;
use common\models\rbac\AuthGroup;
use kartik\widgets\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);
if ($model->isNewRecord) {
    $authGroups = [];
} else {
    $authGroups = AuthGroup::find()->where(['building_cluster_id' => $model->building_cluster_id])->all();
    $authGroups = ArrayHelper::map($authGroups, 'id', 'name');
    asort($authGroups);
}

?>
<script type="text/javascript">
    function search_auth_group() {
        var building_cluster_id = $('#building_cluster_id').val();
        $("#auth_group_id").html('<option value="">Loading...</option>');
        $.ajax({
            type: "GET",
            url: "/auth-group/get-by-cluster",
            data: "building_cluster_id=" + building_cluster_id,
            success: function (data) {
                data = '<option value="">Auth Group ...</option>' + data;
                $("#auth_group_id").html(data);
            }
        });
    }

</script>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                <div class="form-group">
                    <label class="control-label col-sm-3">
                        <?php echo $model->getAttributeLabel('avatar'); ?>
                    </label>
                    <div class="col-sm-9" id="uploadArticle2">
                        <?= Html::activeHiddenInput($model, 'avatar', ['id' => 'ArticleImage']); ?>
                        <div class="well-small">
                            <?php
                            $avatar = ($model->avatar) ? $model->avatar : '/images/avatar.png';
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
                                    'defaultPreviewContent' => Html::img($avatar),
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
                <?= $form->field($model, 'building_cluster_id')->dropDownList($buildingClusters, ['prompt' => 'Building Cluster ...', 'id' => 'building_cluster_id', 'onchange' => 'search_auth_group()']) ?>

                <?= $form->field($model, 'auth_group_id')->dropDownList($authGroups, ['prompt' => 'Auth Group ...', 'id' => 'auth_group_id']) ?>

                <?= $form->field($model, 'status')->checkbox() ?>

                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>