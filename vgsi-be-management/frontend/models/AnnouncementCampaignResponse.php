<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementCampaignResponse")
 * )
 */
class AnnouncementCampaignResponse extends AnnouncementCampaign
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string"),
     * @SWG\Property(property="title_en", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="content_sms", type="string"),
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="status", type="integer", description="0 - bản nháp, 1 - công khai, 2 - hẹ giờ gửi"),
     * @SWG\Property(property="is_send_push", type="integer"),
     * @SWG\Property(property="is_send_email", type="integer"),
     * @SWG\Property(property="is_send_sms", type="integer"),
     * @SWG\Property(property="send_at", type="integer"),
     * @SWG\Property(property="total_apartment_send", type="integer", description="tổng đã gửi"),
     * @SWG\Property(property="total_apartment_open", type="integer", description="tổng dã mở"),
     * @SWG\Property(property="announcement_category_id", type="integer"),
     * @SWG\Property(property="announcement_category_name", type="string"),
     * @SWG\Property(property="announcement_category_color", type="string"),
     * @SWG\Property(property="is_send", type="integer", description="0 - chưa gửi , 1 - dã gửi"),
     * @SWG\Property(property="is_event", type="integer", description="0 - không phải sự kiện , 1 - là sự kiện"),
     * @SWG\Property(property="is_send_event", type="integer"),
     * @SWG\Property(property="is_survey", type="integer", description="is survey : 1 - là thông báo khảo sát, 0 - không phải thông báo khảo sát"),
     * @SWG\Property(property="type_report", type="integer", description="kiểu báo cáo 0- tính theo diện tích, 1 - tính theo đầu người"),
     * @SWG\Property(property="survey_deadline", type="integer", description="survey deadline : Thời hạn làm khảo sát"),
     * @SWG\Property(property="targets", type="array",
     *      @SWG\Items(type="integer", default=0),
     * ),
     * @SWG\Property(property="resident_user_phones", type="array",
     *      @SWG\Items(type="integer", default=0),
     * ),
     * @SWG\Property(property="send_event_at", type="integer", description="thời gian gửi sự kiện"),
     * @SWG\Property(property="building_area_ids", type="array",
     *     @SWG\Items(type="integer", default=0),
     * ),
     * @SWG\Property(property="total_email_send", type="integer"),
     * @SWG\Property(property="total_email_open", type="integer"),
     * @SWG\Property(property="total_sms_send", type="integer"),
     * @SWG\Property(property="type", type="integer"),
     * @SWG\Property(property="type_name", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="total_apartment_send_success", type="integer"),
     * @SWG\Property(property="total_email_send_success", type="integer"),
     * @SWG\Property(property="total_sms_send_success", type="integer"),
     * @SWG\Property(property="total_app_send", type="integer"),
     * @SWG\Property(property="total_app_open", type="integer"),
     * @SWG\Property(property="total_app_success", type="integer"),
     * @SWG\Property(property="apartment_ids", type="array",
     *     @SWG\Items(type="integer", default=0),
     * ),
     * @SWG\Property(property="apartment_not_send_ids", type="array",
     *     @SWG\Items(type="integer", default=0),
     * ),
     * @SWG\Property(property="add_phone_send", type="array",
     *     @SWG\Items(type="string", default=""),
     * ),
     * @SWG\Property(property="add_email_send", type="array",
     *     @SWG\Items(type="string", default=""),
     * ),
     * @SWG\Property(property="management_user", type="object", ref="#/definitions/ManagementUserMinResponse", description="người tạo"),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'title_en',
            'description',
            'content',
            'content_sms',
            'attach' => function ($model) {
                return (!empty($model->attach)) ? json_decode($model->attach) : null;
            },
            'total_apartment_send',
            'total_apartment_open',
            'announcement_category_id',
            'announcement_category_name' => function ($model) {
                if (!empty($model->announcementCategory)) {
                    return $model->announcementCategory->name;
                }
                return '';
            },
            'announcement_category_color' => function ($model) {
                /**
                 * @var $model AnnouncementCampaign
                 */
                if(!empty($model->announcementCategory)){
                    return $model->announcementCategory->label_color;
                }
                return '';
            },
            'is_send_push',
            'is_send_email',
            'is_send_sms',
            'is_send',
            'is_event',
            'send_at',
            'is_send_event',
            'send_event_at',
            'is_survey',
            'survey_deadline',
            'targets' => function($model){
                if(!empty($model->targets)){
                    return json_decode($model->targets, true);
                }
                return null;
            },
            'resident_user_phones' => function($model){
                if(!empty($model->resident_user_phones)){
                    return json_decode($model->resident_user_phones, true);
                }
                return null;
            },
            'status',
            'status' => function ($model) {
                if($model->status == AnnouncementCampaign::STATUS_ACTIVE && $model->is_event == AnnouncementCampaign::IS_EVENT && $model->send_event_at > time()){
                    return AnnouncementCampaign::STATUS_PUBLIC_AT;
                }elseif ($model->status == AnnouncementCampaign::STATUS_ACTIVE && $model->is_event == AnnouncementCampaign::IS_NOT_EVENT && $model->send_at > time()){
                    return AnnouncementCampaign::STATUS_PUBLIC_AT;
                }else{
                    return $model->status;
                }
            },
            'building_area_ids' => function ($model) {
                $announcementItemSends = $model->announcementItemSends;
                $res = [];
                foreach ($announcementItemSends as $announcementItemSend) {
                    $res[] = $announcementItemSend->building_area_id;
                }
                return $res;
            },
            'total_email_send',
            'total_email_open',
            'total_sms_send',
            'type',
            'type_name' => function($model){
                return AnnouncementCampaign::$typeList[$model->type];
            },
            'total_apartment_send_success',
            'total_email_send_success',
            'total_sms_send_success',
            'total_app_send',
            'total_app_open',
            'total_app_success',
            'apartment_ids' => function ($model) {
               if(!empty($model->apartment_ids)){
                   return Json::decode($model->apartment_ids, true);
               }
               return [];
            },
            'apartment_not_send_ids' => function ($model) {
               if(!empty($model->apartment_not_send_ids)){
                   return Json::decode($model->apartment_not_send_ids, true);
               }
               return [];
            },
            'add_phone_send' => function ($model) {
               if(!empty($model->add_phone_send)){
                   return Json::decode($model->add_phone_send, true);
               }
               return [];
            },
            'add_email_send' => function ($model) {
               if(!empty($model->add_email_send)){
                   return Json::decode($model->add_email_send, true);
               }
               return [];
            },
            'type_report',
            'management_user' => function($model){
                return ManagementUserMinResponse::findOne(['id' => $model->created_by]);
            },
            'created_at',
            'updated_at',
        ];
    }
}
