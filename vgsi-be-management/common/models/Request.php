<?php

namespace common\models;

use common\helpers\Color;
use common\helpers\ErrorCode;
use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

use common\models\ApartmentMapResidentUser;
use common\models\ManagementUser;

/**
 * This is the model class for table "request".
 *
 * @property int $id
 * @property string $title
 * @property string $title_en
 * @property string $content
 * @property string $attach
 * @property int $request_category_id
 * @property int $resident_user_id Resident user tạo yêu cầu
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $total_answer Tổng số câu trả lời
 * @property int $status Trạng thái xử lý của yêu cầu: -1 - hủy yêu cầu,0- khởi tạo, 1 - đang xử lý, 2 - đã xử lý xong
 * @property int $is_deleted 0 : chưa xóa, 1 : đã xóa
 * @property int $type_close 0: CD close, 1 : bql close
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $rate
 *
 * @property BuildingCluster $buildingCluster
 * @property ResidentUser $residentUser
 * @property Apartment $apartment
 * @property ApartmentMapResidentUser[] $apartmentMapResidentUsers
 * @property RequestCategory $requestCategory
 * @property RequestMapAuthGroup[] $requestMapAuthGroups
 */
class Request extends \yii\db\ActiveRecord
{
    const CREATE = 0;
    const UPDATE = 1;
    const UPDATE_STATUS = 2;
    const RESIDENT_CREATE_COMMENT = 3;
    const MANAGEMENT_CREATE_COMMENT = 4;

    const NOT_DELETED = 0;
    const DELETED = 1;

    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_CANCEL = -2;
    const STATUS_RECEIVED = -1;
    const STATUS_INIT = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETE = 2;
    const STATUS_REOPEN = 3;
    const STATUS_CLOSE = 4;

    const TYPE_CD_CLOSE = 0;
    const TYPE_BQL_CLOSE = 1;

    const ANSWER_NOT_INTERNAL = 0;
    const ANSWER_INTERNAL = 1;

    public static $status_color = [
        self::STATUS_CANCEL => Color::DARK,
        self::STATUS_RECEIVED => Color::SECONDARY,
        self::STATUS_INIT => Color::DANGER,
        self::STATUS_PROCESSING => Color::WARNING,
        self::STATUS_COMPLETE => Color::SUCCESS,
        self::STATUS_REOPEN => Color::WARNING,
        self::STATUS_CLOSE => Color::SUCCESS,
    ];

    public static $status_list = [
        self::STATUS_CANCEL => "Hủy yêu cầu",
        self::STATUS_RECEIVED => "Đã tiếp nhận",
        self::STATUS_INIT => "Phản ánh mới",
        self::STATUS_PROCESSING => "Đang xử lý",
        self::STATUS_COMPLETE => "Đã xử lý",
        self::STATUS_REOPEN => "Xử lý lại",
        self::STATUS_CLOSE => "Đã đóng",
    ];

    public static $status_en_list = [
        self::STATUS_CANCEL => "Cancel",
        self::STATUS_RECEIVED => "Received",
        self::STATUS_INIT => "New",
        self::STATUS_PROCESSING => "Processing",
        self::STATUS_COMPLETE => "Resolved",
        self::STATUS_REOPEN => "Reprocess",
        self::STATUS_CLOSE => "Closed",
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'attach'], 'string'],
            [['type_close', 'rate', 'total_answer', 'status', 'request_category_id', 'resident_user_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['title', 'title_en'], 'string', 'max' => 255],
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
            'title_en' => Yii::t('common', 'Title En'),
            'content' => Yii::t('common', 'Content'),
            'attach' => Yii::t('common', 'Attach'),
            'request_category_id' => Yii::t('common', 'Request Category ID'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'total_answer' => Yii::t('common', 'Total Answer'),
            'status' => Yii::t('common', 'Status'),
            'rate' => Yii::t('common', 'Rate'),
            'type_close' => Yii::t('common', 'Type Close'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
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
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ]
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => ['updated_by'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ],
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
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
    public function getResidentUser()
    {
        return $this->hasOne(ResidentUser::className(), ['id' => 'resident_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestCategory()
    {
        return $this->hasOne(RequestCategory::className(), ['id' => 'request_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestMapAuthGroups()
    {
        return $this->hasMany(RequestMapAuthGroup::className(), ['request_id' => 'id']);
    }

    public function getManagementUsers()
    {
        return $this->hasMany(ManagementUser::className(), ['id' => 'item_id'])
            ->via('order_item', ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartmentMapResidentUsers()
    {
        return $this->hasMany(ApartmentMapResidentUser::className(), ['apartment_id' => 'apartment_id'])->where(['is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
    }

    public function getCode(){
        return  '#'.str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
    /*
     *
     */
    public function sendNotifyToManagementUser($managementUserIgnore = null, $residentUserIgnore = null, $is_create = Request::UPDATE, $is_internal = Request::ANSWER_INTERNAL)
    {
        try {
            // nếu tồn tài config không cho gửi thì sẽ không gửi
//            $notifySendConfig = NotifySendConfig::findOne(['building_cluster_id' => $this->buildingCluster->id, 'type' => NotifySendConfig::TYPE_REQUEST, 'send_notify_app' => NotifySendConfig::NOT_SEND]);
//            if(!empty($notifySendConfig)){
//                return false;
//            }
            $ACTION_KEY_CREATE = ManagementNotifyReceiveConfig::ACTION_KEY_UPDATE;
            $title = $title_en = '';
            $description = $description_en = '';
            $data = [];
            $url = $this->buildingCluster->domain . '/main/ticket/detail/' . $this->id;
            $app_id = $this->buildingCluster->one_signal_app_id;
            $structrure_name = $this->apartment->buildingArea->parent_path . $this->apartment->buildingArea->name . '/' . $this->apartment->name;
            $idApartmentId      = $this->apartment->id;
            $phoneResidentUser  = $this->residentUser->phone;
            $residentUserFirstName = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment->id, 'resident_user_phone' => $this->residentUser->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if ($is_create == Request::CREATE) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_NEW_REQUEST_TO_MANAGEMENT, [$residentUserFirstName->resident_user_first_name, $structrure_name, $this->getCode()]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_NEW_REQUEST_TO_MANAGEMENT_EN, [$residentUserFirstName->resident_user_first_name , $structrure_name, $this->getCode()]);
                $data = [
                    'type' => 'apartment',
                    'apartment_id' => $this->apartment->id,
                    'deep_link' => '/main/setting/apartment'
                ];
            } else if ($is_create == Request::UPDATE_STATUS) {
                if($is_internal == Request::STATUS_CANCEL){
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST_CANCEL, [$residentUserFirstName->resident_user_first_name , $structrure_name, $this->getCode()]);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST_CANCEL_EN, [$residentUserFirstName->resident_user_first_name , $structrure_name, $this->getCode()]);
                }else if($is_internal == Request::STATUS_REOPEN){
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST_REOPENT, [$residentUserFirstName->resident_user_first_name , $structrure_name, $this->getCode()]);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST_REOPENT_EN, [$residentUserFirstName->resident_user_first_name , $structrure_name, $this->getCode()]);
                }
                $data = [
                    'type' => 'request',
                    'action' => 'change_status',
                    'request_id' => $this->id
                ];
            } else {
                $content = $description;
                if(!empty($requestAnswer)){
                    $content = $requestAnswer->content;
                }
                if (!empty($managementUserIgnore)) {
                    $title = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_UPDATE_REQUEST_TO_MANAGEMENT, [$managementUserIgnore->first_name, $this->getCode()]);
                    $title_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_UPDATE_REQUEST_TO_MANAGEMENT_EN, [$managementUserIgnore->first_name, $this->getCode()]);
                    $description = $managementUserIgnore->first_name . '(' . $managementUserIgnore->authGroup->name . '): ' . $content;
                    $data = [
                        'type' => 'request',
                        'action' => 'reply',
                        'request_id' => $this->id
                    ];
                } else if (!empty($residentUserIgnore)) {
                    $apartment_name = $this->apartment->buildingArea->parent_path . $this->apartment->buildingArea->name . '/' . $this->apartment->name;
                    $description = $residentUserIgnore->first_name . '(' . $apartment_name . '): ' . $content;
                    $title = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_UPDATE_REQUEST_TO_MANAGEMENT, [$residentUserIgnore->first_name, $this->getCode()]);
                    $title_en = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_UPDATE_REQUEST_TO_MANAGEMENT_EN, [$residentUserIgnore->first_name, $this->getCode()]);
                    $data = [
                        'type' => 'request',
                        'action' => 'reply',
                        'request_id' => $this->id
                    ];
                }
                if ($is_internal == Request::ANSWER_INTERNAL) {
                    $requestAnswer = RequestAnswerInternal::find()->where(['request_id' => $this->id])->orderBy(['id' => SORT_DESC])->one();
                }else if($is_internal == Request::ANSWER_NOT_INTERNAL){
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST_COMMENT, [$residentUserIgnore->first_name, $structrure_name, $this->getCode()]);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST_COMMENT_EN, [$residentUserIgnore->first_name, $structrure_name, $this->getCode()]);
                    $requestAnswer = RequestAnswer::find()->where(['request_id' => $this->id])->orderBy(['id' => SORT_DESC])->one();
                } else {
                    $requestAnswer = RequestAnswer::find()->where(['request_id' => $this->id])->orderBy(['id' => SORT_DESC])->one();
                }
            }
            //gửi thông báo cho các user thuộc nhóm quyền này biết có yêu cầu được cập nhật
            $oneSignalApi = new OneSignalApi();
            $requestMapAuthGroups = $this->requestMapAuthGroups;
            $managementUserIds = [];

            $typeNotify = ResidentUserNotify::TYPE_REQUEST;
            $request_answer_id = null;
            $request_answer_internal_id = null;
            if ($is_create != Request::CREATE) {
                $typeNotify = ResidentUserNotify::TYPE_REQUEST_ANSWER;
                if ($is_internal == Request::ANSWER_INTERNAL) {
                    $typeNotify = ResidentUserNotify::TYPE_REQUEST_ANSWER_INTERNAL;
                    if (!empty($requestAnswer)) {
                        $request_answer_internal_id = $requestAnswer->id;
                    }
                } else {
                    if (!empty($requestAnswer)) {
                        $request_answer_id = $requestAnswer->id;
                    }
                }
            }            
            foreach ($requestMapAuthGroups as $requestMapAuthGroup) {
                $managementUsers = $requestMapAuthGroup->managementUsers;
                foreach ($managementUsers as $managementUser) {
                    if (!empty($managementUserIgnore)) {
                        if ($managementUser->id == $managementUserIgnore->id) {
                            continue;
                        }
                    }

                    //kiểm tra xem user này có cấu hình nhận thông báo tạo phí hay không không
//                    $checkReceive = $managementUser->checkNotifyReceiveConfig(ManagementNotifyReceiveConfig::CHANNEL_NOTIFY_APP, ManagementNotifyReceiveConfig::TYPE_REQUEST, [$ACTION_KEY_CREATE => ManagementNotifyReceiveConfig::NOT_RECEIVED]);
//                    if(!empty($checkReceive)){
//                        continue;
//                    }

                    $managementUserIds[] = $managementUser->id;

                    //khởi tạo log cho từng management user
                    $managementUserNotify = new ManagementUserNotify();
                    $managementUserNotify->building_cluster_id = $this->building_cluster_id;
                    $managementUserNotify->building_area_id = $this->building_area_id;
                    $managementUserNotify->management_user_id = $managementUser->id;
                    $managementUserNotify->type = $typeNotify;
                    $managementUserNotify->request_id = $this->id;
                    $managementUserNotify->request_answer_id = $request_answer_id;
                    $managementUserNotify->request_answer_internal_id = $request_answer_internal_id;
                    $managementUserNotify->title = $title;
                    $managementUserNotify->description = $description;
                    $managementUserNotify->title_en = $title_en;
                    $managementUserNotify->description_en = $description_en;
                    if (!$managementUserNotify->save()) {
                        Yii::error($managementUserNotify->getErrors());
                    }
                    //end log
                }
            }
            //gửi thông báo theo device token
            $player_ids = [];
            $managementUserDeviceTokens = ManagementUserDeviceToken::find()->where(['management_user_id' => $managementUserIds, 'type' => ManagementUserDeviceToken::TYPE_WEB])->all();
            foreach ($managementUserDeviceTokens as $managementUserDeviceToken) {
                $player_ids[] = $managementUserDeviceToken->device_token;
            }
            $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, $url, $app_id);
            //end gửi thông báo theo device token
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }

    /*
     *
     * @var $residentUserIgnore ResidentUser
     *
     */
    public function sendNotifyToResidentUser($managementUserIgnore = null, $residentUserIgnore = null, $is_create = Request::UPDATE)
    {
//        try {
            // nếu tồn tài config không cho gửi thì sẽ không gửi
//            $notifySendConfig = NotifySendConfig::findOne(['building_cluster_id' => $this->buildingCluster->id, 'type' => NotifySendConfig::TYPE_REQUEST, 'send_notify_app' => NotifySendConfig::NOT_SEND]);
//            if(!empty($notifySendConfig)){
//                return false;
//            }

            $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CREATE_REQUEST_COMMENT, [$this->getCode()]);
            $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CREATE_REQUEST_COMMENT_EN, [$this->getCode()]);
            $data = [
                'type' => 'request',
                'action' => 'reply',
                'request_id' => $this->id,
                'apartment_id' => $this->apartment_id,
            ];
            if ($is_create == Request::MANAGEMENT_CREATE_COMMENT) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CREATE_REQUEST_COMMENT, [$this->getCode()]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CREATE_REQUEST_COMMENT_EN, [$this->getCode()]);
            }else if ($is_create == Request::RESIDENT_CREATE_COMMENT) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::RESIDENT_CREATE_REQUEST_COMMENT, [$residentUserIgnore->getFullName($this->apartment_id), $this->getCode()]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::RESIDENT_CREATE_REQUEST_COMMENT_EN, [$residentUserIgnore->getFullName($this->apartment_id), $this->getCode()]);
            }else if ($is_create == Request::UPDATE) {
                $title = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_UPDATE_REQUEST_TO_APARTMENT, ['Yêu cầu', $this->title]);
                if (!empty($residentUserIgnore)) {
                    $requestAnswer = RequestAnswer::find()->where(['request_id' => $this->id, 'management_user_id' => null])->orderBy(['id' => SORT_DESC])->one();
                    $description = $residentUserIgnore->first_name . ': ' . $requestAnswer->content;
                } else if (!empty($managementUserIgnore)) {
                    $requestAnswer = RequestAnswer::find()->where(['request_id' => $this->id, 'resident_user_id' => null])->orderBy(['id' => SORT_DESC])->one();
                    $description = 'BQL: ' . $requestAnswer->content;
                }
                $data = [
                    'type' => 'request',
                    'action' => 'reply',
                    'request_id' => $this->id,
                    'apartment_id' => $this->apartment_id,
                ];
            } else if ($is_create == Request::UPDATE_STATUS) {
                if(in_array($this->status, [Request::STATUS_CLOSE, Request::STATUS_COMPLETE])){
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_CLOSE_REQUEST, [$this->getCode(), self::$status_list[$this->status] ?? '']);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_CLOSE_REQUEST_EN, [$this->getCode(), self::$status_en_list[$this->status] ?? '']);
                }else{
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST, [$this->getCode(), self::$status_list[$this->status] ?? '']);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST_EN, [$this->getCode(), self::$status_en_list[$this->status] ?? '']);
                }
                $data = [
                    'type' => 'request',
                    'action' => 'change_status',
                    'request_id' => $this->id,
                    'apartment_id' => $this->apartment_id,
                ];
            }
            // Gửi thông báo tới ban quản lý
            // change massage send to BQL
            // $structure_name = $this->apartment->buildingArea->parent_path . $this->apartment->buildingArea->name . '/' . $this->apartment->name;
            // if ($is_create == Request::RESIDENT_CREATE_COMMENT) 
            // {
            //     $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST_COMMENT, [$residentUserIgnore->getFullName($this->apartment_id),$structure_name, $this->getCode()]);
            //     $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_REQUEST_COMMENT_EN, [$residentUserIgnore->getFullName($this->apartment_id),$structure_name, $this->getCode()]);
            // }
            // $mapAuthGroupsIds = json_decode($this->buildingCluster->setting_group_receives_notices_financial, true);
            // $managementUsers = ManagementUser::find()->where(['auth_group_id' => $mapAuthGroupsIds, 'building_cluster_id' =>  $this->buildingCluster->id, 'is_deleted' => ManagementUser::NOT_DELETED])->all();
            // foreach ($managementUsers as $managementUser) {

            //     $managementUserIds[] = $managementUser->id;

            //     //khởi tạo log cho từng management user
            //     $managementUserNotify = new ManagementUserNotify();
            //     $managementUserNotify->building_cluster_id = $this->building_cluster_id;
            //     $managementUserNotify->building_area_id = $this->building_area_id;
            //     $managementUserNotify->management_user_id = $managementUser->id;
            //     $managementUserNotify->type = ManagementUserNotify::TYPE_FORM;
            //     $managementUserNotify->service_booking_id = $this->id;
            //     $managementUserNotify->title = $title;
            //     $managementUserNotify->description = $description;
            //     $managementUserNotify->title_en = $title_en;
            //     $managementUserNotify->description_en = $description_en;
            //     if (!$managementUserNotify->save()) {
            //         Yii::error($managementUserNotify->getErrors());
            //     }
            //     //end log
            // }
            // //gửi thông báo theo device token
            // $url = $this->apartment->buildingCluster->domain . '/main/form/detail/' . $this->id . '/info';
            // $app_id = $this->apartment->buildingCluster->one_signal_app_id;
            // $oneSignalApi = new OneSignalApi();
            // $player_ids = [];
            // $managementUserDeviceTokens = ManagementUserDeviceToken::find()->where(['management_user_id' => $managementUserIds, 'type' => ManagementUserDeviceToken::TYPE_WEB])->all();
            // foreach ($managementUserDeviceTokens as $managementUserDeviceToken) {
            //     $player_ids[] = $managementUserDeviceToken->device_token;
            // }
            // $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, $url, $app_id);


            //gửi thông báo cho resident user liên quan tới yêu cầu này
            $oneSignalApi = new OneSignalApi();
            $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $this->residentUser->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
            $residentUserIds = [];
            foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
                if (!empty($residentUserIgnore)) {
                    if ($apartmentMapResidentUser->resident_user_phone == $residentUserIgnore->phone) {
                        continue;
                    }
                }
                if($apartmentMapResidentUser->resident_user_is_send_notify == ResidentUser::IS_SEND_NOTIFY){
                    $residentUserIds[] = $apartmentMapResidentUser->resident->id ?? null;
                }

                //kiểm tra xem user này có cấu hình nhận thông báo tạo phí hay không không
//                $checkReceive = $apartmentMapResidentUser->checkNotifyReceiveConfig(ResidentNotifyReceiveConfig::CHANNEL_NOTIFY_APP, ResidentNotifyReceiveConfig::TYPE_REQUEST, [ResidentNotifyReceiveConfig::ACTION_KEY_UPDATE => ResidentNotifyReceiveConfig::NOT_RECEIVED]);
//                if(!empty($checkReceive)){
//                    continue;
//                }
//
//                $residentUserIds[] = $apartmentMapResidentUser->resident_user_id;

                //khởi tạo log cho từng resident user
                $residentUserNotify = new ResidentUserNotify();
                $residentUserNotify->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
                $residentUserNotify->building_area_id = $apartmentMapResidentUser->building_area_id;
                $residentUserNotify->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
                $residentUserNotify->type = ($is_create == Request::CREATE) ? ResidentUserNotify::TYPE_REQUEST : ResidentUserNotify::TYPE_REQUEST_ANSWER;
                $residentUserNotify->request_id = $this->id;
                $residentUserNotify->request_answer_id = (!empty($requestAnswer)) ? $requestAnswer->id : null;
                $residentUserNotify->title = $title;
                $residentUserNotify->description = $description;
                $residentUserNotify->title_en = $title_en;
                $residentUserNotify->description_en = $description_en;
                if (!$residentUserNotify->save()) {
                    Yii::error($residentUserNotify->getErrors());
                }
                //end log
            }
            $player_ids = [];
            $residentUserDeviceTokens = ResidentUserDeviceToken::find()->where(['resident_user_id' => $residentUserIds])->all();
            foreach ($residentUserDeviceTokens as $residentUserDeviceToken) {
                $player_ids[] = $residentUserDeviceToken->device_token;
            }
            $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data);
            //end gửi thông báo theo device token

            //update thông báo chưa đọc cho user resident
            ResidentUserMapRead::updateOrCreate(['is_read' => ResidentUserMapRead::IS_UNREAD], ['building_cluster_id' => $this->building_cluster_id, 'type' => ResidentUserMapRead::TYPE_REQUEST, 'resident_user_id' => $residentUserIds], $residentUserIds);

//        } catch (\Exception $ex) {
//            Yii::error($ex->getMessage());
//        }
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert == true) {
            $requestCategory = $this->requestCategory;
            if (!empty($requestCategory->auth_group_ids)) {
                //update map auth_group
                $authGroupIds = Json::decode($requestCategory->auth_group_ids, true);
                foreach ($authGroupIds as $authGroupId) {
                    $requestMapAuthGroup = new RequestMapAuthGroup();
                    $requestMapAuthGroup->auth_group_id = $authGroupId;
                    $requestMapAuthGroup->request_id = $this->id;
                    $requestMapAuthGroup->save();
                }
            }
        }
    }

    public function getTimeProcess()
    {
        if (self::STATUS_INIT == $this->status || self::STATUS_PROCESSING == $this->status) {
            return time() - $this->created_at;
        } else {
            return time() - $this->updated_at;
        }
    }


    

}
