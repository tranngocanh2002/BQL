<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentResponse")
 * )
 */
class ApartmentResponse extends Apartment
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="handover", type="string"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="status", type="integer", description="0 : chưa có người ở, 1: đang có người ở"),
     * @SWG\Property(property="status_name", type="string"),
     * @SWG\Property(property="capacity", type="number"),
     * @SWG\Property(property="building_area", type="object",
     *     @SWG\Property(property="id", type="integer"),
     *     @SWG\Property(property="name", type="string"),
     * ),
     * @SWG\Property(property="parent_path", type="string"),
     * @SWG\Property(property="resident_user", type="object",
     *     @SWG\Property(property="id", type="integer"),
     *     @SWG\Property(property="phone", type="string"),
     *     @SWG\Property(property="first_name", type="string"),
     *     @SWG\Property(property="last_name", type="string"),
     * ),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="set_water_level", type="integer", description="0 : chưa khai báo định mức nước, 1: đã khai báo định mức nước sử dụng"),
     * @SWG\Property(property="date_received", type="integer", description="ngày nhận nhà"),
     * @SWG\Property(property="date_delivery", type="integer", description="ngày bàn giao"),
     * @SWG\Property(property="total_members", type="integer"),
     * @SWG\Property(property="form_type", type="integer", description="0: Villa đơn lập, 1: Villa song lập, 2: Nhà phố, 3: Nhà phố thương mại, 4: Căn hộ Studio, 5: Căn hộ, 6: Căn hộ Duplex thông tầng, 7: Căn hộ penthouse, 8: Officetel, 9:  Khách sạn và căn hộ dịch vụ"),
     * @SWG\Property(property="form_type_name", type="string"),
     * @SWG\Property(property="form_type_name_en", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'description',
            'handover',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
            'status',
            'status_name' => function ($model) {
                /**
                 * @var $model Apartment
                 */
                return isset(Apartment::$status_list[$model->status])?Apartment::$status_list[$model->status]:"";
            },
            'capacity' => function($model){
                return (float)$model->capacity;
            },
            'building_area' => function ($model) {
                if(isset($model->buildingArea)){
                    $building = $model->buildingArea;
                    return [
                        'id' => $building->id,
                        'name' => $building->name,
                    ];
                }
                return [
                    'id' => null,
                    'name' => null,
                ];
            },
            'resident_user' => function($model){
                $resident = ApartmentMapResidentUser::findOne(['type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_id' => $model->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                if($resident && $resident->resident){
                    $resident = $resident->resident;
                    return [
                        'id' => $resident->id,
                        'phone' => $resident->phone,
                        'first_name' => $resident->first_name,
                        'last_name' => $resident->last_name,
                    ];
                }else{
                    return [
                        'id' => $resident->id ?? '',
                        'phone' => $resident->resident_user_phone ?? '',
                        'first_name' => $resident->resident_user_first_name ?? '',
                        'last_name' => $resident->resident_user_last_name ?? '',
                    ];
                }
            },
            'resident_user_name' => function($model){
                $resident = ApartmentMapResidentUser::findOne(['type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_id' => $model->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                if($resident){
                    return $resident->getFullName();
                }else{
                    return "";
                }
            },
            'parent_path' => function($model){
                return trim($model->parent_path, '/');
            },
            'code',
            'date_received',
            'date_delivery',
            'set_water_level',
            'total_members',
            'form_type',
            'form_type_name' => function($model){
                $type_list = $model->getFormTypeList();
                return $type_list[$model->form_type];
            },
            'form_type_name_en' => function($model){
                $type_list = $model->getFormTypeEnList();
                return $type_list[$model->form_type];
            },
            'created_at',
            'updated_at',
        ];
    }
}
