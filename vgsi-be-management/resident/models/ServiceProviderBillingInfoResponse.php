<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceProviderBillingInfo;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceProviderBillingInfoResponse")
 * )
 */
class ServiceProviderBillingInfoResponse extends ServiceProviderBillingInfo
{
    /**
     * @SWG\Property(property="cash_instruction", type="string"),
     * @SWG\Property(property="transfer_instruction", type="string"),
     * @SWG\Property(property="bank_name", type="string"),
     * @SWG\Property(property="bank_number", type="string"),
     * @SWG\Property(property="bank_holders", type="string"),
     * @SWG\Property(property="service_provider_id", type="integer"),
     * @SWG\Property(property="service_provider_name", type="string"),
     */
    public function fields()
    {
        return [
            'cash_instruction',
            'transfer_instruction',
            'bank_name',
            'bank_number',
            'bank_holders',
            'service_provider_id',
            'service_provider_name' => function($model){
                return $model->serviceProvider->name;
            },
        ];
    }
}
