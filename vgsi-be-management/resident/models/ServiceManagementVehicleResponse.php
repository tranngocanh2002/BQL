<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\CardManagementMapService;
use common\models\ServiceManagementVehicle;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceManagementVehicleResponse")
 * )
 */
class ServiceManagementVehicleResponse extends ServiceManagementVehicle
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="number", type="string", description="Biển số xe"),
     * @SWG\Property(property="cancel_date", type="integer", description="Ngày hủy"),
     * @SWG\Property(property="status", type="integer", description="0 - Mới tạo, 1 - Đang hoạt động, 2 - Đã hủy"),
     * @SWG\Property(property="start_date", type="integer"),
     * @SWG\Property(property="end_date", type="integer"),
     * @SWG\Property(property="service_parking_level_id", type="integer", description="id loại phí"),
     * @SWG\Property(property="service_parking_level_name", type="string", description="tên loại phí"),
     * @SWG\Property(property="service_parking_level_name_en", type="string", description="tên loại phí"),
     * @SWG\Property(property="service_parking_level_price", type="integer", description="giá / tháng"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_capacity", type="integer"),
     * @SWG\Property(property="is_map_card", type="integer", description="0 - chưa map vào thẻ, 1 - đã map và thẻ"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="config", type="object", ref="#/definitions/ServiceVehicleConfigResponse"),
     * @SWG\Property(property="level", type="array",
     *     @SWG\Items(type="object", ref="#/definitions/ServiceParkingLevelResponse"),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'number',
            'cancel_date',
            'status',
            'start_date',
            'end_date',
            'service_parking_level_id',
            'service_parking_level_name' => function($model){
                if($model->serviceParkingLevel){
                    return $model->serviceParkingLevel->name;
                }
                return '';
            },
            'service_parking_level_name_en' => function($model){
                if($model->serviceParkingLevel){
                    return $model->serviceParkingLevel->name_en;
                }
                return '';
            },
            'service_parking_level_price' => function($model){
                if($model->serviceParkingLevel){
                    return $model->serviceParkingLevel->price;
                }
                return 0;
            },
            'apartment_id',
            'apartment_name' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_capacity' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->capacity;
                }
                return 0;
            },
            'is_map_card' => function($model){
                $cardManagementMapService = CardManagementMapService::findOne(['type' => CardManagementMapService::TYPE_PARKING, 'service_management_id' => $model->id]);
                if(!empty($cardManagementMapService)){
                    return 1;
                }
                return 0;
            },
            'config' => function($model){
                return ServiceVehicleConfigResponse::findOne(['building_cluster_id' => $model->building_cluster_id]);
            },
            'level' => function($model){
                return ServiceParkingLevelResponse::find()->where(['building_cluster_id' => $model->building_cluster_id, 'building_area_id' => $model->building_area_id])->all();
            },
            'created_at',
            'updated_at',
        ];
    }
}
