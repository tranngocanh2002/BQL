<?php

use common\models\AnnouncementCampaign;
use common\models\AnnouncementTemplate;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementCampaign */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('backend', 'Chi tiết tin tức');
// $this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Announcement Campaign'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;

$newsTemplate = AnnouncementTemplate::findAll(['type' => AnnouncementTemplate::TYPE_1]);
$templates = ArrayHelper::map($newsTemplate, 'id', 'name');
if (empty($model->targets)) {
    $model->targets = json_encode([]);
}
$status = [
    AnnouncementCampaign::STATUS_ACTIVE => Yii::t('backendQltt', 'Công khai'),
    AnnouncementCampaign::STATUS_UNACTIVE => Yii::t('backendQltt', 'Nháp'),
    AnnouncementCampaign::STATUS_PUBLIC_AT => Yii::t('backendQltt', 'Hẹn giờ'),
];
?>

<script>
    const NEWS_TEMPLATE = <?= json_encode(ArrayHelper::toArray($newsTemplate)) ?>;
</script>
<?= $this->render('_css') ?>
<?= $this->render('_js') ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="col-md-6" style="margin-bottom: 14px;">
                            <h4><strong>
                                    <?= Yii::t('backendQltt', 'Nội dung gửi') ?>
                                </strong></h4>
                        </div>
                        <div class="col-md-6">
                            <?php if ($this->context->checkPermission('announcement-campaign', 'update')) { ?>
                                <a class="pull-right btn btn-primary"
                                    href="/announcement-campaign/update?id=<?= $model->id ?>"><?= Yii::t('backendQltt', 'Chỉnh sửa') ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-12 main">
                        <div class="form-group highlight-addon field-announcementcampaign-title">
                            <span class="control-label has-star col-md-2">
                                <?= Yii::t('backendQltt', 'Tiêu đề') ?>:
                            </span>
                            <div class="col-md-10">
                                <span>
                                    <?= $model->title ?>
                                </span>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-announcementcampaign-title">
                            <span class="control-label has-star col-md-2">
                                <?= Yii::t('backendQltt', 'Tiêu đề (EN)') ?>:
                            </span>
                            <div class="col-md-10">
                                <span>
                                    <?= $model->title_en ?>
                                </span>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-announcementcampaign-title">
                            <span class="control-label has-star col-md-2">
                                <?= $model->getAttributeLabel('status') ?>:
                            </span>
                            <div class="col-md-10">
                                <span
                                    class="btn-1 <?= $model->status == 0 ? 'btn-primary' : $model->status == 1 ? 'bg-warning' : 'bg-danger' ?>">
                                    <?= $status[$model->status] ?>
                                </span>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-announcementcampaign-is_send_push">
                            <span class="control-label has-star col-md-2">
                                <?= Yii::t('backendQltt', 'Hình thức gửi') ?>:
                            </span>
                            <div class="col-md-10">
                                <span>
                                    <input type="checkbox" value="1" disabled <?= $model->is_send_push ? 'checked' : '' ?>><?= Yii::t('backendQltt', 'App (Mặc định)') ?>
                                </span>
                                <div class="help-block"></div>
                                <!-- <span>
                                    <input type="checkbox" value="1" disabled <?= $model->is_send_email ? 'checked' : '' ?>><?= Yii::t('backendQltt', 'Gửi thư điện tử') ?>
                                </span>
                                <div class="help-block"></div> -->
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-announcementcampaign-targets">
                            <span class="control-label has-star col-md-2">
                                <?= Yii::t('backendQltt', 'Gửi tới') ?>:
                            </span>
                            <div class="col-md-10">
                                <div id="announcementcampaign-targets" aria-required="true">
                                    <input type="checkbox" disabled <?= is_numeric(array_search('0', json_decode($model->targets))) ? 'checked' : '' ?>><?= Yii::t('backendQltt', 'Tất cả (Cư dân và người không cư trú)') ?>
                                    <br>
                                    <input type="checkbox" disabled <?= is_numeric(array_search('1', json_decode($model->targets))) ? 'checked' : '' ?>><?= Yii::t('backendQltt', 'Chỉ cư dân') ?>
                                    <br>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-announcementcampaign-title">
                            <span class="control-label has-star col-md-2">
                                <?= Yii::t('backendQltt', 'Thời gian gửi') ?>:
                            </span>
                            <div class="col-md-10">
                                <span>
                                    <?= date('h:i d/m/Y', $model->send_event_at) ?>
                                </span>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-announcementcampaign-title">
                            <span class="control-label has-star col-md-2">
                                <?= Yii::t('backendQltt', 'Nội dung') ?>:
                            </span>
                            <div class="col-md-10">
                                <span>
                                    <?= $model->content ?>
                                </span>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .col-md-12.main {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .btn-1 {
        padding: 4px 8px;
        border-radius: 4px;
        color: white;
    }
</style>