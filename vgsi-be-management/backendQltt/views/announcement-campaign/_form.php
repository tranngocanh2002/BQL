<?php

use common\models\AnnouncementCampaign;
use common\models\AnnouncementTemplate;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\AnnouncementCampaign */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin([
    'class' => 'login-box',
    'type' => ActiveForm::TYPE_HORIZONTAL,
]);

$newsTemplate = AnnouncementTemplate::findAll(['type' => AnnouncementTemplate::TYPE_POST_NEWS]);
$templates = ArrayHelper::map($newsTemplate, 'id', 'name');
$attachDefault = [
    "fileImageList" => [],
    "fileList" => [],
];
$attach = !is_null($model->attach) && $model->attach != "" ? $model->attach : json_encode($attachDefault);
?>

<script>
    const NEWS_TEMPLATE = <?= json_encode(ArrayHelper::toArray($newsTemplate)) ?>;
</script>

<script>
    let filesJson = '<?= $attach ?>';
</script>

<?= $this->render('_css') ?>
<?= $this->render('_js') ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">
                <section>
                    <div class="row justify-content-center">
                        <div class="col-md-2">
                            <h4>
                                <strong>
                                    <?= Yii::t('backend', 'Nội dung gửi') ?>
                                </strong>
                            </h4>
                        </div>
                        <div class="col-md-8">
                            <div class="wizard">
                                <div class="wizard-inner">
                                    <div class="connecting-line"></div>
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation"
                                            class="<?= $model->isNewRecord ? 'active' : 'disabled' ?> step1">
                                            <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab"
                                                aria-expanded="true">
                                                <span class="round-tab">1 </span>
                                                <i>
                                                    <?= Yii::t('backend', 'Tạo mới') ?>
                                                </i>
                                            </a>
                                        </li>

                                        <li role="presentation"
                                            class="<?= $model->status === AnnouncementCampaign::STATUS_UNACTIVE ? 'active' : 'disabled' ?> step2">
                                            <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab"
                                                aria-expanded="false">
                                                <span class="round-tab">2</span>
                                                <i>
                                                    <?= Yii::t('backend', 'Lưu nháp') ?>
                                                </i>
                                            </a>
                                        </li>
                                        <li role="presentation"
                                            class="<?= $model->status != AnnouncementCampaign::STATUS_UNACTIVE ? 'active' : 'disabled' ?> step3">
                                            <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab">
                                                <span class="round-tab">3</span>
                                                <i>
                                                    <?= Yii::t('backend', 'Công khai') ?>
                                                </i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="announcement-campaign-box">
                                    <div class="tab-content" id="main_form">
                                        <?= $this->render('_step1', [
                                            'form' => $form,
                                            'model' => $model,
                                        ]) ?>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-md-2">
                            <?= $form->field($model, 'total_email_open')->widget(Select2::class, [
                                'data' => $templates,
                                'options' => [
                                    'placeholder' => Yii::t('backendQltt', 'Mẫu tin tức'),
                                    'style' => 'min-width: 220px',
                                    'id' => 'news_template'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'pluginEvents' => [
                                    'change' => 'function() { console.log("Option selected"); }',
                                ],
                            ])->label(false) ?>
                        </div> -->
                    </div>
                    <?php ActiveForm::end(); ?>
                </section>

            </div>
        </div>
    </div>
</div>