<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceParkingFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceParkingFeeResponse")
 * )
 */
class ServiceParkingFeeResponse extends ServiceParkingFee
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="description_en", type="string"),
     * @SWG\Property(property="json_desc", type="object"),
     * @SWG\Property(property="service_parking_level_id", type="integer"),
     * @SWG\Property(property="service_parking_level_name", type="string"),
     * @SWG\Property(property="service_parking_level_name_en", type="string"),
     * @SWG\Property(property="service_parking_level_price", type="integer"),
     * @SWG\Property(property="service_management_vehicle_id", type="integer"),
     * @SWG\Property(property="service_management_vehicle_number", type="string"),
     * @SWG\Property(property="start_time", type="integer"),
     * @SWG\Property(property="end_time", type="integer"),
     * @SWG\Property(property="total_money", type="integer"),
     * @SWG\Property(property="is_created_fee", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_service_name", type="string"),
     * @SWG\Property(property="fee_of_month", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'description',
            'description_en',
            'json_desc' => function($model){
                if(!empty($model->json_desc)){
                    return json_decode($model->json_desc, true);
                }
                return null;
            },
            'service_parking_level_id',
            'service_parking_level_name' => function($model){
                if(!empty($model->serviceParkingLevel)){
                    return $model->serviceParkingLevel->name;
                }
                return '';
            },
            'service_parking_level_name_en' => function($model){
                if(!empty($model->serviceParkingLevel)){
                    return $model->serviceParkingLevel->name_en;
                }
                return '';
            },
            'service_parking_level_price' => function($model){
                if(!empty($model->serviceParkingLevel)){
                    return $model->serviceParkingLevel->price;
                }
                return null;
            },
            'service_management_vehicle_id',
            'service_management_vehicle_number' => function($model){
                if(!empty($model->serviceManagementVehicle)){
                    return $model->serviceManagementVehicle->number;
                }
                return '';
            },
            'start_time',
            'end_time',
            'total_money',
            'is_created_fee',
            'apartment_id',
            'apartment_name' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->name;
                }
                return '';
            },
            'service_map_management_id',
            'service_map_management_service_name' => function($model){
                if(!empty($model->serviceMapManagement)){
                    return $model->serviceMapManagement->service_name;
                }
                return '';
            },
            'service_map_management_service_name_en' => function($model){
                if(!empty($model->serviceMapManagement)){
                    return $model->serviceMapManagement->service_name_en;
                }
                return '';
            },
            'fee_of_month',
            'created_at',
            'updated_at',
        ];
    }
}
