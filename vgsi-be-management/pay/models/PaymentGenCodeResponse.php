<?php

namespace api\models;

use common\models\PaymentGenCode;
use pay\models\ServicePaymentFeeResponse;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentGenCodeResponse")
 * )
 */
class PaymentGenCodeResponse extends PaymentGenCode
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="service_payment_fees", type="array",
     *      @SWG\Items(type="object", ref="#/definitions/ServicePaymentFeeResponse")
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'apartment_id',
            'apartment_name' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_parent_path' => function($model){
                if(!empty($model->apartment)){
                    return trim($model->apartment->parent_path,'/');
                }
                return '';
            },
            'code',
            'service_payment_fees' => function($model){
                return ServicePaymentFeeResponse::find()->where(['id' => json_decode($model->service_payment_fee_ids, true)])->all();
            },
        ];
    }
}
