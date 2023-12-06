<?php

namespace resident\models;

use common\models\ApartmentMapResidentUser;
use common\models\Request;
use common\models\RequestAnswer;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestAnswerResponse")
 * )
 */
class RequestAnswerResponse extends RequestAnswer
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="request_id", type="integer"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="resident_user_phone", type="string"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="resident_user_avatar", type="string"),
     * @SWG\Property(property="management_user_id", type="integer"),
     * @SWG\Property(property="management_user_email", type="string"),
     * @SWG\Property(property="management_user_name", type="string"),
     * @SWG\Property(property="management_user_avatar", type="string"),
     * @SWG\Property(property="management_user_auth_group_name", type="string"),
     * @SWG\Property(property="management_user_auth_group_name_en", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'request_id',
            'resident_user_id',
            'resident_user_phone' => function ($model) {
                if (!empty($model->residentUser)) {
                    return $model->residentUser->phone;
                }
                return '';
            },
            'resident_user_name' => function ($model) {
                /**
                 * @var $model RequestAnswer
                 */
                if (!empty($model->residentUser)) {
                    $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $model->residentUser->phone, 'apartment_id' => $model->request->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                    if(!empty($apartmentMapResidentUser)){
                        return $apartmentMapResidentUser->resident_user_first_name;
                    }
                }
                return '';
            },
//            'resident_user_name' => function ($model) {
//                if (!empty($model->residentUser)) {
//                    return $model->residentUser->first_name;
//                }
//                return '';
//            },
            'resident_user_avatar' => function ($model) {
                if (!empty($model->residentUser)) {
                    return $model->residentUser->avatar;
                }
                return '';
            },
            'management_user_id',
            'management_user_email' => function ($model) {
                if (!empty($model->managementUser)) {
                    return $model->managementUser->email;
                }
                return '';
            },
            'management_user_name' => function ($model) {
                if (!empty($model->managementUser)) {
                    return $model->managementUser->first_name;
                }
                return '';
            },
            'management_user_avatar' => function ($model) {
                if (!empty($model->managementUser)) {
                    return $model->managementUser->avatar;
                }
                return '';
            },
            'management_user_auth_group_name' => function ($model) {
                if (!empty($model->managementUser)) {
                    if(!empty($model->managementUser->authGroup)){
                        return $model->managementUser->authGroup->name;
                    }
                }
                return '';
            },
            'management_user_auth_group_name_en' => function ($model) {
                if (!empty($model->managementUser)) {
                    if(!empty($model->managementUser->authGroup)){
                        return $model->managementUser->authGroup->name_en;
                    }
                }
                return '';
            },
            'content',
            'created_at',
            'attach' => function ($model) {
                return (!empty($model->attach)) ? json_decode($model->attach, true) : null;
            },
        ];
    }
}
