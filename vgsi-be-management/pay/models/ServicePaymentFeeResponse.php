<?php

namespace pay\models;

use common\helpers\ErrorCode;
use common\models\ServiceBill;
use common\models\ServiceBillItem;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServicePaymentFeeResponse")
 * )
 */
class ServicePaymentFeeResponse extends ServicePaymentFee
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_service_name", type="string"),
     * @SWG\Property(property="service_map_management_service_name_en", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="json_desc", type="object"),
     * @SWG\Property(property="price", type="integer"),
     * @SWG\Property(property="money_collected", type="integer", description="số tiền đã thu được"),
     * @SWG\Property(property="more_money_collecte", type="integer", description="số tiền cần thu thêm"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="fee_of_month", type="integer"),
     * @SWG\Property(property="day_expired", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'service_map_management_id',
            'service_map_management_service_name' => function ($model) {
                if (!empty($model->serviceMapManagement)) {
                    return $model->serviceMapManagement->service_name;
                }
                return '';
            },
            'service_map_management_service_name_en' => function ($model) {
                if (!empty($model->serviceMapManagement)) {
                    return $model->serviceMapManagement->service_name_en;
                }
                return '';
            },
            'description',
            'json_desc' => function($model){
                if(!empty($model->json_desc)){
                    return json_decode($model->json_desc, true);
                }
                return null;
            },
            'price',
            'money_collected',
            'more_money_collecte',
            'status',
            'fee_of_month',
            'day_expired',
            'created_at',
            'updated_at',
        ];
    }
}
