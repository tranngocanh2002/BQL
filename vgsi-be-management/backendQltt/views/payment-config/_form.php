<?php

use common\models\BuildingCluster;
use common\models\PaymentConfig;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
$buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
$buildingClusters = ArrayHelper::map($buildingClusters, 'id', 'name');
asort($buildingClusters);

/* @var $this yii\web\View */
/* @var $model common\models\PaymentConfig */
/* @var $form yii\widgets\ActiveForm */

$js = '';

if($model->gate !== PaymentConfig::GATE_NGANLUONG ){
    $js .= <<<JS

        $('.field-paymentconfig-receiver_account').hide()
        $('.field-paymentconfig-merchant_id').hide()
        $('.field-paymentconfig-merchant_pass').hide()
        
        $('.field-paymentconfig-merchant_name').hide()
        $('.field-paymentconfig-partner_code').hide()
        $('.field-paymentconfig-access_key').hide()
        $('.field-paymentconfig-secret_key').hide()
    
JS;

}

$js .= <<<JS
    $('#gate').change(function() {
         var is_gate = parseInt($('#gate').val())
         if(is_gate === 0){
             $('.field-paymentconfig-receiver_account').show()
             $('.field-paymentconfig-merchant_id').show()
             $('.field-paymentconfig-merchant_pass').show()
             
            $('.field-paymentconfig-merchant_name').hide()
            $('.field-paymentconfig-partner_code').hide()
            $('.field-paymentconfig-access_key').hide()
            $('.field-paymentconfig-secret_key').hide()
         }else if(is_gate === 1){
             $('.field-paymentconfig-receiver_account').hide()
             $('.field-paymentconfig-merchant_id').hide()
             $('.field-paymentconfig-merchant_pass').hide()
             
             $('.field-paymentconfig-merchant_name').hide()
            $('.field-paymentconfig-partner_code').hide()
            $('.field-paymentconfig-access_key').hide()
            $('.field-paymentconfig-secret_key').hide()
         }else{
              $('.field-paymentconfig-receiver_account').hide()
             $('.field-paymentconfig-merchant_id').hide()
             $('.field-paymentconfig-merchant_pass').hide()
             
            $('.field-paymentconfig-merchant_name').show()
            $('.field-paymentconfig-partner_code').show()
            $('.field-paymentconfig-access_key').show()
            $('.field-paymentconfig-secret_key').show()
         }
    })
JS;
$this->registerJs($js);

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]); ?>
<!--phan chung-->
                <?= $form->field($model, 'building_cluster_id')->dropDownList($buildingClusters, ['prompt' => 'Building Cluster ...', 'id' => 'building_cluster_id']) ?>
                <?= $form->field($model, 'gate')->dropDownList(\common\models\PaymentConfig::$gate_lst, ['prompt' => 'Type ...', 'id' => 'gate']) ?>

<!--momo-->
                <?= $form->field($model, 'merchant_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'partner_code')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'access_key')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'secret_key')->textInput(['maxlength' => true]) ?>

<!--ngan luong-->
                <?= $form->field($model, 'receiver_account')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'merchant_id')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'merchant_pass')->textInput(['maxlength' => true]) ?>

<!--phan chung-->
                <?= $form->field($model, 'checkout_url')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'checkout_url_old')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'return_url')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'cancel_url')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'notify_url')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'return_web_url')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'status')->checkbox() ?>

                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
