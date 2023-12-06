<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceWaterInfo;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceWaterInfoResponse")
 * )
 */
class ServiceWaterInfoResponse extends ServiceWaterInfo
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="start_date", type="integer"),
     * @SWG\Property(property="end_date", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_capacity", type="integer"),
     * @SWG\Property(property="end_index", type="integer", description="Chỉ số cuối"),
     * @SWG\Property(property="config", type="object", ref="#/definitions/ServiceWaterConfigResponse"),
     * @SWG\Property(property="level", type="array",
     *     @SWG\Items(type="object", ref="#/definitions/ServiceWaterLevelResponse"),
     * ),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'start_date',
            'end_date',
            'end_index',
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
//            'define_level' => function($model){
//                $res = [];
//                if(!empty($model->serviceBuildingConfig)){
//                    $res = [
//                        'price' => $model->serviceBuildingConfig->price,
//                        'unit' => $model->serviceBuildingConfig->unit,
//                    ];
//                }
//                return $res;
//            },
            'config' => function($model){
                return ServiceWaterConfigResponse::findOne(['service_map_management_id' => $model->service_map_management_id]);
            },
            'level' => function($model){
                return ServiceWaterLevelResponse::find()->where(['service_map_management_id' => $model->service_map_management_id])->all();
            },
            'created_at',
            'updated_at',
        ];
    }
}
