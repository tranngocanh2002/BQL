<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceBuildingInfo;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBuildingInfoResponse")
 * )
 */
class ServiceBuildingInfoResponse extends ServiceBuildingInfo
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="start_date", type="integer"),
     * @SWG\Property(property="end_date", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_capacity", type="integer"),
     * @SWG\Property(property="define_level", type="object",
     *      @SWG\Property(property="price", type="integer", description="Giá"),
     *      @SWG\Property(property="unit", type="integer", description="0 - giá / m2 , 1 - giá / căn hộ"),
     * ),
     * @SWG\Property(property="config", type="object", ref="#/definitions/ServiceBuildingConfigResponse"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'start_date',
            'end_date',
            'apartment_id',
            'apartment_name' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_capacity' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->capacity;
                }
                return 0;
            },
            'define_level' => function($model){
                $res = [];
                if(!empty($model->serviceBuildingConfig)){
                    $res = [
                        'price' => $model->serviceBuildingConfig->price,
                        'unit' => $model->serviceBuildingConfig->unit,
                    ];
                }
                return $res;
            },
            'config' => function($model){
                return ServiceBuildingConfigResponse::findOne(['service_map_management_id' => $model->service_map_management_id]);
            },
            'created_at',
            'updated_at',
        ];
    }
}
