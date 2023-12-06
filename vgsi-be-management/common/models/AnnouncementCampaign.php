<?php

namespace common\models;

use backendQltt\models\LogBehavior;
use backendQltt\models\LoggerUser;
use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use common\models\ResidentUser;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUserNotify;
use frontend\models\AnnouncementSendNewResponse;
use frontend\models\AnnouncementSendResponse;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

// use frontend\models\AnnouncementSendNewResponse;
// use frontend\models\AnnouncementSendResponse;
// use Yii;
// use yii\behaviors\BlameableBehavior;
// use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "announcement_campaign".
 *
 * @property int $id
 * @property string $title
 * @property string $title_en
 * @property string $description
 * @property string $content
 * @property string $attach
 * @property int $status  0 - chưa active, 1 - đã active
 * @property int $is_send_push
 * @property int $is_send_email
 * @property int $is_send_sms
 * @property int $send_at
 * @property int $is_send Trạng thái gửi 1 - đã gửi, 0 - chưa gửi
 * @property int $building_cluster_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $announcement_category_id
 * @property int $total_apartment_send
 * @property int $total_apartment_open
 * @property string $cr_minutes
 * @property string $cr_hours
 * @property string $cr_days
 * @property string $cr_months
 * @property string $cr_days_of_week
 * @property int $is_event
 * @property int $send_event_at
 * @property int $is_send_event Trạng thái gửi event 1 - đã gửi, 0 - chưa gửi
 * @property int $type
 * @property string $content_sms
 * @property int $total_email_send
 * @property int $total_email_open
 * @property int $total_sms_send
 * @property int $total_apartment_send_success
 * @property int $total_email_send_success
 * @property int $total_sms_send_success
 * @property int $total_app_send
 * @property int $total_app_open
 * @property int $total_app_success
 * @property string $apartment_ids
 * @property string $apartment_not_send_ids
 * @property string $add_phone_send
 * @property string $add_email_send
 * @property int $is_survey loại thông báo khảo sát: 1
 * @property int $survey_deadline thời hạn khảo sát
 * @property int $type_report kiểu báo cáo 0- tính theo diện tích, 1 - tính theo đầu người
 * @property string $targets loại đối tượng nhận thông báo [0,1,2,..]
 * @property string $resident_user_phones loại đối tượng nhận thông báo [0,1,2,..]
 *
 * @property BuildingCluster $buildingCluster
 * @property AnnouncementCategory $announcementCategory
 * @property ManagementUser $managementUser
 * @property AnnouncementItemSend[] $announcementItemSends
 */
class AnnouncementCampaign extends \yii\db\ActiveRecord
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PUBLIC_AT = 2;

    const TYPE_REPORT_DEFAULT = 0;
    const TYPE_REPORT_RESIDENT = 1;

    const IS_UNSEND = 0;
    const IS_SEND = 1;

    const IS_UNSEND_PUSH = 0;
    const IS_SEND_PUSH = 1;
    const IS_UNSEND_EMAIL = 0;
    const IS_SEND_EMAIL = 1;
    const IS_UNSEND_SMS = 0;
    const IS_SEND_SMS = 1;

    const IS_NOT_EVENT = 0;
    const IS_EVENT = 1;
    const IS_UNSEND_EVENT = 0;
    const IS_SEND_EVENT = 1;

    const TYPE_POST_NEW = -1; // tin tức
    const TYPE_DEFAULT = 0;
    const TYPE_REMINDER_DEBT_1 = 1; // thông báo phí
    const TYPE_REMINDER_DEBT_2 = 2; // nhắc nợ lần 1
    const TYPE_REMINDER_DEBT_3 = 3; // nhắc nợ lần 2
    const TYPE_REMINDER_DEBT_4 = 4; // nhắc nợ lần 3
    const TYPE_REMINDER_DEBT_5 = 5; // thông báo tạm dừng dịch vụ

    public static $typeList = [
        self::TYPE_DEFAULT => 'Thông báo',
        self::TYPE_REMINDER_DEBT_1 => 'Thông báo phí',
        self::TYPE_REMINDER_DEBT_2 => 'Nhắc nợ lần 1',
        self::TYPE_REMINDER_DEBT_3 => 'Nhắc nợ lần 2',
        self::TYPE_REMINDER_DEBT_4 => 'Nhắc nợ lần 3',
        self::TYPE_REMINDER_DEBT_5 => 'Tạm ngừng dịch vụ',
    ];

    public static $statusList = [
        self::STATUS_ACTIVE => 'Công khai',
        self::STATUS_UNACTIVE => 'Nháp',
        self::STATUS_PUBLIC_AT => 'Hẹn giờ',
    ];

    const IS_SURVEY = 1; // thông báo khảo sát

    const TARGET_CH = 0; // chu ho
    const TARGET_TV = 1; // thanh vien
    const TARGET_KH = 2; // khach

    const TARGET_POST_ALL = 0; // tất cả
    const TARGET_POST_CD = 1; // chỉ cư dân
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'announcement_campaign';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'title_en', 'content', 'is_send_push', 'targets'], 'required'],
            [
                [
                    'content_sms',
                    'content',
                    'attach',
                    'cr_minutes',
                    'cr_hours',
                    'cr_days',
                    'cr_months',
                    'cr_days_of_week',
                    'description',
                    'title',
                    'title_en',
                    'apartment_ids',
                    'apartment_not_send_ids',
                    'add_phone_send',
                    'add_email_send',
                    'image',
                ],
                'string'
            ],
            [
                [
                    'total_apartment_send_success',
                    'total_email_send_success',
                    'total_sms_send_success',
                    'total_app_send',
                    'total_app_open',
                    'total_app_success',
                    'type',
                    'total_email_send',
                    'total_email_open',
                    'total_sms_send',
                    // 'send_event_at',
                    'is_event',
                    'is_send_event',
                    'status',
                    'is_send',
                    // 'is_send_push',
                    'is_send_email',
                    'is_send_sms',
                    'send_at',
                    'building_cluster_id',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'announcement_category_id',
                    'total_apartment_send',
                    'total_apartment_open',
                    'is_survey',
                    'survey_deadline',
                    'type_report'
                ],
                'integer'
            ],
            [['targets', 'resident_user_phones', 'is_send_push', 'send_event_at'], 'safe'],
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
            'title_en' => Yii::t('common', 'Title (En)'),
            'description' => Yii::t('common', 'Description'),
            'content' => Yii::t('common', 'Content'),
            'attach' => Yii::t('common', 'Attach'),
            'status' => Yii::t('common', 'Status'),
            'is_send_push' => Yii::t('common', 'Is Send Push'),
            'is_send_email' => Yii::t('common', 'Is Send Email'),
            'is_send_sms' => Yii::t('common', 'Is Send Sms'),
            'send_at' => Yii::t('common', 'Send At'),
            'is_send' => Yii::t('common', 'Is Send'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
            'announcement_category_id' => Yii::t('common', 'Announcement Category ID'),
            'total_apartment_send' => Yii::t('common', 'Total Apartment Send'),
            'total_apartment_open' => Yii::t('common', 'Total Apartment Open'),
            'cr_minutes' => Yii::t('common', 'Cr Minutes'),
            'cr_hours' => Yii::t('common', 'Cr Hours'),
            'cr_days' => Yii::t('common', 'Cr Days'),
            'cr_months' => Yii::t('common', 'Cr Months'),
            'cr_days_of_week' => Yii::t('common', 'Cr Days Of Week'),
            'is_event' => Yii::t('common', 'Is Event'),
            'send_event_at' => Yii::t('common', 'Send Event At'),
            'is_send_event' => Yii::t('common', 'Is Send Event'),
            'type' => Yii::t('common', 'Type'),
            'content_sms' => Yii::t('common', 'Content Sms'),
            'total_email_send' => Yii::t('common', 'Total Email Send'),
            'total_email_open' => Yii::t('common', 'Total Email Open'),
            'total_sms_send' => Yii::t('common', 'Total Sms Send'),
            'total_apartment_send_success' => Yii::t('common', 'Total Apartment Send Success'),
            'total_email_send_success' => Yii::t('common', 'Total Email Send Success'),
            'total_sms_send_success' => Yii::t('common', 'Total Sms Send Success'),
            'total_app_send' => Yii::t('common', 'Total App Send'),
            'total_app_open' => Yii::t('common', 'Total App Open'),
            'total_app_success' => Yii::t('common', 'Total App Success'),
            'apartment_ids' => Yii::t('common', 'Apartment Ids'),
            'apartment_not_send_ids' => Yii::t('common', 'Apartment Not Send Ids'),
            'add_phone_send' => Yii::t('common', 'Add Phone Send'),
            'add_email_send' => Yii::t('common', 'Add Email Send'),
            'type_report' => Yii::t('common', 'Kiểu báo cáo'),
            'image' => Yii::t('common', 'Image'),
            'targets' => Yii::t('common', 'Gửi tới'),
            'resident_user_phones' => Yii::t('common', 'Gửi tới cư dân'),
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
            // 'log' => [
            //     'class' => LogBehavior::class, //lưu lịch sử thao tác người dùng
            // ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnnouncementCategory()
    {
        return $this->hasOne(AnnouncementCategory::className(), ['id' => 'announcement_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagementUser()
    {
        return $this->hasOne(ManagementUser::className(), ['id' => 'created_by']);
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
    public function getAnnouncementItemSends()
    {
        return $this->hasMany(AnnouncementItemSend::className(), ['announcement_campaign_id' => 'id']);
    }

    public static function countTotalSend($building_cluster_id, $building_area_ids)
    {
        $total_apartment = AnnouncementSendResponse::find()
            ->where(['building_cluster_id' => $building_cluster_id, 'is_deleted' => Apartment::NOT_DELETED])
            ->andFilterWhere([
                'building_area_id' => $building_area_ids,
            ])->count();

        $count_email = ResidentUser::find()->select(["COUNT(resident_user.email) as email"])
            ->join('LEFT JOIN', 'apartment', 'resident_user.id = apartment.resident_user_id')
            ->where(['apartment.building_cluster_id' => $building_cluster_id, 'apartment.building_area_id' => $building_area_ids, 'resident_user.is_deleted' => ResidentUser::NOT_DELETED, 'apartment.is_deleted' => Apartment::NOT_DELETED])
            ->andWhere(['not', ['resident_user.email' => null]])
            ->andWhere(['<>', 'resident_user.email', ''])->one();

        $count_app = ResidentUser::find()->select(["COUNT(resident_user.active_app) as active_app"])
            ->join('LEFT JOIN', 'apartment', 'resident_user.id = apartment.resident_user_id')
            ->where(['apartment.building_cluster_id' => $building_cluster_id, 'resident_user.active_app' => ResidentUser::ACTIVE_APP, 'apartment.building_area_id' => $building_area_ids, 'resident_user.is_deleted' => ResidentUser::NOT_DELETED, 'apartment.is_deleted' => Apartment::NOT_DELETED])->one();

        $count_phone = ResidentUser::find()->select(["COUNT(resident_user.phone) as phone"])
            ->join('LEFT JOIN', 'apartment', 'resident_user.id = apartment.resident_user_id')
            ->where(['apartment.building_cluster_id' => $building_cluster_id, 'apartment.building_area_id' => $building_area_ids, 'resident_user.is_deleted' => ResidentUser::NOT_DELETED, 'apartment.is_deleted' => Apartment::NOT_DELETED])
            ->andWhere(['not', ['resident_user.phone' => null]])
            ->andWhere(['<>', 'resident_user.phone', ''])->one();

        return [
            'total_apartment' => (int) $total_apartment,
            'total_email' => (int) $count_email->email,
            'total_app' => (int) $count_app->active_app,
            'total_sms' => (int) $count_phone->phone,
        ];
    }
    public static function countTotalSendNew($building_cluster_id, $building_area_ids, $targets)
    {
        $total_apartment = AnnouncementSendNewResponse::find()
            ->where(['building_cluster_id' => $building_cluster_id, 'is_deleted' => Apartment::NOT_DELETED])
            ->andFilterWhere([
                'building_area_id' => $building_area_ids,
                'type' => $targets
            ])->count();

        $count_email = AnnouncementSendNewResponse::find()
            ->where(['building_cluster_id' => $building_cluster_id, 'is_deleted' => Apartment::NOT_DELETED])
            ->andFilterWhere([
                'building_area_id' => $building_area_ids,
                'type' => $targets,
            ])
            ->andWhere(['not', ['resident_user_email' => null]])
            ->andWhere(['<>', 'resident_user_email', ''])->count();

        $count_app = AnnouncementSendNewResponse::find()
            ->where(['building_cluster_id' => $building_cluster_id, 'is_deleted' => Apartment::NOT_DELETED])
            ->andFilterWhere([
                'building_area_id' => $building_area_ids,
                'type' => $targets,
                'install_app' => ResidentUser::ACTIVE_APP,
            ])
            ->andWhere(['not', ['resident_user_phone' => null]])
            ->andWhere(['<>', 'resident_user_phone', ''])->count();

        $count_phone = AnnouncementSendNewResponse::find()
            ->where(['building_cluster_id' => $building_cluster_id, 'is_deleted' => Apartment::NOT_DELETED])
            ->andFilterWhere([
                'building_area_id' => $building_area_ids,
                'type' => $targets,
            ])
            ->andWhere(['not', ['resident_user_phone' => null]])
            ->andWhere(['<>', 'resident_user_phone', ''])->count();

        return [
            'total_apartment' => (int) $total_apartment,
            'total_email' => (int) $count_email,
            'total_app' => (int) $count_app,
            'total_sms' => (int) $count_phone,
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!$this->send_event_at) {
            $this->send_event_at = time();
        }

        return true;
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('backendQltt', 'Công khai ngay'),
                // self::STATUS_UNACTIVE => Yii::t('backendQltt', 'Nháp'),
            self::STATUS_PUBLIC_AT => Yii::t('backendQltt', 'Công khai vào lúc'),
        ];
    }
    /*
     *
     * @var $residentUserIgnore ResidentUser
     *
     */
    public function sendNotifyToResidentUser($paramTitle = "", $paramTitle_en = "", $paramTargets = [], $announcementItemId = 0)
    {
        try {
            $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_CREATE;
            $title = $description = '';
            $title_en = $description_en = '';
            $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_UPDATE;
            $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::INVESTORS_SEND_NEWS, [$paramTitle]);
            $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::INVESTORS_SEND_NEWS_EN, [$paramTitle_en]);
            $data = [
                'type' => 'news',
                'action' => 'update',
                'booking_id' => $announcementItemId,
                'apartment_id' => 1,
            ];
            self::oneSignalSend($title, $description, $title_en, $description_en, $data, $ACTION_KEY_CREATE, 9, $paramTargets, $announcementItemId);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }

    private function oneSignalSend($title, $description, $title_en, $description_en, $data, $ACTION_KEY_CREATE, $notify_type = ResidentUserNotify::TYPE_SERVICE_BOOKING, $paramTargets, $announcementItemId)
    {
        //gửi thông báo cho resident user liên quan tới yêu cầu này
        $oneSignalApi = new OneSignalApi();
        $residentUsers = ResidentUser::find()->all();
        $residentUserIds = [];
        $isCheckSendToAllResident = false ;
        for($index = 0; $index < count($paramTargets);$index++)
        {
            if("0" == $paramTargets[$index])
            {
                $isCheckSendToAllResident = true ;
            }
        }
        // var_dump($isCheckSendToAllResident);die();
        if ($isCheckSendToAllResident) {
            foreach ($residentUsers as $residentUser) {
                $residentUserIds[] = $residentUser->id ?? null;
                $residentUserNotify = new ResidentUserNotify();
                $residentUserNotify->building_cluster_id = 1;
                // $residentUserNotify->building_area_id = 4;
                $residentUserNotify->announcement_item_id = $announcementItemId;
                $residentUserNotify->apartment_id = -1;// lấy giá trị -1 làm mặc định để lấy về user app
                $residentUserNotify->resident_user_id = $residentUser->id ?? null;
                $residentUserNotify->type = 9;
                // $residentUserNotify->service_booking_id = 4;
                $residentUserNotify->title = $title;
                $residentUserNotify->description = $description;
                $residentUserNotify->title_en = $title_en;
                $residentUserNotify->description_en = $description_en;
                if (!$residentUserNotify->save()) {
                    Yii::error($residentUserNotify->getErrors());
                }
                //end log
            }
        }else
        {
            foreach ($residentUsers as $residentUser) {
                $residentUserPhone = $residentUser->phone;
                $aryArtmentMapResidentUser = ApartmentMapResidentUser::find(['resident_user_phone'=>$residentUserPhone,'install_app'=>ApartmentMapResidentUser::INSTALL_APP,'resident_user_is_send_notify'=>ApartmentMapResidentUser::IS_SEND_NOTIFY ,'is_deleted'=> ApartmentMapResidentUser::NOT_DELETED])->one();
                if(!empty($aryArtmentMapResidentUser))
                {
                    $residentUserIds[] = $residentUser->id ?? null;
                    $residentUserNotify = new ResidentUserNotify();
                    $residentUserNotify->building_cluster_id = 1;
                    // $residentUserNotify->building_area_id = 4;
                    $residentUserNotify->announcement_item_id = $announcementItemId;
                    $residentUserNotify->apartment_id = -1;// lấy giá trị -1 làm mặc định để lấy về user app
                    $residentUserNotify->resident_user_id = $residentUser->id ?? null;
                    $residentUserNotify->type = 9;
                    // $residentUserNotify->service_booking_id = 4;
                    $residentUserNotify->title = $title;
                    $residentUserNotify->description = $description;
                    $residentUserNotify->title_en = $title_en;
                    $residentUserNotify->description_en = $description_en;
                    if (!$residentUserNotify->save()) {
                        Yii::error($residentUserNotify->getErrors());
                    }
                    //end log
                }
            }

        }
        // var_dump($residentUserIds);die();
        //gửi thông báo theo device token
        $player_ids = [];
        $residentUserDeviceTokens = ResidentUserDeviceToken::find()->where(['resident_user_id' => $residentUserIds])->all();
        foreach ($residentUserDeviceTokens as $residentUserDeviceToken) {
            $player_ids[] = $residentUserDeviceToken->device_token;
        }
       //đếm số lượng gửi thông báo
        $countTotalAppSend = count($residentUserIds);
        $countTotalAppSendSuccess = count($player_ids);
        $announcementCampaign = AnnouncementCampaign::findOne([
            'id' => $announcementItemId,
            'type' => AnnouncementCampaign::TYPE_POST_NEW
        ]);
        // $countTotalAppSend = count($player_ids);
        // var_dump($paramTargets[0]);die();
        if ($announcementCampaign !== null) {
            $announcementCampaign->total_app_send = $countTotalAppSend;
            $announcementCampaign->total_app_open = $countTotalAppSendSuccess;
            $announcementCampaign->save(); // Lưu lại thông tin đã cập nhật vào cơ sở dữ liệu
        }
        $player_ids = array_unique($player_ids);
        $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data);
        //end gửi thông báo theo device token

        //update thông báo chưa đọc cho user resident
        ResidentUserMapRead::updateOrCreate(['is_read' => ResidentUserMapRead::IS_UNREAD], ['building_cluster_id' => $this->building_cluster_id, 'type' => ResidentUserMapRead::TYPE_REQUEST, 'resident_user_id' => $residentUserIds], $residentUserIds);
    }

}