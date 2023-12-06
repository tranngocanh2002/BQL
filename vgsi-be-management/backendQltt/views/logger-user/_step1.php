<?php

use common\models\AnnouncementCampaign;
use dosamigos\ckeditor\CKEditor;
use kartik\datetime\DateTimePicker;
use kartik\widgets\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementCampaign */
/* @var $form yii\widgets\ActiveForm */

$targets = [
    '0' => Yii::t('backendQltt', 'Tất cả (Cư dân và người không cư trú)'),
    '1' => Yii::t('backendQltt', 'Chỉ cư dân'),
];

$statusList = AnnouncementCampaign::getStatusList();
$time = is_numeric($model->send_event_at) ? date('H:i d/m/Y', $model->send_event_at) : date('H:i d/m/Y', time());
// $time = is_numeric($model->send_event_at) ?  $model->send_event_at * 1000 : time() * 1000;

if (gettype($model->send_event_at) == 'string') {
    $time = $model->send_event_at;
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(document).ready(function () {
            document.getElementById('announcementcampaign-send_event_at').value = "<?= $time ?>"
        })
    }, false);
</script>
<div class="tab-pane active" role="tabpanel" id="step1">
    <div class="row">
        <?= $form->field($model, 'title')->textInput(['maxlength' => 255])->label(Yii::t('backendQltt', 'Tiêu đề') . ":") ?>
        <?= $form->field($model, 'title_en')->textInput(['maxlength' => 255])->label(Yii::t('backendQltt', 'Tiêu đề (EN)') . ":") ?>
        <?= $form->field($model, 'content')->widget(CKEditor::className(), [
            'options' => ['rows' => 6],
            'preset' => 'basic'
        ])->label(Yii::t('backendQltt', 'Nội dung') . ":") ?>
        <div class="form-group highlight-addon field-status">
            <label class="control-label has-star col-md-2" for="announcementcampaign-status">
                <?= Yii::t('backendQltt', 'Công khai') . ":" ?>
            </label>
            <div class="col-md-10 d-flex">
                <?php
                $className = $model->status == AnnouncementCampaign::STATUS_PUBLIC_AT ? '' : 'd-none';
                $publicAt = $model->send_event_at ? date('Y-m-d\TH:i', $model->send_event_at) : date('Y-m-d\TH:i', time());
                ?>
                <?= Html::dropDownList('AnnouncementCampaign[status]', is_numeric($model->status) ? $model->status : AnnouncementCampaign::STATUS_ACTIVE, $statusList, ['class' => 'form-control', 'style' => 'max-width: 210px;', 'id' => 'status', 'disabled' => $model->status === AnnouncementCampaign::STATUS_ACTIVE]); ?>
                <!-- <?= Html::input('datetime-local', 'AnnouncementCampaign[send_event_at]', $publicAt, ['class' => $className, 'style' => 'max-width: 210px; margin-left: 20px', 'id' => 'send_event_at']); ?> -->
                <?= $form->field($model, 'send_event_at', [
                    'options' => [
                        'class' => $className,
                        'id' => 'send_event_at',
                    ]
                ])->widget(DateTimePicker::classname(), [
                            'model' => $model,
                            'attribute' => 'send_event_at',
                            'value' => $time,
                            'options' => [
                                'readonly' => true,
                            ],
                            'pluginOptions' => [
                                'showClear' => false,
                                'format' => 'hh:ii dd/mm/yyyy',
                                'todayHighlight' => true,
                                'startDate' => date("Y-m-d H:i"),
                                'disabled' => true,

                            ]
                        ])->label(false)
                    ?>
            </div>
        </div>
        <div class="form-group highlight-addon field-image">
            <label class="control-label has-star col-md-2" for="announcementcampaign-image">
                <?= Yii::t('backendQltt', 'Ảnh đại diện') . ":" ?>
            </label>
            <div class="col-md-6">
                <div class="form-group" style="max-width: 345px;">
                    <!-- <label class="control-label col-sm-3"><?php echo $model->getAttributeLabel('avatar'); ?></label> -->
                    <div class="col-sm-12" id="uploadArticle2">
                        <?= Html::activeHiddenInput($model, 'image', ['id' => 'ArticleImage']); ?>
                        <div class="well-small">
                            <?php
                            $image = ($model->image) ? $model->image : '/images/imageDefault.jpg';
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
                                    'defaultPreviewContent' => Html::img($image, ['id' => 'image-preview']),
                                    'maxFileSize' => 1500,
                                    'minImageWidth' => 200,
                                    'minImageHeight' => 200,
                                    'maxFileCount' => 2,
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
        <div class="form-group highlight-addon field-attach">
            <label class="control-label has-star col-md-2" for="announcementcampaign-attach">
                <?= Yii::t('backendQltt', 'Tệp đính kèm') . ":" ?>
            </label>
            <div class="col-md-10" style="display: flex; align-items: center;">
                <?= Html::activeHiddenInput($model, 'attach', ['id' => 'ArticleAttach']); ?>

                <label class="input-file">
                    <b class="btn btn-primary">
                        <i class="material-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                                <path fill="#FFF"
                                    d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z" />
                            </svg>
                        </i>
                        <?= Yii::t('backendQltt', 'Tải lên') ?>
                    </b>
                    <input type="file" class="fileInput" id="files" multiple accept=".doc, .docx, .pdf, .xls, .xlsx"
                        onchange="uploadFiles()">
                </label>
                <span>
                    <?= Yii::t('backendQltt', 'Định dạng .doc .docx .pdf .xls .xlsx không vượt quá 25Mb') ?>
                </span>
            </div>
            <label class="control-label has-star col-md-2"></label>
            <div class="col-md-10">
                <div class="list-files" id="list-files">
                    <?php
                    $fileList = !is_null($model->attach) && !empty(json_decode($model->attach)->fileList) ? json_decode($model->attach)->fileList : [];
                    foreach ($fileList as $key => $file) {
                        echo $this->render('_file_preview', [
                            'file' => $file,
                            'key' => $key,
                        ]);
                    }
                    ?>
                </div>
            </div>
        </div>
        <?= $form->field($model, 'is_send_push')->checkboxList([
            '1' => Yii::t('backendQltt', 'Gửi qua App (Mặc định)'),
        ])->label(Yii::t('backendQltt', 'Hình thức gửi') . ":");
        ?>
        <?= $form->field($model, 'targets')->checkboxList($targets, [
            'item' => function ($index, $label, $name, $checked, $value) use ($targets, $model) {
                $targetsArr = !empty($model->targets) ? json_decode($model->targets) : [];
                $isChecked = in_array($value, $targetsArr);
                $checked = $isChecked ? 'checked' : '';
                return "
                    <div class='checkbox'>
                        <label>
                            <input type='checkbox' name='{$name}' {$checked} value='{$value}'>{$label}
                        </label>
                    </div>
                ";

            },
        ])->label(Yii::t('backendQltt', 'Gửi tới') . ":");
        ?>
    </div>
    <ul class="list-inline text-center">
        <?php if (($model->status == AnnouncementCampaign::STATUS_UNACTIVE && !$model->isNewRecord) || $model->isNewRecord) { ?>
            <li>
                <button type="button" id="trash" class="btn btn-default trash">
                    <?= Yii::t('backend', 'Lưu nháp') ?>
                </button>
            </li>
            <li>
                <button type="button" id="next-step" class="btn btn-primary skip-btn">
                    <?= Yii::t('backend', 'Công khai') ?>
                </button>
            </li>
        <?php } ?>
        <?php if ($model->status == AnnouncementCampaign::STATUS_PUBLIC_AT || $model->status == AnnouncementCampaign::STATUS_ACTIVE) { ?>
            <li>
                <button type="button" id="next-step" class="btn btn-primary skip-btn">
                    <?= Yii::t('backend', 'Cập nhật') ?>
                </button>
            </li>
        <?php } ?>
        <!-- <li>
            <button type="button" id="next-step" class="btn btn-primary skip-btn">
                <?= Yii::t('backend', 'Công khai') ?>
            </button>
        </li> -->
    </ul>
</div>