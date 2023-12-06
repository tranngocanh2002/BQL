<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\AnnouncementSurvey;
use common\models\rbac\AuthGroup;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

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
     * @SWG\Property(property="announcement_campaign", type="object",
     *      @SWG\Property(property="id", type="integer", description="id khảo sát"),
     *      @SWG\Property(property="title", type="string"),
     *      @SWG\Property(property="title_en", type="string"),
     *      @SWG\Property(property="description", type="string"),
     *      @SWG\Property(property="content", type="string"),
     *      @SWG\Property(property="attach", type="object",
     *          @SWG\Property(property="key1", type="integer"),
     *          @SWG\Property(property="key2", type="string"),
     *      ),
     *      @SWG\Property(property="announcement_category_id", type="integer"),
     *      @SWG\Property(property="announcement_category_name", type="string"),
     *      @SWG\Property(property="announcement_category_name_en", type="string"),
     *      @SWG\Property(property="announcement_category_label_color", type="string"),
     *      @SWG\Property(property="send_at", type="integer"),
     *      @SWG\Property(property="send_event_at", type="integer"),
     *      @SWG\Property(property="is_survey", type="integer", description="is survey : 1 - là thông báo khảo sát, 0 - không phải thông báo khảo sát"),
     *      @SWG\Property(property="type_report", type="integer", description="kiểu báo cáo 0- tính theo diện tích, 1 - tính theo đầu người"),
     *      @SWG\Property(property="survey_deadline", type="integer", description="survey deadline : Thời hạn làm khảo sát"),
     *      @SWG\Property(property="voted", type="integer", description="0: chưa vote, 1: đồng ý, 2: không đồng ý"),
     *      @SWG\Property(property="targets", type="array", description="đối tượng nhận thông báo [0,1,2, ...]: 0 - chủ hộ, 1 - thành viên, 2 - khách thuê",
     *          @SWG\Items(type="integer", default=0),
     *      ),
     * ),
     * @SWG\Property(property="author", type="object",
     *      @SWG\Property(property="id", type="integer"),
     *      @SWG\Property(property="name", type="string"),
     *      @SWG\Property(property="name_en", type="string"),
     *      @SWG\Property(property="management_user_name", type="string"),
     *      @SWG\Property(property="management_user_avatar", type="string"),
     * ),
     * @SWG\Property(property="apartment", type="object",
     *      @SWG\Property(property="id", type="integer"),
     *      @SWG\Property(property="name", type="string"),
     *      @SWG\Property(property="parent_path", type="string"),
     * ),
     * @SWG\Property(property="label_color", type="string"),
     * @SWG\Property(property="is_hidden", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        $user = Yii::$app->user->getIdentity();
        return [
            'id',
            'announcement_campaign' => function($model) use ($user) {
                $announcementCampaign = $model->announcementCampaign;
                $content = $announcementCampaign->content;
                if(!empty($model->content) && $announcementCampaign->type > AnnouncementCampaign::TYPE_DEFAULT){
                    $content = $model->content;
                }
                $description = $announcementCampaign->description;
                if(!empty($model->description) && $announcementCampaign->type > AnnouncementCampaign::TYPE_DEFAULT){
                    $description = $model->description;
                }
                $vote_status = AnnouncementSurvey::STATUS_DEFAULT;
                $checkVote = AnnouncementSurvey::find()
                ->where(['announcement_campaign_id' => $announcementCampaign->id, 'resident_user_id' => $user->id])
                ->andWhere(['<>', 'status', AnnouncementSurvey::STATUS_DEFAULT])
                ->one();
                if(!empty($checkVote)){
                    $vote_status = $checkVote->status;
                }
                return [
                    'id' => $announcementCampaign->id,
                    'title' => $announcementCampaign->title,
                    'title_en' => $announcementCampaign->title_en,
                    'description' => $description,
                    'content' => $content,
                    'send_at' => $announcementCampaign->send_at,
                    'send_event_at' => $announcementCampaign->send_event_at,
                    'attach' => !empty($announcementCampaign->attach) ? Json::decode($announcementCampaign->attach, true) : null,
                    'announcement_category_id' => $announcementCampaign->announcement_category_id,
                    'announcement_category_name' => ($announcementCampaign->announcementCategory) ? $announcementCampaign->announcementCategory->name : '',
                    'announcement_category_name_en' => ($announcementCampaign->announcementCategory) ? $announcementCampaign->announcementCategory->name_en : '',
                    'announcement_category_label_color' => ($announcementCampaign->announcementCategory) ? $announcementCampaign->announcementCategory->label_color : '',
                    'is_survey' => $announcementCampaign->is_survey,
                    'survey_deadline' => $announcementCampaign->survey_deadline,
                    'type_report' => $announcementCampaign->type_report,
                    'voted' => $vote_status,
                    'targets' => !empty($announcementCampaign->targets) ? json_decode($announcementCampaign->targets, true) : null,
                ];
            },
            'author' => function($model){
                $announcementCampaign = $model->announcementCampaign;
                $name = '';
                $name_en = '';
                $management_user_name = '';
                $management_user_avatar = '';
                if(!empty($announcementCampaign->managementUser)){
                    $management_user_name = $announcementCampaign->managementUser->first_name;
                    $management_user_avatar = $announcementCampaign->managementUser->avatar;
                    if(!empty($announcementCampaign->managementUser->authGroup)){
                        $name = AuthGroup::$type_list[$announcementCampaign->managementUser->authGroup->type];
                        $name_en = AuthGroup::$type_list_en[$announcementCampaign->managementUser->authGroup->type];
                    }
                }
                return [
                    'id' => $announcementCampaign->created_by,
                    'name' => $name,
                    'name_en' => $name_en,
                    'management_user_name' => $management_user_name,
                    'management_user_avatar' => $management_user_avatar,
                ];
            },
            'apartment' => function($model){
                $apartment = $model->apartment;
                return [
                    'id' => $apartment->id,
                    'name' => $apartment->name,
                    'parent_path' => trim($apartment->buildingArea->parent_path, '/'),
                ];
            },
            'read_at',
            'is_hidden',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}
