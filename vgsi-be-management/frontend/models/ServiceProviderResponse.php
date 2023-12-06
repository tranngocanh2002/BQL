<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceProvider;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceProviderResponse")
 * )
 */
class ServiceProviderResponse extends ServiceProvider
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="address", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="using_bank_cluster", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="billing_info", type="object",
     *     ref="#/definitions/ServiceProviderBillingInfoForm"
     * ),
     * @SWG\Property(property="payment_config", type="object", ref="#/definitions/PaymentConfigResponse"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'name_en',
            'address',
            'description',
            'status',
            'using_bank_cluster',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias, true) : null;
            },
            'billing_info' => function($model){
                return ServiceProviderBillingInfoResponse::findOne(['service_provider_id' => $model->id]);
            },
            'payment_config' => function($model){
                return PaymentConfigResponse::findOne(['building_cluster_id' => $model->building_cluster_id, 'service_provider_id' => $model->id]);
            },
            'created_at',
            'updated_at',
        ];
    }
}
