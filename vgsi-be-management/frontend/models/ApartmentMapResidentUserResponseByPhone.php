<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserResponseByPhone")
 * )
 */
class ApartmentMapResidentUserResponseByPhone extends ApartmentMapResidentUser
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="first_name", type="string"),
     * @SWG\Property(property="last_name", type="string"),
     */


    public function fields()
    {
        return [
            'id' => function($model){
                return $model->resident_user_id;
            },
            'phone' => function($model){
                return $model->resident_user_phone;
            },
            'first_name' => function($model){
                return $model->resident_user_first_name;
            },
            'last_name' => function($model){
                return $model->resident_user_last_name;
            },
        ];
    }
}
