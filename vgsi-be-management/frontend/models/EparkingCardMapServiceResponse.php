<?php

namespace frontend\models;

use common\models\CardManagement;
use common\models\CardManagementMapService;
use common\models\ResidentUser;
use common\models\ServiceManagementVehicle;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="EparkingCardMapServiceResponse")
 * )
 */
class EparkingCardMapServiceResponse extends CardManagementMapService
{
    /**
     * @SWG\Property(property="serial", type="string"),
     * @SWG\Property(property="card_type", type="integer"),
     * @SWG\Property(property="customer_id", type="integer"),
     * @SWG\Property(property="start_datetime", type="string"),
     * @SWG\Property(property="end_datetime", type="string"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="price", type="integer"),
     * @SWG\Property(property="plate", type="string"),
     * @SWG\Property(property="vehicle_type", type="integer"),
     */
    public function fields()
    {
        return [
            'serial' => function($model){
                if(!empty($model->cardManagement)){
                    return $model->cardManagement->number;
                }
                return '';
            },
            'card_type' => function($model){
                if(!empty($model->cardManagement)){
                    return $model->cardManagement->type;
                }
                return 0;
            },
            'customer_id' => function($model){
                if(!empty($model->cardManagement)){
                    return $model->cardManagement->resident_user_id;
                }
                return 0;
            },
            'start_datetime' => function($model){
                return '2019/10/01 00:00:00';
            },
            'end_datetime' => function($model){
                return date('Y/m/d H:i:s', $model->expiry_time);
            },
            'status' => function($model){
                $status = $model->status;
                if($model->status !== CardManagementMapService::STATUS_ACTIVE){
                    $status = CardManagementMapService::STATUS_CREATE;
                }
                if(!empty($model->cardManagement)){
                    if($model->cardManagement->status !== CardManagement::STATUS_ACTIVE){
                        $status = CardManagement::STATUS_CREATE;
                    };
                }
                return $status;
            },
            'price' => function($model){
                return 0;
            },
            'plate' => function ($model) {
                if($model->type == CardManagementMapService::TYPE_PARKING){
                    $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $model->service_management_id]);
                    if(!empty($serviceManagementVehicle)){
                        return $serviceManagementVehicle->number;
                    }
                }
                return '';
            },
            'vehicle_type' => function ($model) {
                if($model->type == CardManagementMapService::TYPE_PARKING){
                    $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $model->service_management_id]);
                    if(!empty($serviceManagementVehicle)){
                        if(!empty($serviceManagementVehicle->serviceParkingLevel)){
                            if($serviceManagementVehicle->serviceParkingLevel->code == 'MX1'){
                                return 2;
                            }else if($serviceManagementVehicle->serviceParkingLevel->code == 'MX2'){
                                return 1;
                            }
                        }
                    }
                }
                return 0;
            },
        ];
    }
}
