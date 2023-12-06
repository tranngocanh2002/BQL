<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\BuildingCluster;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */
/* @var $managementUserModel common\models\ManagementUser */

//$this->title = Yii::t('backendQltt', 'Thông tin chung');
$this->title = Yii::t('backendQltt', 'Xem chi tiết dự án');

?>

<style>
.line {
    margin-top: 1rem;
    margin-bottom: 2rem;
    border: 0;
    border-top: 2px solid #e8e8e8;
}

label {
    font-weight: 400;
}

label.control-label.has-star.col-md-3 {
    text-align: left;
}

.btn-inside {
    color: #016343;
    padding: 5px 8px;
    border: solid 1px #016343;
    border-radius: 5px;
}

.col-md-9 {
    padding-top: 8px;
}

.box {
    border-top: 0px;
}

.title {
    font-size: 18px;
}

.well-small {
    border: none;
}
</style>

<div class="user-role-create">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="col-xs-12">
                    <div class="col-md-6">
                        <h4 style="margin-top: 18px;"><strong><?= Yii::t('backendQltt', 'Thông tin chung') ?></strong>
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <p class="text-right" style="padding-top: 20px; padding-bottom: 8px">
                            <a target="_blank"
                                href="<?= Url::toRoute(['building-cluster/to-bql', 'id' => $model->id]);?>"><strong
                                    class="btn-inside"><?= Yii::t('backendQltt', 'Xem chi tiết dự án') ?></strong></a>
                        </p>
                    </div>

                </div>
                <div class="col-xs-12">
                    <div class="col-md-6">
                        <!-- <h3><strong><?= Yii::t('backendQltt', 'Thông tin dự án') ?></strong></h3> -->
                    </div>
                    <div class="col-md-6">
                        <p class="text-right">
                            <?php if ($this->context->checkPermission('building-cluster', 'update')) { ?>
                            <a href="<?= Url::toRoute(['building-cluster/update', 'id' => $model->id]);?>"><span
                                    class="btn-inside"><?= Yii::t('backendQltt', 'Chỉnh sửa') ?></span></a>
                            <?php } ?>
                        </p>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="min-height: 80vh;">
                    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]); ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <!-- <label class="control-label col-sm-3"><?php echo $model->getAttributeLabel('avatar'); ?></label> -->
                            <div class="col-sm-12" id="uploadArticle2">
                                <?= Html::activeHiddenInput($model, 'avatar', ['id' => 'ArticleImage']); ?>
                                <div class="well-small">
                                    <?php
                                        $urlAvartar = json_decode($model->medias,true);
                                        $avatar = $urlAvartar['avatarUrl'] ? $urlAvartar['avatarUrl'] : '/images/imageDefault.jpg';
                                        if(!empty($model->avatar))
                                        {
                                            $avatar = $model->avatar;
                                        }
                                    ?>
                                    <img src="<?= $avatar ?>" alt=""
                                        style="width: 80%; height: 80%;  border-radius: 10px; border: solid 1px #ddd;  object-fit: <?= $model->avatar ? 'cover' : 'cover' ?>;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="margin-left: 50px;">
                        <div class="form-group highlight-addon field-buildingcluster-name ">
                            <label class="control-label has-star col-md-3"
                                for="buildingcluster-name">
                                <?= $model->getAttributeLabel('name') ?>:</label>
                            <div class="col-md-9">
                                <strong><?= $model->name ?></strong>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-buildingcluster-name ">
                            <label class="control-label has-star col-md-3"
                                for="buildingcluster-name">
                                <?= $model->getAttributeLabel('domain') ?>:</label>
                            <div class="col-md-9">
                                <strong><?= $model->domain ?></strong>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-buildingcluster-name">
                            <label class="control-label has-star col-md-3"
                                for="buildingcluster-name"><?= $model->getAttributeLabel('address') ?>:</label>
                            <div class="col-md-9">
                                <strong><?= $model->address ?></strong>
                            </div>
                        </div>
                        <div class="form-group highlight-addon field-buildingcluster-name">
                            <label class="control-label has-star col-md-3"
                                for="buildingcluster-name"><?= Yii::t('backendQltt', 'Giới thiệu') ?>:</label>
                            <div class="col-md-9">
                                <span><?= nl2br($model->description) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12"
                        style="border-top: 1px #f4eeeea6  solid; padding-top: 24px; margin-top: 24px">
                        <div class="col-xs-12">
                            <div class="col-md-6">
                                <h3 class="required">
                                    <strong><?= Yii::t('backend', 'Thông tin tài khoản Admin') ?></strong>
                                </h3>
                            </div>
                            <div class="col-md-6">
                                <p class="text-right">
                                    <?php if ($this->context->checkPermission('building-cluster', 'update')) { ?>
                                    <a
                                        href="<?= Url::toRoute(['building-cluster/update', 'id' => $model->id, 'update-admin' => 1]);?>"><span
                                            class="btn-inside"><?= Yii::t('backendQltt', 'Chỉnh sửa') ?></span></a>
                                    <?php } ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-left: 18px;">
                            <span><strong><?= Yii::t('backend', 'Ghi chú') ?></strong></span><br>
                            <small style="margin-left: 20px;">-
                                <?= Yii::t('backend', 'Đây là tài khoản dành cho cấp quản lý tập trung. Một số dự án chỉ có thể có 1 tài khoản admin') ?></small><br>
                            <small style="margin-left: 20px;">-
                                <?= Yii::t('backend', 'Bắt buộc phải nhập thông tin khi tạo dự án') ?></small><br>
                        </div>

                        <div class="col-md-5" style="margin-top: 45px; margin-left: 40px;">
                            <div class="form-group highlight-addon field-buildingcluster-name ">
                                <label class="control-label has-star col-md-3"
                                    for="buildingcluster-name"><?= $managementUserModel->getAttributeLabel('email') ?>:</label>
                                <div class="col-md-9">
                                    <strong>
                                    <?php 
                                        $managementUserModel->email ;
                                        $buildingCluster = BuildingCluster::findOne($managementUserModel->building_cluster_id);
                                    echo $buildingCluster ? $buildingCluster->email : '';
                                    ?>
                                    </strong>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <div class="form-group highlight-addon field-buildingcluster-name ">
                                <label class="control-label has-star col-md-3"
                                    for="buildingcluster-name"><?= Yii::t('backend', 'Password')?>:</label>
                                <div style="height: 30px; display: flex;">
                                    <strong style="margin: 10px 0 10px 14px;"><?= '**********************' ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>