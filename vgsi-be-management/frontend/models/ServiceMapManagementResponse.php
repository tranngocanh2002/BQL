<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceBuildingConfig;
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
     * @SWG\Property(property="service_provider_name_en", type="string"),
     * @SWG\Property(property="service_provider_id", type="integer"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="color", type="string"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="config", type="object",
     *      @SWG\Property(property="id", type="integer"),
     * ),
     * @SWG\Property(property="service_level", type="array",
     *      @SWG\Items(type="object", ref="#/definitions/ServiceWaterLevelResponse"),
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
            'service_provider_name_en' => function($model){
                if($model->serviceProvider){
                    return $model->serviceProvider->name_en;
                }
                return '';
            },
            'status',
            'color',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias, true) : null;
            },
            'config' => function($model){
                $res = ServiceBuildingConfigResponse::findOne(['service_map_management_id' => $model->id]);
                if(empty($res)){
                    $res = ServiceElectricConfigResponse::findOne(['service_map_management_id' => $model->id]);
                    if(empty($res)){
                        $res = ServiceWaterConfigResponse::findOne(['service_map_management_id' => $model->id]);
                        if(empty($res)){
                            $res = ServiceVehicleConfigResponse::findOne(['service_map_management_id' => $model->id]);
                        }
                    }
                }
                return $res;
            },
            'service_level' => function($model){
                $res = ServiceWaterLevelResponse::find()->where(['service_map_management_id' => $model->id])->all();
                if(empty($res)){
                    $res = ServiceParkingLevelResponse::find()->where(['service_map_management_id' => $model->id])->all();
                    if(empty($res)){
                        $res = ServiceElectricLevelResponse::find()->where(['service_map_management_id' => $model->id])->all();
                    }
                }
                return $res;
            }
        ];
    }
}
