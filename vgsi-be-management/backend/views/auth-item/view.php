<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserRole */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Management User Role'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <!-- /.box-header -->
            <div class="box-body">

                <p>
                    <?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name',
                        [
                            'attribute' => 'permission',
                            'label' => Yii::t('backend', 'Permission Api'),
                            'format'    => 'html',
                            'value' => function ($model) {
                                $per_stirng = '';
                                $authItemChild = $model->authItemChildren;
                                foreach ($authItemChild as $per) {
                                    $per_stirng .= Html::tag('span', $per->child, ['style' => 'color:#989898', 'class' => 'fa fa-check']) .'<br>';
                                }
                                return $per_stirng;
                            }
                        ],
                        [
                            'attribute' => 'data_web',
                            'label' => Yii::t('backend', 'Permission Web'),
                            'format'    => 'html',
                            'value' => function ($model) {
                                $per_stirng = '';
                                $roles = (!empty($model->data_web)) ? \yii\helpers\Json::decode($model->data_web) : [];
                                foreach ($roles as $per) {
                                    $per_stirng .= Html::tag('span', $per, ['style' => 'color:#989898', 'class' => 'fa fa-check']) .'<br>';
                                }
                                return $per_stirng;
                            }
                        ],
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
