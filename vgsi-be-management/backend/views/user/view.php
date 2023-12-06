<?php

use common\models\User;
use common\models\UserRole;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var common\models\User $model
 */
$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Admin'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$new_category = UserRole::find()->where(['<>','id','1'])->all();
$list_role = ArrayHelper::map($new_category, 'id', 'name');
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">
                <p>
                    <?php echo Html::a(Yii::t('backend', 'Reset password'), ['reset-password', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                </p>

                <?=
                DetailView::widget([
                    'model' => $model,
                    'condensed' => false,
                    'bootstrap' => true,
                    'hover' => true,
                    'mode' => Yii::$app->request->get('edit') == 't' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
                    'panel' => [
                        'heading' => $this->title,
                        'type' => DetailView::TYPE_INFO,
                    ],
                    'attributes' => [
                        [
                            'attribute' => 'id',
                            'value' => $model->id,
                            'displayOnly' => true
                        ],
                        [
                            'attribute' => 'username',
                            'value' => $model->username,
                            'displayOnly' => true
                        ],
                        'email:email',
                        'phone',
                        [
                            'attribute' => 'status',
                            'value' => ($model->status) ? Yii::t('backend', 'Active') : Yii::t('backend', 'InActive'),
                            'type' => DetailView::INPUT_SELECT2,
                            'widgetOptions' => [
                                'data' => [User::STATUS_ACTIVE => Yii::t('backend', 'Active'), User::STATUS_DELETED => Yii::t('backend', 'InActive')],
                                'options' => ['placeholder' => 'Select ...'],
                                'pluginOptions' => ['allowClear' => true, 'width' => '100%'],
                            ],
                        ],
                        [
                            'attribute' => 'role_id',
                            'value' => UserRole::findOne($model->role_id)->name,
                            'type' => DetailView::INPUT_SELECT2,
                            'widgetOptions' => [
                                'data' => $list_role,
                                'options' => ['placeholder' => 'Select ...'],
                                'pluginOptions' => ['allowClear' => true, 'width' => '100%'],
                            ],
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['datetime', 'format' => 'php:d-m-Y g:i A'],
                            'displayOnly' => true
                        ],
                        [
                            'attribute' => 'updated_at',
                            'format' => ['datetime', 'format' => 'php:d-m-Y g:i A'],
                            'displayOnly' => true
                        ],
                    ],
                    'deleteOptions' => [
                        'url' => ['delete', 'id' => $model->id],
                    ],
                    'enableEditMode' => true,
                    'alertWidgetOptions' => false
                ])
                ?>

            </div>
        </div>
    </div>
</div>
