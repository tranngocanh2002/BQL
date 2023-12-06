<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceProvider;
use common\models\ServiceProviderBillingInfo;
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
     * @SWG\Property(property="address", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="billing_info", type="object",
     *     ref="#/definitions/ServiceProviderBillingInfoResponse",
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'address',
            'description',
            'status',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias, true) : null;
            },
            'billing_info' => function($model){
                /**
                 * @var $model ServiceProvider
                 */
                if(count($model->serviceProviderBillingInfo) > 0){
                    $billInfo = new ServiceProviderBillingInfoResponse();
                    $billInfo->load($model->serviceProviderBillingInfo[0]->toArray(), '');
                    return $billInfo;
                }
                return null;
            },
            'created_at',
            'updated_at',
        ];
    }
}
