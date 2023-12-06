<?php

namespace frontend\models;

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
     * @SWG\Property(property="service_payment_fee_id", type="integer"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_name", type="string"),
     */
    public function fields()
    {
        return [
            'id',
            'description',
            'price',
            'fee_of_month',
            'service_payment_fee_id',
            'service_map_management_id',
            'service_map_management_name' => function($model){
                return $model->serviceMapManagement->service_name;
            },
        ];
    }
}
