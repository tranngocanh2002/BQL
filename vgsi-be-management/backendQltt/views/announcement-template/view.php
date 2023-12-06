<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementTemplate */

$this->title = Yii::t('backendQltt', 'Chi tiết mẫu tin tức');
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row justify-content-center">
                    <div class="col-md-12" style="padding-right: 28px;">
                        <?php if ($this->context->checkPermission('announcement-template', 'update')) { ?>
                            <a class="pull-right btn btn-primary"
                                href="/announcement-template/update?id=<?= $model->id ?>"><?= Yii::t('backendQltt', 'Chỉnh sửa') ?></a>
                        <?php } ?>
                    </div>
                    <div class="col-md-12 main">

                        <div class="form-group highlight-addon field-announcementcampaign-title required">
                            <span class="control-label has-star col-md-2">
                                <?= Yii::t('backendQltt', 'Tiêu đề') ?>*:
                            </span>
                            <div class="col-md-10">
                                <span>
                                    <?= $model->name ?>
                                </span>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-announcementcampaign-title required">
                            <span class="control-label has-star col-md-2">
                                <?= Yii::t('backendQltt', 'Tiêu đề (EN)') ?>*:
                            </span>
                            <div class="col-md-10">
                                <span>
                                    <?= $model->name_en ?>
                                </span>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-announcementcampaign-title required">
                            <span class="control-label has-star col-md-2">
                                <?= Yii::t('backendQltt', 'Nội dung') ?>*:
                            </span>
                            <div class="col-md-10">
                                <span>
                                    <?= $model->content_email ?>
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