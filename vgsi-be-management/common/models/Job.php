<?php

namespace common\models;

use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use frontend\models\PerformerResponse;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "job".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $performer người xử lý
 * @property string|null $people_involved người liên quan
 * @property int|null $status 0 - mới tạo, 1 - đang làm, 2 - làm xong
 * @property int|null $prioritize 0 - không ưu tiên, 1 - có ưu tiên
 * @property int|null $time_start
 * @property int|null $time_end
 * @property string|null $medias
 * @property int $building_cluster_id
 * @property int $count_expire
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $updated_at_by_user
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property BuildingCluster $buildingCluster
 * @property ManagementUser $createdUser
 */
class Job extends \yii\db\ActiveRecord
{
    const REMIND_WORK = 99;

    const CREATE = 0;
    const UPDATE = 1;
    const UPDATE_STATUS = 2;
    const DELETE = 3;

    const CATEGORY_MY_JOB = 0;
    const CATEGORY_ASSIGNED_ME = 1;
    const CATEGORY_RELATED_ME = 2;


    const STATUS_CANCEL = -1;
    const STATUS_NEW = 0;
    const STATUS_DOING = 1;
    const STATUS_FINISH = 2;
    const STATUS_EXPIRE = 3; // quá hạn

    public static $arrStatus = [
        self::STATUS_CANCEL => "Hủy",
        self::STATUS_NEW => "Mới tạo",
        self::STATUS_DOING => "Đang làm",
        self::STATUS_FINISH => "Hoàn thành",
    ];

    public static $arrStatusEn = [
        self::STATUS_CANCEL => "Cancel",
        self::STATUS_NEW => "New",
        self::STATUS_DOING => "Doing",
        self::STATUS_FINISH => "Done",
    ];

    const PRIORITIZE_DEFAULT = 0;
    const PRIORITIZE = 1;

    public static $arrPrioritize = [
        self::PRIORITIZE_DEFAULT => "Không",
        self::PRIORITIZE => "Có",
    ];

    public static $arrPrioritizeEn = [
        self::PRIORITIZE_DEFAULT => "No",
        self::PRIORITIZE => "Yes",
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'job';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'building_cluster_id'], 'required'],
            [['description', 'medias'], 'string'],
            [['count_expire', 'status', 'prioritize', 'time_start', 'time_end', 'building_cluster_id', 'created_at', 'updated_at', 'created_by', 'updated_by','updated_at_by_user'], 'integer'],
            [['title', 'performer', 'people_involved'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'title' => Yii::t('common', 'Title'),
            'description' => Yii::t('common', 'Description'),
            'performer' => Yii::t('common', 'Performer'),
            'people_involved' => Yii::t('common', 'People Involved'),
            'status' => Yii::t('common', 'Status'),
            'prioritize' => Yii::t('common', 'Prioritize'),
            'time_start' => Yii::t('common', 'Time Start'),
            'time_end' => Yii::t('common', 'Time End'),
            'medias' => Yii::t('common', 'Medias'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'updated_at_by_user' => Yii::t('common', 'Updated At By User'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
        ];
    }

    /**
     * @inheritdoc
     */
    function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'time',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at','updated_at_by_user'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ]
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by','updated_at_by_user'],
                    self::EVENT_BEFORE_UPDATE => ['updated_by'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ],
            ],
        ];
    }

    public function diffDate(){
        $diff = 9999;
        $start = null;
        $end = null;
        if(!empty($this->time_start)){
            $start = date('Y-m-d', $this->time_start);
        }
        if(!empty($this->time_end)){
            if($this->time_end >= time() + 5*365*24*60*60 - 5*24*60*60){
                return $diff;
            }else if($this->time_end >= time() && $this->time_end < time() + 5*365*24*60*60 - 5*24*60*60){
                $end = date('Y-m-d', $this->time_end);
            }else{
                $end = date('Y-m-d', time());
            }
        }
        if(empty($start) || empty($end)){
            return $diff;
        }
        $date1=date_create($start);
        $date2=date_create($end);
        $diff=date_diff($date2,$date1);
        return $diff->format("%R%a");
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if(empty($this->time_end)){
            $this->time_end = time() + 5*365*24*60*60;
        }
        if(empty($this->time_start)){
            $this->time_start = null;
        }
        $this->count_expire = self::diffDate();
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(ManagementUser::className(), ['id' => 'created_by']);
    }

    public function categoryType(){
        $user = Yii::$app->user->getIdentity();
        if($user->id == $this->created_by){
            return self::CATEGORY_MY_JOB;
        }else{
            if(!empty($this->performer)){
                if(in_array($user->id, explode(',', $this->performer))){
                    return self::CATEGORY_ASSIGNED_ME;
                }
            }
            if(!empty($this->people_involved)){
                if(in_array($user->id, explode(',', $this->people_involved))){
                    return self::CATEGORY_RELATED_ME;
                }
            }
        }
        return null;
    }

    public function sendNotifyToPerformer($is_create = Job::CREATE, $arrPerformer,$titleCrontab = "")
    {
        if(empty($arrPerformer)){ return false;}
        try {
            $title = $description = '';
            $title_en = $description_en = '';
            $data = [
                'type' => 'job',
                'job_id' => $this->id,
                'deep_link' => '/main/setting/job'
            ];
            $app_id = $this->buildingCluster->one_signal_app_id;
            if (in_array($is_create, [Job::CREATE, Job::UPDATE])) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_PERFORMER_CREATE_JOB, [trim($this->createdUser->fullName), $this->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_PERFORMER_CREATE_JOB_EN, [trim($this->createdUser->fullName), $this->title]);
            }else if($is_create == Job::UPDATE_STATUS && $this->status == Job::STATUS_CANCEL){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CANCEL_JOB, [trim($this->createdUser->fullName), $this->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CANCEL_JOB_EN, [trim($this->createdUser->fullName), $this->title]);
            }else if($is_create == Job::DELETE){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_DELETE_JOB, [trim($this->createdUser->fullName), $this->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_DELETE_JOB_EN, [trim($this->createdUser->fullName), $this->title]);
            }else if($is_create == Job::REMIND_WORK){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_REMIND_WORK_JOB, [$titleCrontab]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_REMIND_WORK_JOB_EN, [$titleCrontab]);
            }
            if(empty($title)) { return false; }
            array_unique($arrPerformer);
            foreach ($arrPerformer as $id){
                self::createNotify($id, $title, $description, $title_en, $description_en);
            }
            $oneSignalApi = new OneSignalApi();
            $player_ids = [];
            $managementUsers = ManagementUser::find()->where(['id' => $arrPerformer, 'is_send_notify' => ManagementUser::IS_SEND_NOTIFY])->all();
            $managementUserIds = ArrayHelper::map($managementUsers, 'id', 'id');
            $managementUserDeviceTokens = ManagementUserDeviceToken::find()->where(['management_user_id' => $managementUserIds, 'type' => ManagementUserDeviceToken::TYPE_APP])->all();
            foreach ($managementUserDeviceTokens as $managementUserDeviceToken) {
                $player_ids[] = $managementUserDeviceToken->device_token;
            }
            $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, null, $app_id);
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }
    public function sendNotifyToPeopleInvolved($is_create = Job::CREATE, $arrPeopleInvolved)
    {
        if(empty($arrPeopleInvolved)){ return false; }
        try {
            $title = $description = '';
            $title_en = $description_en = '';
            $data = [
                'type' => 'job',
                'job_id' => $this->id,
                'deep_link' => '/main/setting/job'
            ];
            $app_id = $this->buildingCluster->one_signal_app_id;
            if (in_array($is_create, [Job::CREATE, Job::UPDATE])) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_PEOPLE_INVOLVED_CREATE_JOB, [$this->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_PEOPLE_INVOLVED_CREATE_JOB_EN, [$this->title]);
            }else if($is_create == Job::UPDATE_STATUS && $this->status == Job::STATUS_CANCEL){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CANCEL_JOB, [trim($this->createdUser->fullName), $this->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CANCEL_JOB_EN, [trim($this->createdUser->fullName), $this->title]);
            }else if($is_create == Job::DELETE){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_DELETE_JOB, [trim($this->createdUser->fullName), $this->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_DELETE_JOB_EN, [trim($this->createdUser->fullName), $this->title]);
            }else if($is_create == Job::UPDATE_STATUS && $this->status == Job::STATUS_CANCEL){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CANCEL_JOB, [trim($this->createdUser->fullName), $this->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CANCEL_JOB_EN, [trim($this->createdUser->fullName), $this->title]);
            }
            if(empty($title)) { return false; }
            array_unique($arrPeopleInvolved);
            foreach ($arrPeopleInvolved as $id){
                self::createNotify($id, $title, $description, $title_en, $description_en);
            }
            $oneSignalApi = new OneSignalApi();
            //gửi thông báo theo device token
            $player_ids = [];
            $managementUsers = ManagementUser::find()->where(['id' => $arrPeopleInvolved, 'is_send_notify' => ManagementUser::IS_SEND_NOTIFY])->all();
            $managementUserIds = ArrayHelper::map($managementUsers, 'id', 'id');
            $managementUserDeviceTokens = ManagementUserDeviceToken::find()->where(['management_user_id' => $managementUserIds, 'type' => ManagementUserDeviceToken::TYPE_APP])->all();
            foreach ($managementUserDeviceTokens as $managementUserDeviceToken) {
                $player_ids[] = $managementUserDeviceToken->device_token;
            }
            $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, null, $app_id);
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }

    private function createNotify($management_user_id, $title, $description, $title_en, $description_en){
        if(empty($management_user_id)){ return false; }
        $managementUserNotify = new ManagementUserNotify();
        $managementUserNotify->building_cluster_id = $this->building_cluster_id;
        $managementUserNotify->management_user_id  = (int)$management_user_id;
        $managementUserNotify->type = ManagementUserNotify::TYPE_FORM;
        $managementUserNotify->request_id = $this->id;
        $managementUserNotify->title = $title;
        $managementUserNotify->description = $description;
        $managementUserNotify->title_en = $title_en;
        $managementUserNotify->description_en = $description_en;
        if (!$managementUserNotify->save()) {
            Yii::error($managementUserNotify->getErrors());
        }
        unset($managementUserNotify);
    }

    /**
     * @param Job $dataOld
     * @return true
     */
    private function getContentChange($dataOld){
        if(!empty($dataOld->performer) || !empty($this->performer)){
            $performerOld = [];
            if(!empty($dataOld->performer)){
                $performerOld = explode(',', $dataOld->performer);
            }
            $performerNew = [];
            if(!empty($this->performer)){
                $performerNew = explode(',', $this->performer);
            }
            $performerChange = array_diff($performerNew, $performerOld);
            if(!empty($performerChange)){
                $content = 'Đã thêm ';
                $content_en = 'Added ';
                $managementUsers = ManagementUser::find()->where(['id' => $performerChange])->all();
                foreach ($managementUsers as $managementUser){
                    $content .= $managementUser->first_name . ' ' . $managementUser->last_name . ', ';
                    $content_en .= $managementUser->first_name . ' ' . $managementUser->last_name . ', ';
                }
                $content = trim($content, ', ');
                $content_en = trim($content_en, ', ');
                $content .= ' tiếp nhận công việc này';
                $content_en .= ' take this task';
                self::addComment($content, $content_en);
            }
        }

        if(!empty($dataOld->people_involved) || !empty($this->people_involved)){
            $peopleInvolvedOld = [];
            if(!empty($dataOld->people_involved)){
                $peopleInvolvedOld = explode(',', $dataOld->people_involved);
            }
            $peopleInvolvedNew = [];
            if(!empty($this->people_involved)){
                $peopleInvolvedNew = explode(',', $this->people_involved);
            }
            $peopleInvolvedChange = array_diff($peopleInvolvedNew, $peopleInvolvedOld);
            if(!empty($peopleInvolvedChange)){
                $content = 'Đã thêm ';
                $content_en = 'Added ';
                $managementUsers = ManagementUser::find()->where(['id' => $peopleInvolvedChange])->all();
                foreach ($managementUsers as $managementUser){
                    $content .= $managementUser->first_name . ' ' . $managementUser->last_name . ', ';
                    $content_en .= $managementUser->first_name . ' ' . $managementUser->last_name . ', ';
                }
                $content = trim($content, ', ');
                $content_en = trim($content_en, ', ');
                $content .= ' tiếp nhận công việc này';
                $content_en .= ' take this task';
                self::addComment($content, $content_en);
            }
        }

        if(trim($dataOld->title) !== trim($this->title)){
            $content = 'Đã thay đổi tiêu đề công việc thành ' . $this->title;
            $content_en = 'Changed task title ' . $this->title;
            self::addComment($content, $content_en);
        }

        if(trim($dataOld->description) !== trim($this->description)){
            $content = 'Đã thay đổi mô tả công việc thành ' . $this->description;
            $content_en = 'Changed task description ' . $this->description;
            self::addComment($content, $content_en);
        }

        if(date('Ymd', $dataOld->time_start) !== date('Ymd', $this->time_start)){
            $content = 'Đã thay đổi Ngày bắt đầu thành ' . date('d-m-Y', $this->time_start);
            $content_en = 'Changed start date to ' . date('d-m-Y', $this->time_start);
            self::addComment($content, $content_en);
        }

        if(date('Ymd', $dataOld->time_end) !== date('Ymd', $this->time_end)){
            $content = 'Đã thay đổi Ngày kết thúc thành ' . date('d-m-Y', $this->time_end);
            $content_en = 'Changed deadline to ' . date('d-m-Y', $this->time_end);
            self::addComment($content, $content_en);
        }

        if($dataOld->prioritize !== $this->prioritize){
            $content = 'Đã thay đổi độ ưu tiên thành ' . Job::$arrPrioritize[$this->prioritize];
            $content_en = 'Changed the priority to ' . Job::$arrPrioritizeEn[$this->prioritize];
            self::addComment($content, $content_en);
        }

        return true;
    }

    /**
     * @param $is_create
     * @param Job $dataOld
     * @return bool
     */
    public function addCommentAuto($is_create = Job::CREATE, $dataOld = null)
    {
        try {
            $content = '';
            $content_en = '';
            if ($is_create == Job::CREATE) {
                $content = 'Đã thêm mới công việc';
                $content_en = 'Added new task';
            }else if ($is_create == Job::UPDATE) {
                return self::getContentChange($dataOld);
            }else if($is_create == Job::UPDATE_STATUS){
                $status_old_txt = null;
                $status_old_txt_en = null;
                if(isset($dataOld->status)){
                    if(isset(self::$arrStatus[$dataOld->status])){
                        $status_old_txt = self::$arrStatus[$dataOld->status];
                        $status_old_txt_en = self::$arrStatusEn[$dataOld->status];
                    }
                }
                $status_new_txt = null;
                $status_new_txt_en = null;
                if(isset(self::$arrStatus[$this->status])){
                    $status_new_txt = self::$arrStatus[$this->status];
                    $status_new_txt_en = self::$arrStatusEn[$this->status];
                }
                $content = 'Đã chuyển trạng thái công việc từ '.$status_old_txt.' sang '. $status_new_txt;
                $content_en = 'Changed task status from '.$status_old_txt_en.' to '. $status_new_txt_en;
            }else if($is_create == Job::DELETE){
                $content = 'Đã xóa công việc';
                $content_en = 'Deleted the task';
            }
            if(empty($content)) {
                return false;
            }
            self::addComment($content, $content_en);
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }

    private function addComment($content, $content_en){
        $item = new JobComment();
        $item->building_cluster_id = $this->building_cluster_id;
        $item->job_id = $this->id;
        $item->content = $content;
        $item->content_en = $content_en;
        $item->type = JobComment::TYPE_HISTORY;
        if (!$item->save()) {
            Yii::error($item->errors);
            return false;
        }
        return true;
    }
    public function sendNotifyChangeStatus($is_create = Job::CREATE, $arrPerformer = [], $dataOld,$fullName,$statusNew)
    {
        if(empty($arrPerformer)){ return false;}
        try {
            $title = $description = '';
            $title_en = $description_en = '';
            $data = [
                'type' => 'job',
                'job_id' => $dataOld->id,
                'deep_link' => '/main/setting/job'
            ];
            if (in_array($is_create, [Job::CREATE, Job::UPDATE])) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_PERFORMER_CREATE_JOB, [trim($fullName), $dataOld->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_PERFORMER_CREATE_JOB_EN, [trim($fullName), $dataOld->title]);
            }else if($is_create == Job::UPDATE_STATUS && $dataOld->status == Job::STATUS_CANCEL){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CANCEL_JOB, [trim($fullName), $dataOld->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CANCEL_JOB_EN, [trim($fullName), $dataOld->title]);
            }else if($is_create == Job::DELETE){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_DELETE_JOB, [trim($fullName), $dataOld->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_DELETE_JOB_EN, [trim($fullName), $dataOld->title]);
            }else if($is_create == Job::REMIND_WORK){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_REMIND_WORK_JOB, [$dataOld->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_REMIND_WORK_JOB_EN, [$dataOld->title]);
            }else if($is_create == Job::UPDATE_STATUS && $dataOld->status == Job::STATUS_NEW){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_JUST_CHANGED_THE_TASK_STATUS, [trim($fullName), self::$arrStatus[$dataOld->status],self::$arrStatus[$statusNew]]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_JUST_CHANGED_THE_TASK_STATUS_EN, [trim($fullName),self::$arrStatusEn[$dataOld->status],self::$arrStatusEn[$statusNew]]);
            }else if($is_create == Job::UPDATE_STATUS && $dataOld->status == Job::STATUS_DOING){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_JUST_CHANGED_THE_TASK_STATUS, [trim($fullName), self::$arrStatus[$dataOld->status],self::$arrStatus[$statusNew]]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_JUST_CHANGED_THE_TASK_STATUS_EN, [trim($fullName),self::$arrStatusEn[$dataOld->status],self::$arrStatusEn[$statusNew]]);
            }
            if(empty($title)) { return false; }
            $arrPerformer = array_unique($arrPerformer);
            for($i = 0; $i < count($arrPerformer);$i ++)
            {
                    $t[$i] = $arrPerformer[$i];
                $this->createNotifyChangeStatus($arrPerformer[$i], $title, $description, $title_en, $description_en,$dataOld);
            }
            $oneSignalApi = new OneSignalApi();
            $player_ids = [];
            $managementUsers = ManagementUser::find()->where(['id' => $arrPerformer, 'is_send_notify' => ManagementUser::IS_SEND_NOTIFY])->all();

            $managementUserIds = [];
            foreach($managementUsers as $item)
            {
                $managementUserIds[] = $item->id;
            }
            // $managementUserIds = ArrayHelper::map($managementUsers, 'id', 'id');
            $managementUserDeviceTokens = ManagementUserDeviceToken::find()->where(['management_user_id' => $managementUserIds, 'type' => ManagementUserDeviceToken::TYPE_APP])->all();
            foreach ($managementUserDeviceTokens as $managementUserDeviceToken) {
                $player_ids[] = $managementUserDeviceToken->device_token;
            }
            $app_id = $this->buildingCluster->one_signal_app_id ?? "f2e6f808-23bf-47b2-948c-b0e14953707f";
            $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, null, $app_id);
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }
    private function createNotifyChangeStatus($management_user_id, $title, $description, $title_en, $description_en,$dataOld){
        if(empty($management_user_id)){ return false; }
        $managementUserNotify = new ManagementUserNotify();
        $managementUserNotify->building_cluster_id = (int)$dataOld->building_cluster_id ?? 1;
        $managementUserNotify->management_user_id  = (int)$management_user_id;
        $managementUserNotify->type = ManagementUserNotify::TYPE_JOB;
        $managementUserNotify->request_id = (int)$dataOld->id ?? 1;
        $managementUserNotify->title = $title;
        $managementUserNotify->description = $description;
        $managementUserNotify->title_en = $title_en;
        $managementUserNotify->description_en = $description_en;
        if (!$managementUserNotify->save()) {
            Yii::error($managementUserNotify->getErrors());
        }
        unset($managementUserNotify);
    }
}
