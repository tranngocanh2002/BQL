<?php

namespace frontend\models;

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
     * @SWG\Property(property="number", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="start_date", type="integer"),
     * @SWG\Property(property="end_date", type="integer"),
     * @SWG\Property(property="cancel_date", type="integer", description="Thời điểm hủy"),
     * @SWG\Property(property="status", type="integer", description="0 - khởi tạo, 1 - đang hoạt động, 2 - đã hủy"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="service_parking_level_id", type="integer", description="Loại tính phí"),
     * @SWG\Property(property="service_parking_level_name", type="string"),
     * @SWG\Property(property="is_map_card", type="integer", description="0 - chưa map vào thẻ, 1 - đã map và thẻ"),
     */
    public function fields()
    {
        return [
            'id',
            'number',
            'description',
            'start_date',
            'end_date',
            'cancel_date',
            'status',
            'apartment_id',
            'apartment_name' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_parent_path' => function($model){
                if(!empty($model->apartment)){
                    return trim($model->apartment->parent_path, '/');
                }
                return '';
            },
            'service_parking_level_id',
            'service_parking_level_name' => function($model){
                if(!empty($model->serviceParkingLevel)){
                    return $model->serviceParkingLevel->name;
                }
                return '';
            },
            'service_parking_level_price' => function($model){
                if(!empty($model->serviceParkingLevel)){
                    return $model->serviceParkingLevel->price;
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
        ];
    }
}
