<?php

namespace resident\models;

use common\models\CardManagementMapService;
use common\models\ResidentUser;
use common\models\ServiceManagementVehicle;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="CardManagementMapServiceResponse")
 * )
 */
class CardManagementMapServiceResponse extends CardManagementMapService
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="service_management_name", type="string"),
     * @SWG\Property(property="service_management_id", type="integer"),
     * @SWG\Property(property="expiry_time", type="integer"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="type", type="integer"),
     * @SWG\Property(property="type_name", type="string"),
     */
    public function fields()
    {
        return [
            'id',
            'expiry_time',
            'type',
            'type_name' => function($model){
                return CardManagementMapService::$type_lst[$model->type];
            },
            'status',
            'service_management_id',
            'service_management_name' => function ($model) {
                if($model->type == CardManagementMapService::TYPE_PARKING){
                    $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $model->service_management_id]);
                    if(!empty($serviceManagementVehicle)){
                        return $serviceManagementVehicle->number;
                    }
                }else if($model->type == CardManagementMapService::TYPE_RESIDENT_USER){
                    $residentUser = ResidentUser::findOne(['id' => $model->service_management_id]);
                    if(!empty($residentUser)){
                        return $residentUser->first_name . ' ' . $residentUser->last_name;
                    }
                }
                return '';
            },
        ];
    }
}
