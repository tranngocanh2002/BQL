<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\Service;
use common\models\ServiceMapManagement;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceResponse")
 * )
 */
class ServiceResponse extends Service
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="description_en", type="string"),
     * @SWG\Property(property="logo", type="string"),
     * @SWG\Property(property="base_url", type="string"),
     * @SWG\Property(property="icon_name", type="string"),
     * @SWG\Property(property="color", type="string"),
     * @SWG\Property(property="service_type", type="integer", description="0 - Điện, 1 - Nước, 2 - Vệ sinh , 3 - Tiện ích, 4 - Gửi xe, 5 - Nợ cũ chuyển giao..."),
     * @SWG\Property(property="type", type="integer", description="0 - Dịch vụ hệ thống, 1 - Dịch vụ phát sinh"),
     * @SWG\Property(property="type_target", type="integer", description="0 - Theo phòng, 1 - Theo cư dân"),
     * @SWG\Property(property="is_map_management", type="integer", description="0 - chưa map , 1 - đã map"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'description',
            'logo',
            'base_url',
            'icon_name',
            'service_type',
            'type',
            'type_target',
            'color',
            'is_map_management' => function($model){
                $is_map = 0;
                $buildingCluster = Yii::$app->building->BuildingCluster;
                if(!empty($buildingCluster)){
                    $serviceMapManagement = ServiceMapManagement::findOne(['service_id' => $model->id, 'building_cluster_id' => $buildingCluster->id, 'is_deleted' => ServiceMapManagement::NOT_DELETED]);
                    if(!empty($serviceMapManagement)){
                        $is_map = 1;
                    }
                }
                return $is_map;
            },
            'name_en',
            'description_en'
        ];
    }
}
