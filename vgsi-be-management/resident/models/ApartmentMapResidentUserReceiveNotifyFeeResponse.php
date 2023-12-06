<?php

namespace resident\models;

use common\models\Post;
use common\models\ApartmentMapResidentUserReceiveNotifyFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserReceiveNotifyFeeResponse")
 * )
 */
class ApartmentMapResidentUserReceiveNotifyFeeResponse extends ApartmentMapResidentUserReceiveNotifyFee
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="email", type="string"),
     */
    public function fields()
    {
        return [
            'id',
            'apartment_id',
            'apartment_name' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->name;
                }
                return '';
            },
            'resident_user_id',
            'resident_user_name' => function($model){
                if(!empty($model->apartmentMapResidentUser)){
                    return $model->apartmentMapResidentUser->resident_user_first_name;
                }
                return '';
            },
            'phone',
            'email',
        ];
    }
}
