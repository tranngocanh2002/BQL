<?php

namespace resident\models;

use common\models\CardManagement;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="CardManagementResponse")
 * )
 */
class CardManagementResponse extends CardManagement
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="number", type="string"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="resident_user_id", type="integer", description="id chủ thẻ"),
     * @SWG\Property(property="resident_user_name", type="string", description="tên chủ thẻ"),
     * @SWG\Property(property="map_service", type="array", @SWG\Items(type="object", ref="#/definitions/CardManagementMapServiceResponse"),),
     */
    public function fields()
    {
        return [
            'id',
            'number',
            'status',
            'resident_user_id',
            'resident_user_name' => function($model){
                if($model->apartmentMapResidentUser){
                    return $model->apartmentMapResidentUser->resident_user_first_name;
                }
                return '';
            },
            'map_service' => function ($model) {
                return CardManagementMapServiceResponse::find()->where(['card_management_id' => $model->id])->all();
            },
        ];
    }
}
