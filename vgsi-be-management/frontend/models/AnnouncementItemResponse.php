<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\ServiceDebt;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementItemResponse")
 * )
 */
class AnnouncementItemResponse extends AnnouncementItem
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_area_id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="content_sms", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="resident_user_name", type="string", description="chủ hộ"),
     * @SWG\Property(property="email", type="string", description="email"),
     * @SWG\Property(property="phone", type="string", description="phone"),
     * @SWG\Property(property="read_at", type="integer", description="read_at - thời điểm đọc trên app - có thời gian tức là đã đọc"),
     * @SWG\Property(property="read_email_at", type="integer", description="read_email_at - thời điểm đọc email - có thời gian tức là đã đọc"),
     * @SWG\Property(property="end_debt", type="integer", description="nợ cuối kỳ"),
     * @SWG\Property(property="type", type="integer"),
     * @SWG\Property(property="app", type="integer", description="0 chưa cài, 1 đã cài"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="status_sms", type="integer", description="0- đã gửi, 1 - gửi thành công, 2 - gửi lỗi"),
     * @SWG\Property(property="status_email", type="integer", description="0- đã gửi, 1 - gửi thành công, 2 - gửi lỗi"),
     * @SWG\Property(property="status_notify", type="integer", description="0- đã gửi, 1 - gửi thành công, 2 - gửi lỗi"),
     */
    public function fields()
    {
        return [
            'id',
            'building_area_id',
            'apartment_id',
            'apartment_name' => function ($model) {
                if (!empty($model->apartment)) {
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_parent_path' => function ($model) {
                if (!empty($model->apartment)) {
                    return trim($model->apartment->parent_path, '/');
                }
                return '';
            },
            'app' => function($model){
                if(!empty($model->apartment_id) && !empty($model->phone))
                {
                    $dataResult = ApartmentMapResidentUser::findOne(['apartment_id'=> $model->apartment_id,'resident_user_phone'=>$model->phone,'is_deleted'=>ApartmentMapResidentUser::NOT_DELETED]);
                    if(!empty($dataResult))
                    {
                        return $dataResult->install_app;
                    }
                }
                if(!empty($model->device_token)){
                    if($model->apartment){
                        if($model->apartment->residentUser){
                            return $model->apartment->residentUser->active_app;
                        }
                    }
                }
                return 0;
            },
            'resident_user_name',
            'content',
            'content_sms',
            'email',
            'phone',
            'read_at',
            'read_email_at',
            'end_debt',
            'type',
            'created_at',
            'status_sms',
            'status_email',
            'status_notify',
        ];
    }
}
