<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceWaterConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceWaterConfigResponse")
 * )
 */
class ServiceWaterConfigResponse extends ServiceWaterConfig
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="type", type="integer"),
     * @SWG\Property(property="type_name", type="string"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_service_name", type="string"),
     * @SWG\Property(property="percent", type="integer", description="% + cộng thêm lên fee"),
     * @SWG\Property(property="is_vat", type="integer", description="1- đã bảo gồm vat, 0  - chưa bao gồm vat"),
     * @SWG\Property(property="vat_percent", type="integer", description="% + thuế bql"),
     * @SWG\Property(property="vat_dvtn", type="integer", description="% + thuế dịch vụ thoát nước"),
     * @SWG\Property(property="environ_percent", type="integer", description="% + phí bảo vệ môi trường"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'type',
            'type_name' => function($model){
                return ServiceWaterConfig::$arrType[$model->type];
            },
            'service_map_management_id',
            'service_map_management_service_name' => function($model){
                if(!empty($model->serviceMapManagement)){
                    return $model->serviceMapManagement->service_name;
                }
                return '';
            },
            'percent',
            'is_vat',
            'vat_percent',
            'vat_dvtn',
            'environ_percent',
            'created_at',
            'updated_at',
        ];
    }
}
