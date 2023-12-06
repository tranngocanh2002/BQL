<?php

namespace common\models;

use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use common\helpers\StringUtils;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\ManagementUserNotify;
use common\models\ApartmentMapResidentUser;

/**
 * This is the model class for table "payment_gen_code".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $apartment_id
 * @property string $service_payment_fee_ids
 * @property int $status
 * @property string $code
 * @property string $description
 * @property string $image
 * @property string $reason
 * @property int $type
 * @property int $is_auto
 * @property int $payment_order_id
 * @property int $lock_time
 * @property int $resident_user_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Apartment $apartment
 * @property BuildingCluster $buildingCluster
 * @property ResidentUser $residentUser
 * @property PaymentGenCodeItem[] $paymentGenCodeItems
 */
class PaymentGenCode extends \yii\db\ActiveRecord
{
    const CREATE = 0;
    const UPDATE = 1;
    const DELETED = -1;
    const REJECT = 2;

    const PAY_OFFLINE = 0;
    const PAY_ONLINE = 1;

    const IS_NOT_AUTO = 0;
    const IS_AUTO = 1;

    const STATUS_CANCEL = -1; // cư dân hủy yêu cầu
    const STATUS_UNPAID = 0; // chờ xác nhận
    const STATUS_PAID = 1; // hoàn thành
    const STATUS_REJECT = 2; // bị từ chối

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_gen_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'apartment_id', 'code', 'type'], 'required'],
            [['resident_user_id', 'building_cluster_id', 'apartment_id', 'type', 'status', 'is_auto', 'payment_order_id', 'lock_time', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['image', 'service_payment_fee_ids', 'code'], 'string', 'max' => 255],
            [['description', 'reason'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'service_payment_fee_ids' => Yii::t('common', 'Service Payment Fee Ids'),
            'status' => Yii::t('common', 'Status'),
            'code' => Yii::t('common', 'Code'),
            'type' => Yii::t('common', 'Type'),
            'is_auto' => Yii::t('common', 'Payment Order Id'),
            'payment_order_id' => Yii::t('common', 'Is Auto'),
            'lock_time' => Yii::t('common', 'Lock Time'),
            'resident_user_id' => Yii::t('common', 'Resident User Id'),
            'description' => Yii::t('common', 'Description'),
            'image' => Yii::t('common', 'Image'),
            'reason' => Yii::t('common', 'Reason'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
        ];
    }

    /**
     * @inheritdoc
     */
    function behaviors() {
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

    private function generateCodeNew($code, $length, $building_cluster_id = null)
    {
        if (empty($building_cluster_id)) {
            return null;
        }
        $res = self::findOne(['code' => $code, 'building_cluster_id' => $building_cluster_id]);
        if (!empty($res)) {
            $code_new = StringUtils::randomStr($length);
            return self::generateCodeNew($code_new, $length, $building_cluster_id);
        }
        return $code;
    }

    /**
     * Generates new code
     */
    public function generateCode()
    {
        $this->code = strtoupper(self::generateCodeNew(StringUtils::randomStr(8), 8, $this->building_cluster_id));
    }

    /**
     * Generates new code
     */
    public function generateCodeVnpay($type = 0,$payment_code = "")
    {
        if(!empty($payment_code) && 4 == $type)
        {
            $this->code = strtoupper(self::generateCodeNew($payment_code, 8, $this->building_cluster_id));
            return;
        }
        $this->code = strtoupper(self::generateCodeNew(StringUtils::randomStr(8), 8, $this->building_cluster_id));
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
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
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
    public function getPaymentGenCodeItems()
    {
        return $this->hasMany(PaymentGenCodeItem::className(), ['payment_gen_code_id' => 'id']);
    }

    /*
     * Gửi thông báo khi cư dân tạo yêu cầu thanh toán
     * Tài khoản bql thuộc nhóm xử lý tài chinh mới nhận được thông báo
     */
    public function sendNotifyToManagementUser($user = null,$paymentGenCodeCode = null,$type = null)
    {
        $mapAuthGroupsIds = [];
        if(!empty($this->buildingCluster) && !empty($this->buildingCluster->setting_group_receives_notices_financial)){
            $mapAuthGroupsIds = json_decode($this->buildingCluster->setting_group_receives_notices_financial, true);
        }
        // sen notify to BQL
        $structure_name = $this->apartment->buildingArea->parent_path . $this->apartment->buildingArea->name . '/' . $this->apartment->name;
        $mapAuthGroupsIds = json_decode($this->apartment->buildingCluster->setting_group_receives_notices_financial, true);
        $idApartmentId      = $this->apartment->id;
        $phoneResidentUser  = $user->phone;
        $residentUserFirstName      = ApartmentMapResidentUser::findOne(['apartment_id' => $idApartmentId , 'resident_user_phone' => $phoneResidentUser, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        $nameUserCreateNotify       = $residentUserFirstName['resident_user_first_name'];
        $title = $description       = NotificationTemplate::vsprintf(NotificationTemplate::SERVICE_PAYMENT_GEN_CODE, [$residentUserFirstName->resident_user_first_name , $structure_name ]);
        $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::SERVICE_PAYMENT_GEN_CODE_EN, [$residentUserFirstName->resident_user_first_name , $structure_name ]);

        if(4 == $type)
        {
            $title = $description       = NotificationTemplate::vsprintf(NotificationTemplate::VNPAY_PAYMENT, [$residentUserFirstName->resident_user_first_name , $structure_name ,$this->apartment->name]);
            $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::VNPAY_PAYMENT_EN, [$residentUserFirstName->resident_user_first_name , $structure_name ,$this->apartment->name]);
        }

        $managementUsers = ManagementUser::find()->where(['auth_group_id' => $mapAuthGroupsIds, 'is_deleted' => ManagementUser::NOT_DELETED, 'status' => ManagementUser::STATUS_ACTIVE])->all();
        foreach ($managementUsers as $managementUser) {

            $managementUserIds[] = $managementUser->id;

            //khởi tạo log cho từng management user
            $managementUserNotify = new ManagementUserNotify();
            $managementUserNotify->building_cluster_id  = $this->building_cluster_id ?? "building_cluster_id";
            $managementUserNotify->building_area_id     = $this->apartment->building_area_id ?? "building_area_id";
            $managementUserNotify->management_user_id   = $managementUser->id ?? "management_user_id";
            $managementUserNotify->type                 = ManagementUserNotify::TYPE_FORM ?? "type";
            if(4 == $type){
                $managementUserNotify->service_bill_id  = $this->jjh ?? "";
            }
            $managementUserNotify->title                = $title ?? "title";
            $managementUserNotify->description          = $description ?? "description";
            $managementUserNotify->title_en             = $title_en ?? "title_en";
            $managementUserNotify->description_en       = $description_en ?? "description_en";
            $managementUserNotify->code                 = $paymentGenCodeCode ?? ""; // map với code khi tạo phí thanh toán
            if (!$managementUserNotify->save()) {
                Yii::error($managementUserNotify->getErrors());
            }
            //end log
        }
        $data = [
            'type' => 'form',
            'apartment_id' => $this->apartment->id,
            'form_id' => $this->id,
            'deep_link' => '/main/form/apartment'
        ];
        $url = $this->apartment->buildingCluster->domain . '/main/form/detail/' . $this->id . '/info';
        $app_id = $this->apartment->buildingCluster->one_signal_app_id;
        //gửi thông báo theo device token
        $oneSignalApi = new OneSignalApi();
        $player_ids = [];
        $managementUserDeviceTokens = ManagementUserDeviceToken::find()->where(['management_user_id' => $managementUserIds, 'type' => ManagementUserDeviceToken::TYPE_WEB])->all();
        foreach ($managementUserDeviceTokens as $managementUserDeviceToken) {
            $player_ids[] = $managementUserDeviceToken->device_token;
        }
        $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, $url, $app_id);
        foreach ($managementUsers as $managementUser) {
            if(!empty($managementUser->buildingCluster)){
                $managementUser->sendNotifyPaymentGenCode($this->residentUser);
            }
        }
    }

    /*
     * Gửi thông báo khi bql từ trối yc thanh toán
     */
    public function sendNotifyToResidentUser($is_create = self::UPDATE, $apartmentId = null)
    {
        try {
            $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_CREATE;
            $title = $description = '';
            $title_en = $description_en = '';
            $data = [
                'type' => 'genCode',
                'action' => 'create',
                'booking_id' => $this->id,
                'apartment_id' => $this->apartment_id,
            ];
            if($is_create == self::REJECT){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_GEN_CODE_REJECT, []);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_GEN_CODE_REJECT_EN, []);
                $data = [
                    'type' => 'genCode',
                    'action' => 'reject',
                    'booking_id' => $this->id,
                    'apartment_id' => $this->apartment_id,
                ];
            }
            if(!empty($title)){
                self::oneSignalSend($title, $description, $title_en, $description_en, $data, $ACTION_KEY_CREATE, ResidentUserNotify::TYPE_SERVICE_GEN_CODE,$apartmentId);
            }
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }

    private function oneSignalSend($title, $description, $title_en, $description_en, $data, $ACTION_KEY_CREATE, $notify_type = ResidentUserNotify::TYPE_SERVICE_BOOKING,$apartmentId = null){
        //gửi thông báo cho resident user liên quan tới yêu cầu này
        $oneSignalApi = new OneSignalApi();
        // $apartmentMapResidentUsers = $this->apartmentMapResidentUsers;
        $apartmentId = $this->apartment_id ?? Yii::$app->getRequest()->getBodyParams('apartment_id'); 
        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['apartment_id' => $apartmentId, 'resident_user_phone' => $this->residentUser->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
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
            $residentUserNotify = new ResidentUserNotify();
            $residentUserNotify->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
            $residentUserNotify->building_area_id = $apartmentMapResidentUser->building_area_id;
            $residentUserNotify->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
            $residentUserNotify->type = $notify_type;
            $residentUserNotify->apartment_id = $apartmentId;
            // $residentUserNotify->service_booking_id = $this->id ?? 4;
            $residentUserNotify->title = $title;
            $residentUserNotify->description = $description;
            $residentUserNotify->title_en = $title_en;
            $residentUserNotify->description_en = $description_en;
            if (!$residentUserNotify->save()) {
                Yii::error($residentUserNotify->getErrors());
            }
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
}
