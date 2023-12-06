<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceMapManagementResponse")
 * )
 */
class ServiceMapManagementResponse extends ServiceMapManagement
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="service_id", type="integer"),
     * @SWG\Property(property="service_name", type="string"),
     * @SWG\Property(property="service_name_en", type="string"),
     * @SWG\Property(property="service_type", type="integer"),
     * @SWG\Property(property="service_base_url", type="string"),
     * @SWG\Property(property="service_icon_name", type="string"),
     * @SWG\Property(property="service_description", type="string"),
     * @SWG\Property(property="service_provider_name", type="string"),
     * @SWG\Property(property="service_provider_id", type="integer"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="color", type="string"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'service_id',
            'service_name',
            'service_name_en',
            'service_type',
            'service_base_url',
            'service_icon_name',
            'service_description',
            'service_provider_id',
            'service_provider_name' => function($model){
                if($model->serviceProvider){
                    return $model->serviceProvider->name;
                }
                return '';
            },
            'status',
            'color',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias, true) : null;
            },
        ];
    }
}
