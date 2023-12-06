<?php

namespace resident\models;

use common\models\PaymentGenCodeItem;
use common\models\ServicePaymentFee;
use pay\models\ServicePaymentFeeResponse;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentGenCodeItemResponse")
 * )
 */
class PaymentGenCodeItemResponse extends PaymentGenCodeItem
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="service_payment_fee_id", type="integer"),
     * @SWG\Property(property="amount", type="integer"),
     * @SWG\Property(property="service_payment_fee_type", type="string"),
     * @SWG\Property(property="service_payment_fee",  type="object", ref="#/definitions/ServicePaymentFeeResponse"),
     */
    public function fields()
    {
        return [
            'id',
            'amount',
            'service_payment_fee_id',
            'service_payment_fee_type' => function($model){
                if($model->servicePaymentFee){
                    return ServicePaymentFee::$typeList[$model->servicePaymentFee->type];
                }
                return '';
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
