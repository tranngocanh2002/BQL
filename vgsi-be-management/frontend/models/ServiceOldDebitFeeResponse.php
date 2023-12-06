<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceOldDebitFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceOldDebitFeeResponse")
 * )
 */
class ServiceOldDebitFeeResponse extends ServiceOldDebitFee
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="description_en", type="string"),
     * @SWG\Property(property="json_desc", type="object"),
     * @SWG\Property(property="total_money", type="integer"),
     * @SWG\Property(property="fee_of_month", type="integer"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="is_created_fee", type="integer", description="0 - chưa tạo phí, 1 - đã tạo phí -> không được thao tác gì nữa"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_service_name", type="string"),
     * @SWG\Property(property="is_paid", type="integer", description="0 - chưa thanh toán, 1 - đã thanh toán"),
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
            'total_money',
            'fee_of_month',
            'status',
            'is_created_fee',
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
