<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserResponse")
 * )
 */
class ResidentUserResponse extends ResidentUser
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="email", type="string"),
     * @SWG\Property(property="first_name", type="string"),
     * @SWG\Property(property="last_name", type="string"),
     * @SWG\Property(property="avatar", type="string"),
     * @SWG\Property(property="gender", type="integer"),
     * @SWG\Property(property="birthday", type="integer"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="status_verify_phone", type="integer"),
     * @SWG\Property(property="status_verify_email", type="integer"),
     * @SWG\Property(property="notify_tags", type="array",
     *     @SWG\Items(type="string", default= "BUILDING_CLUSTER_1"),
     * ),
     * @SWG\Property(property="apartments", type="array",
     *     @SWG\Items(type="object",
     *          @SWG\Property(property="apartment_id", type="integer", description="Id căn hộ"),
     *          @SWG\Property(property="apartment_name", type="string", description="Tên căn hộ"),
     *          @SWG\Property(property="apartment_capacity", type="string", description="Diện tích căn hộ"),
     *          @SWG\Property(property="type", type="integer", description="0 - thành viên, 1 - chủ hộ"),
     *      ),
     * ),
     */


    public function fields()
    {
        return [
            'id',
            'phone',
            'email',
            'first_name',
            'first_name',
            'avatar',
            'gender' => function($model){
                if(empty($model->gender)){
                    return ResidentUser::GENDER_1;
                }
                return $model->gender;
            },
            'birthday',
            'status',
            'status_verify_phone',
            'status_verify_email',
            'notify_tags' => function ($model) {
                return (!empty($model->notify_tags)) ? json_decode($model->notify_tags) : [];
            },
            'apartments' => function ($model) {
                $res = [];
                $apartmentMapResidentUsers = $model->apartmentMapResidentUsers;
                foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
//                    $apartment = Apartment::findOne(['id' => $apartmentMapResidentUser->apartment_id]);
                    $res[] = [
                        'type' => $apartmentMapResidentUser->type,
                        'apartment_id' => $apartmentMapResidentUser->apartment_id,
                        'apartment_name' => $apartmentMapResidentUser->apartment_name,
                        'apartment_capacity' => $apartmentMapResidentUser->apartment_capacity,
                    ];
                }
                return $res;
            },
        ];
    }
}
