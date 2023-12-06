<?php

namespace frontend\models;

use common\models\PaymentGenCodeItem;
use common\models\ServicePaymentFee;
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
        ];
    }
}
