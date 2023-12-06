<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceUtilityConfig;
use common\models\ServiceUtilityFree;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityConfigResponse")
 * )
 */
class ServiceUtilityConfigResponse extends ServiceUtilityConfig
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="address", type="string"),
     * @SWG\Property(property="address_en", type="string"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="service_utility_free_id", type="integer"),
     * @SWG\Property(property="service_utility_free_id_name", type="string"),
     * @SWG\Property(property="type", type="integer", description="0 - không thu phí, 1 - có thu phí"),
     * @SWG\Property(property="booking_type", type="integer", description="0 - đặt theo lượt, 1 - đặt theo slot"),
     * @SWG\Property(property="total_slot", type="integer", description="Tổng số chỗ"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'name_en',
            'address',
            'address_en',
            'building_cluster_id',
            'service_utility_free_id',
            'service_utility_free_id_name' => function($model){
                if(!empty($model->serviceUtilityFree)){
                    return $model->serviceUtilityFree->name;
                }
                return '';
            },
            'type',
            'booking_type',
            'total_slot',
            'created_at',
            'updated_at',
        ];
    }
}
