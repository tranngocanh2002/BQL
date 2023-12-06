<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceBuildingInfo;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBuildingInfoResponse")
 * )
 */
class ServiceBuildingInfoResponse extends ServiceBuildingInfo
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="start_date", type="integer"),
     * @SWG\Property(property="end_date", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_capacity", type="string"),
     * @SWG\Property(property="apartment_date_received", type="integer"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_service_name", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'start_date',
            'end_date',
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
                return '';
            },
            'apartment_date_received' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->date_received;
                }
                return '';
            },
            'apartment_parent_path' => function($model){
                if(!empty($model->apartment)){
                    return trim($model->apartment->parent_path, '/');
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
            'created_at',
            'updated_at',
        ];
    }
}
