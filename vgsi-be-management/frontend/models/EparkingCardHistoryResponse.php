<?php

namespace frontend\models;

use common\models\EparkingCardHistory;
use common\models\ServiceManagementVehicle;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="EparkingCardHistoryResponse")
 * )
 */
class EparkingCardHistoryResponse extends EparkingCardHistory
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="service_management_vehicle_id", type="integer", description="id xe"),
     * @SWG\Property(property="number", type="string", description="Biển số xe"),
     * @SWG\Property(property="status", type="integer", description="1 - vào, 2- ra"),
     * @SWG\Property(property="datetime", type="integer", description="Thời điểm vào / ra"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="integer"),
     * @SWG\Property(property="apartment_parent_path", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'service_management_vehicle_id',
            'number' => function($model){
                if(!empty($model->plate_in)){
                    return $model->plate_in;
                }else{
                    return $model->plate_out;
                }
            },
            'datetime' => function($model){
                if($model->status == EparkingCardHistory::STATUS_P){
                    return $model->datetime_in;
                }else{
                    return $model->datetime_out;
                }
            },
            'status',
            'apartment_id',
            'apartment_name' => function ($model) {
                if (!empty($model->apartment)) {
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_parent_path' => function ($model) {
                if (!empty($model->apartment)) {
                    return $model->apartment->parent_path;
                }
                return '';
            },
        ];
    }
}
