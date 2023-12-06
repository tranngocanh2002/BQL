<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\AnnouncementSurvey;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceDebt;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementItemResponse")
 * )
 */
class AnnouncementSurveyResponse extends AnnouncementSurvey
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="apartment", type="object",
     *     @SWG\Property(property="id", type="integer"),
     *     @SWG\Property(property="name", type="string"),
     *     @SWG\Property(property="code", type="string", description="Mã bất động sản"),
     *     @SWG\Property(property="parent_path", type="string", description="Lô/Tầng"),
     * ),
     * @SWG\Property(property="resident_user", type="object",
     *     @SWG\Property(property="id", type="integer"),
     *     @SWG\Property(property="phone", type="string"),
     *     @SWG\Property(property="email", type="string"),
     *     @SWG\Property(property="first_name", type="string"),
     *     @SWG\Property(property="last_name", type="string"),
     *     @SWG\Property(property="active_app", type="integer"),
     * ),
     * @SWG\Property(property="status", type="integer", description="0- chưa làm, 1 - đồng ý, 2 - không đồng ý"),
     * @SWG\Property(property="updated_at", type="integer", description="Thời gian thực hiện"),
     */
    public function fields()
    {
        return [
            'id',
            'apartment' => function ($model) {
                if (!empty($model->apartment)) {
                    return [
                        'id' => $model->apartment->id,
                        'name' => $model->apartment->name,
                        'code' => $model->apartment->code,
                        'parent_path' => trim($model->apartment->parent_path, '/')
                    ];
                }
                return '';
            },
            'resident_user' => function($model){
                if (!empty($model->residentUser)) {
                    $apartmentMapResidentUser = ApartmentMapResidentUser::findOne([
                        'apartment_id' => $model->apartment_id,
                        'resident_user_phone' => $model->residentUser->phone,
                        'is_deleted' => ApartmentMapResidentUser::NOT_DELETED
                    ]);
                    if(!empty($apartmentMapResidentUser)){
                        return [
                            'id' => $model->residentUser->id,
                            'phone' => $model->residentUser->phone,
                            'email' => $apartmentMapResidentUser->resident_user_email,
                            'first_name' => $apartmentMapResidentUser->resident_user_first_name,
                            'last_name' => $apartmentMapResidentUser->resident_user_last_name,
                            'active_app' => $model->residentUser->active_app
                        ];
                    }
                }
                return null;
            },
            'status',
            'updated_at', 
        ];
    }
}
