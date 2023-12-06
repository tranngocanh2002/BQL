<?php

namespace frontend\models;

use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use common\models\CardManagement;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="CardManagementResponse")
 * )
 */
class CardManagementResponse extends CardManagement
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="number", type="string"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="type", type="integer", description="1 - thẻ từ, 2 - thẻ rfid"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="integer"),
     * @SWG\Property(property="apartment_parent_path", type="integer"),
     * @SWG\Property(property="apartment_map_resident_user_id", type="integer", description="id chủ thẻ map"),
     * @SWG\Property(property="resident_user_id", type="integer", description="id chủ thẻ"),
     * @SWG\Property(property="resident_user_name", type="string", description="tên chủ thẻ"),
     * @SWG\Property(property="resident_user_phone", type="string", description="Số điện thoại chủ thẻ"),
     * @SWG\Property(property="code", type="string", description="Mã thẻ"),
     * @SWG\Property(property="description", type="string", description="Mô tả thẻ"),
     * @SWG\Property(property="description_en", type="string", description="Mô tả thẻ tiếng anh"),
     * @SWG\Property(property="reason", type="string", description="lý do từ chối thẻ"),
     * @SWG\Property(property="created_at", type="integer", description="Ngày tạo"),
     * @SWG\Property(property="updated_at", type="integer", description="Ngày tạo"),
     * @SWG\Property(property="map_service", type="array", @SWG\Items(type="object", ref="#/definitions/CardManagementMapServiceResponse"),),
     */
    public function fields()
    {
        return [
            'id',
            'number',
            'status',
            'type',
            'apartment_id',
            'apartment_name' => function ($model) {
                if (!empty($model->apartment)) {
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_parent_path' => function ($model) {
                if (!empty($model->apartment)) {
                    return $model->apartment->parent_path;
                }
                return '';
            },
            'apartment_map_resident_user_id' => function ($model) {
                $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['building_cluster_id' => $model->building_cluster_id, 'apartment_id' => $model->apartment_id, 'resident_user_id' => $model->resident_user_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                if (!empty($apartmentMapResidentUser)) {
                    return $apartmentMapResidentUser->id;
                }
                return null;
            },
            'resident_user_id',
            'resident_user_name' => function ($model) {
                $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['id' => $model->resident_user_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                if (!empty($apartmentMapResidentUser)) {
                    return $apartmentMapResidentUser->resident_user_first_name . ' ' . $apartmentMapResidentUser->resident_user_last_name;
                }
                // if($model->apartmentMapResidentUser){
                //     return $model->apartmentMapResidentUser->resident_user_first_name . ' ' . $model->apartmentMapResidentUser->resident_user_last_name;
                // }
                return '';
            },
            'resident_user_phone' => function ($model) {
                if($model->apartmentMapResidentUser){
                    return $model->apartmentMapResidentUser->resident_user_phone;
                }
                return '';
            },
            'created_at',
            'updated_at',
            'code',
            'description',
            'description_en',
            'reason',
            'map_service' => function ($model) {
                return CardManagementMapServiceResponse::find()->where(['card_management_id' => $model->id])->all();
            },
        ];
    }
}
