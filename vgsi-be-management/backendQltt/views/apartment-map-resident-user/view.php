<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ApartmentMapResidentUser */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backendQltt', 'Apartment Map Resident Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apartment-map-resident-user-view box box-primary">
    <div class="box-header">
<!--        --><?php //= Html::a(Yii::t('backendQltt', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
<!--        --><?php //= Html::a(Yii::t('backendQltt', 'Delete'), ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger btn-flat',
//            'data' => [
//                'confirm' => Yii::t('backendQltt', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'apartment_id',
                'resident_user_id',
                'building_cluster_id',
                'building_area_id',
                'type',
                'status',
                'created_at:datetime',
                'updated_at:datetime',
                'created_by',
                'updated_by',
                'apartment_name',
                'apartment_capacity',
                'apartment_code',
                'resident_user_phone',
                'resident_user_email:email',
                'resident_user_first_name',
                'resident_user_last_name',
                'resident_user_avatar',
                'resident_user_gender',
                'resident_user_birthday',
                'apartment_parent_path',
                'install_app',
                'resident_user_is_send_email:email',
                'type_relationship',
                'resident_user_is_send_notify',
                'apartment_short_name',
                'resident_user_nationality',
                'resident_name_search',
                'cmtnd',
                'noi_cap_cmtnd',
                'ngay_cap_cmtnd',
                'work',
                'so_thi_thuc',
                'ngay_dang_ky_nhap_khau',
                'ngay_dang_ky_tam_chu',
                'ngay_het_han_thi_thuc',
                'last_active',
                'is_deleted',
                'deleted_at',
            ],
        ]) ?>
    </div>
</div>
