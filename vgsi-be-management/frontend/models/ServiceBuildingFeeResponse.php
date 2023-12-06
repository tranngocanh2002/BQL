<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceBuildingFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBuildingFeeResponse")
 * )
 */
class ServiceBuildingFeeResponse extends ServiceBuildingFee
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="count_month", type="integer"),
     * @SWG\Property(property="start_time", type="integer"),
     * @SWG\Property(property="end_time", type="integer"),
     * @SWG\Property(property="total_money", type="integer"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="fee_of_month", type="integer"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="description_en", type="string"),
     * @SWG\Property(property="json_desc", type="object"),
     * @SWG\Property(property="service_building_config_id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_service_name", type="string"),
     * @SWG\Property(property="updated_by", type="integer"),
     * @SWG\Property(property="updated_name", type="string"),
     * @SWG\Property(property="is_paid", type="integer", description="0 - chưa thanh toán, 1 - đã thanh toán"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'count_month',
            'start_time',
            'end_time',
            'total_money',
            'status',
            'fee_of_month',
            'description',
            'description_en',
            'json_desc' => function($model){
                if(!empty($model->json_desc)){
                    return json_decode($model->json_desc, true);
                }
                return null;
            },
            'service_building_config_id',
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
            'service_map_management_id',
            'service_map_management_service_name' => function($model){
                if(!empty($model->serviceMapManagement)){
                    return $model->serviceMapManagement->service_name;
                }
                return '';
            },
            'updated_by',
            'updated_name' => function($model){
                if(!empty($model->managementUser)){
                    return $model->managementUser->first_name;
                }
                return '';
            },
            'is_paid' => function($model){
                if(!empty($model->servicePaymentFee)){
                    return $model->servicePaymentFee->status;
                }
                return 0;
            },
            'created_at',
            'updated_at',
        ];
    }
}
