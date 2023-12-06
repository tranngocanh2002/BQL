<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceBillItem;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBillItemResponse")
 * )
 */
class ServiceBillItemResponse extends ServiceBillItem
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="price", type="integer"),
     * @SWG\Property(property="fee_of_month", type="integer"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_name", type="string"),
     * @SWG\Property(property="service_payment_fee",  type="object", ref="#/definitions/ServicePaymentFeeResponse"),
     */
    public function fields()
    {
        return [
            'id',
            'description' => function($model){
                if(!empty($model->servicePaymentFee)){
                    return $model->servicePaymentFee->description;
                }
                return $model->description;
            },
            'price',
            'fee_of_month',
            'service_map_management_id',
            'service_map_management_name' => function($model){
                return $model->serviceMapManagement->service_name;
            },
            'service_map_management_name_en' => function($model){
                return $model->serviceMapManagement->service_name_en;
            },
            'service_map_management_service_icon_name' => function($model){
                return $model->serviceMapManagement->service_icon_name;
            },
            'service_payment_fee' => function($model){
                if($model->servicePaymentFee){
                    return \resident\models\ServicePaymentFeeResponse::findOne(['id' => $model->servicePaymentFee->id]);
                }
                return '';
            }
        ];
    }
}
