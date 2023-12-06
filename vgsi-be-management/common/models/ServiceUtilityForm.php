<?php

namespace common\models;

use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\ApartmentMapResidentUser;

/**
 * This is the model class for table "service_utility_form".
 *
 * @property int $id
 * @property string $title
 * @property int $type 0: đăng ký sân chơi, 2: đăng ký thang máy, 3: ...
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $resident_user_id
 * @property int|null $status 0: khởi tạo, 1: đồng ý, 2: không đồng ý
 * @property string|null $elements Các thuộc tính trong form
 * @property string|null $reason
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * 
 * @property Apartment $apartment
 * @property ApartmentMapResidentUser[] $apartmentMapResidentUsers
 * @property ResidentUser $residentUser
 * @property ManagementUser $managementUserAgree
 */
class ServiceUtilityForm extends \yii\db\ActiveRecord
{
    const CANCEL = -1;
    const CREATE = 0;
    const UPDATE = 1;
    const UPDATE_STATUS = 2;

    const STATUS_CANCEL = -1;
    const STATUS_CREATE = 0;
    const STATUS_AGREE = 1;
    const STATUS_DISAGREE = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_utility_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type', 'building_cluster_id', 'building_area_id', 'apartment_id', 'resident_user_id'], 'required'],
            [['building_cluster_id', 'building_area_id', 'apartment_id', 'resident_user_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['title', 'elements', 'reason'], 'string'],
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
            'type' => Yii::t('common', 'Type'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'status' => Yii::t('common', 'Status'),
            'elements' => Yii::t('common', 'Elements'),
            'reason' => Yii::t('common', 'Reason'),
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
            ]
        ];
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
    public function getApartmentMapResidentUsers()
    {
        return $this->hasMany(ApartmentMapResidentUser::className(), ['apartment_id' => 'apartment_id'])->where(['is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartmentMapResidentUser()
    {
        $resident = ResidentUser::findOne($this->resident_user_id);
        return $this->hasOne(ApartmentMapResidentUser::className(), ['apartment_id' => 'apartment_id'])->where(['resident_user_phone' => $resident->phone]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResidentUser()
    {
        return $this->hasOne(ResidentUser::className(), ['id' => 'resident_user_id']);
    }

    public function getManagementUserAgree()
    {
        return $this->hasOne(ManagementUser::className(), ['id' => 'updated_by']);
    }

    public function sendNotifyToManagementUser($managementUserIgnore = null, $residentUserIgnore = null, $is_create = self::UPDATE)
    {
        try {
            $url = $this->apartment->buildingCluster->domain . '/main/form/detail/' . $this->id . '/info';
            $app_id = $this->apartment->buildingCluster->one_signal_app_id;
            $structure_name = $this->apartment->buildingArea->parent_path . $this->apartment->buildingArea->name . '/' . $this->apartment->name;
            $mapAuthGroupsIds = json_decode($this->apartment->buildingCluster->setting_group_receives_notices_financial, true);
            $idApartmentId      = $this->apartment->id;
            $phoneResidentUser  = $residentUserIgnore->phone;
            $residentUserFirstName = ApartmentMapResidentUser::findOne(['apartment_id' => $idApartmentId , 'resident_user_phone' => $phoneResidentUser, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            $nameUserCreateNotify  = $residentUserFirstName['resident_user_first_name'];
            $titleFormNameEn = $this->convertFormName($this->title);
            if ($is_create == self::CREATE) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_NEW_FORM_TO_MANAGEMENT, [$nameUserCreateNotify, $structure_name, $this->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_NEW_FORM_TO_MANAGEMENT_EN, [$nameUserCreateNotify, $structure_name, $titleFormNameEn]);
                $data = [
                    'type' => 'form',
                    'apartment_id' => $this->apartment->id,
                    'form_id' => $this->id,
                    'deep_link' => '/main/form/apartment'
                ];
            }else if ($is_create == self::CANCEL) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_CANCEL_FORM_TO_MANAGEMENT, [$nameUserCreateNotify, $structure_name, $this->title]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_CANCEL_FORM_TO_MANAGEMENT_EN, [$nameUserCreateNotify, $structure_name, $titleFormNameEn]);
                $data = [
                    'type' => 'form',
                    'apartment_id' => $this->apartment->id,
                    'form_id' => $this->id,
                    'deep_link' => '/main/form/apartment'
                ];
            }
            $managementUsers = ManagementUser::find()->where(['auth_group_id' => $mapAuthGroupsIds, 'building_cluster_id' => $this->apartment->building_cluster_id, 'is_deleted' => ManagementUser::NOT_DELETED])->all();
            foreach ($managementUsers as $managementUser) {

                $managementUserIds[] = $managementUser->id;

                //khởi tạo log cho từng management user
                $managementUserNotify = new ManagementUserNotify();
                $managementUserNotify->building_cluster_id = $this->building_cluster_id;
                $managementUserNotify->building_area_id = $this->building_area_id;
                $managementUserNotify->management_user_id = $managementUser->id;
                $managementUserNotify->type = ManagementUserNotify::TYPE_FORM;
                $managementUserNotify->service_booking_id = $this->id;
                $managementUserNotify->title = $title;
                $managementUserNotify->description = $description;
                $managementUserNotify->title_en = $title_en;
                $managementUserNotify->description_en = $description_en;
                $managementUserNotify->service_utility_form_id = $this->id;
                if (!$managementUserNotify->save()) {
                    Yii::error($managementUserNotify->getErrors());
                }
                //end log
            }
            //gửi thông báo theo device token
            $oneSignalApi = new OneSignalApi();
            $player_ids = [];
            $managementUserDeviceTokens = ManagementUserDeviceToken::find()->where(['management_user_id' => $managementUserIds, 'type' => ManagementUserDeviceToken::TYPE_WEB])->all();
            foreach ($managementUserDeviceTokens as $managementUserDeviceToken) {
                $player_ids[] = $managementUserDeviceToken->device_token;
            }
            $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, $url, $app_id);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }

    /*
    *
    * @var $residentUserIgnore ResidentUser
    *
    */
    public function sendNotifyToResidentUser($managementUserIgnore = null, $residentUserIgnore = null, $is_create = self::UPDATE)
    {
        try {
            $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_CREATE;

            $title = $description = '';
            $title_en = $description_en = '';
            $data = [
                'type' => 'form',
                'action' => 'create',
                'booking_id' => $this->id,
                'apartment_id' => $this->apartment_id,
            ];
            $titleFormNameEn = $this->convertFormName($this->title);
            if ($is_create == self::UPDATE_STATUS) {
                if ($this->status == self::STATUS_AGREE) {
                    $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_APPROVED;
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_FORM_APPROVED, [$this->title]);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_FORM_APPROVED_EN, [$titleFormNameEn]);
                }else if ($this->status == self::STATUS_DISAGREE) {
                    $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_CANCEL;
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_FORM_CANCEL, [$this->title]);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_FORM_CANCEL_EN, [$titleFormNameEn]);
                }
                $data = [
                    'type' => 'form',
                    'action' => 'change_status',
                    'booking_id' => $this->id,
                    'apartment_id' => $this->apartment_id,
                ];
            }
            if(!empty($title)){
                self::oneSignalSend($title, $description, $title_en, $description_en, $data, $ACTION_KEY_CREATE, ResidentUserNotify::TYPE_SERVICE_FORM);
            }
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }
    private function oneSignalSend($title, $description, $title_en, $description_en, $data, $ACTION_KEY_CREATE, $notify_type = ResidentUserNotify::TYPE_SERVICE_BOOKING){
        //gửi thông báo cho resident user liên quan tới yêu cầu này
        $oneSignalApi = new OneSignalApi();
        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $this->residentUser->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
        $residentUserIds = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
            if ($apartmentMapResidentUser->resident_user_is_send_notify == ResidentUser::IS_SEND_NOTIFY) {
                $residentUserIds[] = $apartmentMapResidentUser->resident->id ?? null;
            }

            //kiểm tra xem user này có cấu hình nhận thông báo tạo phí hay không không
//            $checkReceive = $apartmentMapResidentUser->checkNotifyReceiveConfig(ResidentNotifyReceiveConfig::CHANNEL_NOTIFY_APP, ResidentNotifyReceiveConfig::TYPE_BOOKING, [$ACTION_KEY_CREATE => ResidentNotifyReceiveConfig::NOT_RECEIVED]);
//            if(!empty($checkReceive)){
//                continue;
//            }
//
//            $residentUserIds[] = $apartmentMapResidentUser->resident_user_id;

            //khởi tạo log cho từng resident user
            // $residentUserNotify = new ResidentUserNotify();
            // $residentUserNotify->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
            // $residentUserNotify->building_area_id = $apartmentMapResidentUser->building_area_id;
            // $residentUserNotify->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
            // $residentUserNotify->type = $notify_type;
            // $residentUserNotify->service_booking_id = $this->id;
            // $residentUserNotify->title = $title;
            // $residentUserNotify->description = $description;
            // $residentUserNotify->title_en = $title_en;
            // $residentUserNotify->description_en = $description_en;
            // if (!$residentUserNotify->save()) {
            //     Yii::error($residentUserNotify->getErrors());
            // }
            //end log
        }
        //gửi thông báo theo device token
        $player_ids = [];
        $residentUserDeviceTokens = ResidentUserDeviceToken::find()->where(['resident_user_id' => $residentUserIds])->all();
        foreach ($residentUserDeviceTokens as $residentUserDeviceToken) {
            $player_ids[] = $residentUserDeviceToken->device_token;
        }
        $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data);
        //end gửi thông báo theo device token

        //update thông báo chưa đọc cho user resident
        ResidentUserMapRead::updateOrCreate(['is_read' => ResidentUserMapRead::IS_UNREAD], ['building_cluster_id' => $this->building_cluster_id, 'type' => ResidentUserMapRead::TYPE_REQUEST, 'resident_user_id' => $residentUserIds], $residentUserIds);
    }

    public function convertFormName($title = null)
    {
        $titleFormNameEn = "";
        switch($title)
        {
            case "Đăng ký gửi xe": $titleFormNameEn = "Resgister Parking Card"; break;
            case "Đăng ký thẻ cư dân": $titleFormNameEn = "Registration of Resident Card"; break;
            case "Đăng ký thẻ ra vào": $titleFormNameEn = "Registration of Access Card"; break;
            default : $titleFormNameEn = "Register Delivery"; 
            break;
        }
        return $titleFormNameEn ;
    }
}
