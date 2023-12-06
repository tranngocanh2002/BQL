<?php

namespace common\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use resident\models\ResidentNotifyReceiveConfigUpdateForm;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;
use yii\web\HttpException;

/**
 * This is the model class for table "service_utility_booking".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $service_utility_config_id
 * @property int $service_utility_free_id
 * @property int $status -1: hủy yêu cầu, 0: khởi tạo, 1: xác nhận yêu cầu
 * @property int $start_time
 * @property int $end_time
 * @property string $book_time
 * @property int $total_adult Số lượng người lớn
 * @property int $total_child Số lượng trẻ em
 * @property int $total_slot Tổng chỗ đặt
 * @property int $price
 * @property int $fee_of_month
 * @property int $service_payment_fee_id
 * @property int $service_map_management_id
 * @property int $is_created_fee
 * @property int $is_paid
 * @property string $service_payment_fee_incurred_ids
 * @property string $service_payment_fee_deposit_ids
 * @property string $service_payment_fee_ids_text_search
 * @property int $total_deposit_money
 * @property int $total_incurred_money
 * @property string $description
 * @property string $json_desc
 * @property string $code
 * @property string $reason
 * @property int $is_send_notify
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property BuildingCluster $buildingCluster
 * @property Apartment $apartment
 * @property ServiceUtilityConfig $serviceUtilityConfig
 * @property ServiceUtilityFree $serviceUtilityFree
 * @property ServicePaymentFee $servicePaymentFee
 * @property ResidentUser $residentUser
 * @property ServiceMapManagement $serviceMapManagement
 * @property ApartmentMapResidentUser[] $apartmentMapResidentUsers
 */
class ServiceUtilityBooking extends \yii\db\ActiveRecord
{
    const CREATE = 0;
    const UPDATE = 1;
    const UPDATE_STATUS = 2;
    const SEND_NOTIFY = 3;

    const IS_UNCREATED_FEE = 0;
    const IS_CREATED_FEE = 1;

    const IS_NOT_SEND_NOTIFY = 0;
    const IS_SEND_NOTIFY = 1;

    const STATUS_CANCEL_SYSTEM = -3;
    const STATUS_CANCEL_BQL = -2;
    const STATUS_CANCEL = -1;
    const STATUS_CREATE = 0;
    const STATUS_ACTIVE = 1;
    const CHANGE_PHONE = 11;
    public static $status_list = [
        self::STATUS_CANCEL_SYSTEM => "Hệ thống hủy",
        self::STATUS_CANCEL_BQL => "BQL hủy",
        self::STATUS_CANCEL => "Cư dân hủy",
        self::STATUS_CREATE => "Chờ duyệt",
        self::STATUS_ACTIVE => "Đặt thành công",
    ];
    const REASON_CANCEL_SYSTEM = "Tiện ích đã hết chỗ trong khoảng thời gian này" ; 

    const IS_UNPAID = 0;
    const IS_PAID = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_utility_booking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'apartment_id', 'service_utility_config_id', 'service_utility_free_id', 'service_map_management_id'], 'required'],
            [['is_send_notify', 'is_paid', 'total_deposit_money', 'total_incurred_money', 'service_map_management_id', 'is_created_fee', 'building_cluster_id', 'building_area_id', 'apartment_id', 'service_utility_config_id', 'service_utility_free_id', 'status', 'start_time', 'end_time', 'total_adult', 'total_child', 'total_slot', 'price', 'fee_of_month', 'service_payment_fee_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description', 'json_desc', 'book_time', 'code', 'service_payment_fee_ids_text_search', 'reason'], 'string'],
            [['service_payment_fee_incurred_ids', 'service_payment_fee_deposit_ids'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'code' => Yii::t('common', 'Code'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'service_utility_config_id' => Yii::t('common', 'Service Utility Config ID'),
            'service_utility_free_id' => Yii::t('common', 'Service Utility Free ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'is_created_fee' => Yii::t('common', 'Is Created Fee'),
            'status' => Yii::t('common', 'Status'),
            'start_time' => Yii::t('common', 'Start Time'),
            'end_time' => Yii::t('common', 'End Time'),
            'book_time' => Yii::t('common', 'Book Time'),
            'total_adult' => Yii::t('common', 'Total Adult'),
            'total_child' => Yii::t('common', 'Total Child'),
            'total_slot' => Yii::t('common', 'Total Slot'),
            'price' => Yii::t('common', 'Price'),
            'fee_of_month' => Yii::t('common', 'Fee Of Month'),
            'service_payment_fee_id' => Yii::t('common', 'Service Payment Fee ID'),
            'description' => Yii::t('common', 'Description'),
            'json_desc' => Yii::t('common', 'Json Desc'),
            'is_paid' => Yii::t('common', 'Is Paid'),
            'reason' => Yii::t('common', 'Reason'),
            'is_send_notify' => Yii::t('common', 'Is Send Notify'),
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

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $system_code = "";
                while (empty($system_code)) {
                    $system_code = "BK" . CUtils::generateRandomString(2) . CUtils::generateRandomNumber(4);
                    if (ServiceUtilityBooking::findOne(['code' => $system_code, 'building_cluster_id' => $this->building_cluster_id])) {
                        $system_code = "";
                    }
                }
                $this->code = $system_code;
            }
            $fee_ids_text_search = null;
            $fee_ids = [];
            if(!empty($this->service_payment_fee_id)){
                $fee_ids[] = $this->service_payment_fee_id;
            }
            if(!empty($this->service_payment_fee_deposit_ids)){
                $fee_ids = array_merge($fee_ids, Json::decode($this->service_payment_fee_deposit_ids, true));
            }
            if(!empty($this->service_payment_fee_incurred_ids)){
                $fee_ids = array_merge($fee_ids, Json::decode($this->service_payment_fee_incurred_ids, true));
            }
            if(!empty($fee_ids)){
                $fee_ids_text_search = implode(',', $fee_ids);
            }
            if(!empty($fee_ids_text_search)){
                $fee_ids_text_search = ','.trim($fee_ids_text_search, ',').',';
            }
            $this->service_payment_fee_ids_text_search = $fee_ids_text_search;
            return true;
        } else {
            return false;
        }
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
        return $this->hasOne(ResidentUser::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceUtilityConfig()
    {
        return $this->hasOne(ServiceUtilityConfig::className(), ['id' => 'service_utility_config_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceUtilityFree()
    {
        return $this->hasOne(ServiceUtilityFree::className(), ['id' => 'service_utility_free_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicePaymentFee()
    {
        return $this->hasOne(ServicePaymentFee::className(), ['id' => 'service_payment_fee_id']);
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
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }

    public function setStartEndTime()
    {
        if (!empty($this->book_time)) {
            $book_time = json_decode($this->book_time, true);
            $this->start_time = -1;
            $this->end_time = -1;
            foreach ($book_time as $item) {
                if ($this->start_time == -1) {
                    $this->start_time = $item['start_time'];
                }
                if ($this->end_time == -1) {
                    $this->end_time = $item['end_time'];
                }
                if ($item['start_time'] <= $this->start_time) {
                    $this->start_time = $item['start_time'];
                }
                if ($item['end_time'] >= $this->end_time) {
                    $this->end_time = $item['end_time'];
                }
            }
        }
    }

    public function setTotalSlot()
    {
        $this->total_slot = $this->total_adult + $this->total_child;
    }

    public function setPrice()
    {
        $this->price = 0;
        if (!empty($this->service_utility_config_id)) {
            $serviceUtilityConfig = ServiceUtilityConfig::findOne(['id' => $this->service_utility_config_id]);
            if (!empty($serviceUtilityConfig) && !empty($this->book_time)) {
                $book_time = json_decode($this->book_time, true);
                foreach ($book_time as $item) {
                    $this->price += $serviceUtilityConfig->getPrice($item['start_time'], $item['end_time'], $this->total_adult, $this->total_child);
                }
            }
        }
    }

    public function setJsonDesc()
    {
        $serviceUtilityFree = ServiceUtilityFree::findOne(['id' => $this->service_utility_free_id]);
        $serviceUtilityConfig = ServiceUtilityConfig::findOne(['id' => $this->service_utility_config_id]);
        $serviceMapManagement = ServiceMapManagement::findOne(['id' => $this->service_map_management_id]);
        $json_desc = [
            'text' => $this->description,
        ];
        if (!empty($serviceUtilityFree)) {
            $json_desc['service_utility_free_name'] = $serviceUtilityFree->name;
        }
        if (!empty($serviceUtilityConfig)) {
            $json_desc['service_utility_config_name'] = $serviceUtilityConfig->name;
        }
        if (!empty($serviceMapManagement)) {
            $json_desc['service_map_management_name'] = $serviceMapManagement->service_name;
        }
        $this->json_desc = json_encode($json_desc);
    }

    public function resetInfo()
    {
        $this->status = self::STATUS_CANCEL_SYSTEM;
        if (!$this->save()) {
            Yii::error($this->errors);
            return false;
        }
        return true;
    }

    /*
     * return:
     * false => được hủy, trurn => không được hủy
     */
    public function timeoutCancelBook()
    {
        //nếu hủy khi book đã được duyệt thì phải check thời gian được phép hủy
        if($this->status == ServiceUtilityBooking::STATUS_ACTIVE){
            if(!$this->serviceUtilityConfig){
                return true;
            }

            if(($this->start_time - $this->serviceUtilityFree->timeout_cancel_book * 60) < time()){
                return true;
            }
        }
        return false;
    }

    public function cancelBook($cancel_status = self::STATUS_CANCEL_SYSTEM, $reason = null)
    {
        try {
            //hủy là phải xóa phí cho trường hơp chưa xác nhận thanh toán thành công
//            ==> không xóa phí => có thể thanh toán cho những book đã hủy (trường hợp đã chuyển tiền)
            //các trường hợp hủy book sẽ hủy phí
            //1 - chưa tạo yêu cầu thanh toán
            //2 - yêu câu thanh toán đã bị hủy
            //3 - book đã hủy (hệ thống hủy hoặc cư dân hủy), sau đó mới hủy yêu cầu thanh toán (chỉ yêu cầu thanh toán cư dân tạo và hệ thống tạo)
            if ($this->status == self::STATUS_CREATE) {
                $payment_fee_ids = $this->getPaymentIds();
                if(!empty($payment_fee_ids)){
                    $paymentGenCodeItem = PaymentGenCodeItem::findOne(['service_payment_fee_id' => $payment_fee_ids]);
                    if(empty($paymentGenCodeItem)){
                        ServicePaymentFee::deleteAll(['id' => $payment_fee_ids, 'money_collected' => 0]);
                        $this->service_payment_fee_incurred_ids = null;
                        $this->service_payment_fee_deposit_ids = null;
                        $this->service_payment_fee_ids_text_search = null;
                        $this->is_created_fee = null;

                        //lấy lại những phí đã thanh toán
                        $servicePaymentFees = ServicePaymentFee::find()->where(['id' => $payment_fee_ids])->all();
                        $incurred_ids = [];//phat sinh
                        $deposit_ids = [];//đặt cọc
                        $search_ids = [];
                        if(!empty($servicePaymentFees)){
                            foreach ($servicePaymentFees as $servicePaymentFee){
                                if($servicePaymentFee->for_type == ServicePaymentFee::FOR_TYPE_0){
                                    $this->service_payment_fee_id = $servicePaymentFee->id;
                                    $search_ids[] = $servicePaymentFee->id;
                                }else if($servicePaymentFee->for_type == ServicePaymentFee::FOR_TYPE_1){
                                    $deposit_ids[] = $servicePaymentFee->id;
                                    $search_ids[] = $servicePaymentFee->id;
                                }else if($servicePaymentFee->for_type == ServicePaymentFee::FOR_TYPE_2){
                                    $incurred_ids[] = $servicePaymentFee->id;
                                    $search_ids[] = $servicePaymentFee->id;
                                }
                            }
                        }
                        if(!empty($deposit_ids)){
                            $this->service_payment_fee_deposit_ids = Json::encode($deposit_ids);
                        }
                        if(!empty($incurred_ids)){
                            $this->service_payment_fee_incurred_ids = Json::encode($incurred_ids);
                        }
                        if(!empty($search_ids)){
                            $this->is_created_fee = ServiceUtilityBooking::IS_CREATED_FEE;
                            $fee_ids_text_search = implode(',', $search_ids);
                            if(!empty($fee_ids_text_search)){
                                $fee_ids_text_search = ','.trim($fee_ids_text_search, ',').',';
                            }
                            $this->service_payment_fee_ids_text_search = $fee_ids_text_search;
                        }
                    }
                }
            }
            $this->status = $cancel_status;
            $this->reason = $reason;
            if (!$this->save()) {
                Yii::error($this->errors);
                return false;
            }

//            $status_text = 'Hủy yêu cầu đặt chỗ';
//            $description = 'Yêu cầu đặt chỗ';
//            $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING, [$status_text, $this->serviceUtilityConfig->name . ' ' . $this->serviceUtilityConfig->address]);
//            $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_EN, [$status_text, $this->serviceUtilityConfig->name . ' ' . $this->serviceUtilityConfig->address]);
//            $data = [
//                'type' => 'booking',
//                'action' => 'change_status',
//                'booking_id' => $this->id,
//            ];
//            $ACTION_KEY_CREATE = ManagementNotifyReceiveConfig::ACTION_KEY_CANCEL;
//            self::oneSignalSend($title, $description, $title_en, $description_en, $data, $ACTION_KEY_CREATE, ResidentUserNotify::TYPE_SERVICE_BOOKING);
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }

    /*
    *
    */
    public function sendNotifyToManagementUser($managementUserIgnore = null, $residentUser = null, $is_create = self::UPDATE)
    {
        try {
            $resident_user_first_name = '';
            if(!empty($residentUser)){
                $residentUserIgnore = ApartmentMapResidentUser::findOne([
                    'resident_user_phone' => $residentUser->phone,
                    'is_deleted' => ApartmentMapResidentUser::NOT_DELETED,
                    'apartment_id' => $this->apartment_id
                ]);
                if($residentUserIgnore){
                    $resident_user_first_name = $residentUserIgnore->resident_user_first_name;
                }
            }
            // nếu tồn tài config không cho gửi thì sẽ không gửi
//            $notifySendConfig = NotifySendConfig::findOne(['building_cluster_id' => $this->buildingCluster->id, 'type' => NotifySendConfig::TYPE_BOOKING, 'send_notify_app' => NotifySendConfig::NOT_SEND]);
//            if(!empty($notifySendConfig)){
//                return false;
//            }

            $ACTION_KEY_CREATE = ManagementNotifyReceiveConfig::ACTION_KEY_CREATE;
            $title = '';
            $title_en = '';
            $description = '';
            $description_en = '';
            $data = [];
            $url = $this->buildingCluster->domain . '/main/bookinglist/detail/' . $this->id . '/info';
            $app_id = $this->buildingCluster->one_signal_app_id;
            $structure_name = $this->apartment->buildingArea->parent_path . $this->apartment->buildingArea->name . '/' . $this->apartment->name;
            if ($is_create == self::CREATE) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_NEW_BOOKING_TO_MANAGEMENT, [$resident_user_first_name, $structure_name, $this->serviceUtilityFree->name]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_NEW_BOOKING_TO_MANAGEMENT_EN, [$resident_user_first_name, $structure_name, $this->serviceUtilityFree->name_en]);
                $data = [
                    'type' => 'booking',
                    'apartment_id' => $this->apartment->id,
                    'booking_id' => $this->id,
                    'deep_link' => '/main/booking/apartment'
                ];
            } else if ($is_create == self::UPDATE_STATUS) {
                $status_text = 'Yêu cầu';
                if ($this->status == self::STATUS_ACTIVE) {
                    $ACTION_KEY_CREATE = ManagementNotifyReceiveConfig::ACTION_KEY_APPROVED;
                    $status_text = 'Yêu cầu đặt chỗ đã được duyệt';
                } else if ($this->status == self::STATUS_CANCEL) {
                    $ACTION_KEY_CREATE = ManagementNotifyReceiveConfig::ACTION_KEY_CANCEL;
                    $status_text = 'Hủy yêu cầu đặt chỗ';
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING, [$resident_user_first_name, $structure_name, $this->serviceUtilityFree->name]);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_EN, [$resident_user_first_name, $structure_name, $this->serviceUtilityFree->name_en]);
                }
                $data = [
                    'type' => 'booking',
                    'action' => 'change_status',
                    'booking_id' => $this->id,
                ];
            }

            //gửi thông báo cho các user thuộc nhóm quyền này biết có yêu cầu được cập nhật
            $oneSignalApi = new OneSignalApi();
            $mapAuthGroupsIds = json_decode($this->buildingCluster->setting_group_receives_notices_financial, true);
            $managementUserIds = [];

            $typeNotify = ManagementUserNotify::TYPE_SERVICE_BOOKING;
            $request_answer_id = null;
            $request_answer_internal_id = null;

            $managementUsers = ManagementUser::find()->where([
                                                                // 'auth_group_id' => 1, 
                                                                'building_cluster_id' => $this->buildingCluster->id,
                                                                'is_deleted' => ManagementUser::NOT_DELETED])->all();
            foreach ($managementUsers as $managementUser) {

                //kiểm tra xem user này có cấu hình nhận thông báo tạo phí hay không không
//                $checkReceive = $managementUser->checkNotifyReceiveConfig(ManagementNotifyReceiveConfig::CHANNEL_NOTIFY_APP, ManagementNotifyReceiveConfig::TYPE_BOOKING, [$ACTION_KEY_CREATE => ManagementNotifyReceiveConfig::NOT_RECEIVED]);
//                if(!empty($checkReceive)){
//                    continue;
//                }

                $managementUserIds[] = $managementUser->id;

                //khởi tạo log cho từng management user
                $managementUserNotify = new ManagementUserNotify();
                $managementUserNotify->building_cluster_id  = $this->building_cluster_id;
                $managementUserNotify->building_area_id     = $this->building_area_id;
                $managementUserNotify->management_user_id   = $managementUser->id;
                $managementUserNotify->type                 = $typeNotify;
                $managementUserNotify->service_booking_id   = $this->id;
                $managementUserNotify->title                = $title;
                $managementUserNotify->description          = $description;
                $managementUserNotify->title_en             = $title_en;
                $managementUserNotify->description_en       = $description_en;
                if (!$managementUserNotify->save()) {
                    Yii::error($managementUserNotify->getErrors());
                }
                //end log
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
    public function sendNotifyToResidentUser($managementUserIgnore = null, $residentUserIgnore = null, $is_create = self::UPDATE,$isCheckFee = false)
    {
        try {
            // nếu tồn tài config không cho gửi thì sẽ không gửi
//            $notifySendConfig = NotifySendConfig::findOne(['building_cluster_id' => $this->buildingCluster->id, 'type' => NotifySendConfig::TYPE_BOOKING, 'send_notify_app' => NotifySendConfig::NOT_SEND]);
//            if(!empty($notifySendConfig)){
//                return false;
//            }
            $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_CREATE;
            $title = $description = '';
            $title_en = $description_en = '';
            $data = [
                'type' => 'booking',
                'action' => 'create',
                'booking_id' => $this->id,
                'apartment_id' => $this->apartment_id,
            ];
            if ($is_create == self::CREATE) {
                $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_UPDATE;
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CREATE_FEE_BOOKING_TO_APARTMENT, [$this->serviceUtilityFree->name ]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CREATE_FEE_BOOKING_TO_APARTMENT_EN, [$this->serviceUtilityFree->name_en]);
                $data = [
                    'type' => 'booking',
                    'action' => 'create',
                    'booking_id' => $this->id,
                    'apartment_id' => $this->apartment_id,
                ];
            }else if ($is_create == self::UPDATE) {
                $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_UPDATE;
                $title = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_UPDATE_BOOKING_TO_APARTMENT, ['Yêu cầu đặt chỗ', $this->serviceUtilityConfig->name . ' ' . $this->serviceUtilityConfig->address]);
                $data = [
                    'type' => 'booking',
                    'action' => 'update',
                    'booking_id' => $this->id,
                    'apartment_id' => $this->apartment_id,
                ];
            } else if ($is_create == self::UPDATE_STATUS) {
                if ($this->status == self::STATUS_ACTIVE) {
                    $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_APPROVED;
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_APPROVED, [$this->serviceUtilityFree->name]);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_APPROVED_EN, [$this->serviceUtilityFree->name_en]);
                } else if ($this->status == self::STATUS_CANCEL_BQL) {
                    $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_CANCEL;
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_CANCEL, [$this->serviceUtilityFree->name]);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_BOOKING_CANCEL_EN, [$this->serviceUtilityFree->name_en]);
                }else if ($this->status == self::STATUS_CANCEL_SYSTEM) {
                    $ACTION_KEY_CREATE = ResidentNotifyReceiveConfig::ACTION_KEY_CANCEL;
                    $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::SYSTEMS_CHANGE_STATUS_BOOKING_CANCEL, [$this->serviceUtilityFree->name]);
                    $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::SYSTEMS_CHANGE_STATUS_BOOKING_CANCEL_EN, [$this->serviceUtilityFree->name_en]);
                }
                $data = [
                    'type' => 'booking',
                    'action' => 'change_status',
                    'booking_id' => $this->id,
                    'apartment_id' => $this->apartment_id,
                ];
            }else if($is_create == self::SEND_NOTIFY){
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_BOOKING_SEND_NOTIFY, [$this->serviceUtilityConfig->name, date('H:i:s', $this->start_time), date('d-m-Y', $this->start_time)]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_BOOKING_SEND_NOTIFY_EN, [$this->serviceUtilityConfig->name, date('H:i:s', $this->start_time), date('d-m-Y', $this->start_time)]);
                $data = [
                    'type' => 'booking',
                    'action' => 'send_notify',
                    'booking_id' => $this->id,
                    'apartment_id' => $this->apartment_id,
                ];
            }
            $notify_type = ResidentUserNotify::TYPE_SERVICE_BOOKING;
            if($isCheckFee)
            {
                $data['type'] = 'incurred_fee';
                $notify_type = ResidentUserNotify::TYPE_MANAGEMENT_CREATE_PAYMENT_FEE;
            }
            self::oneSignalSend($title, $description, $title_en, $description_en, $data, $ACTION_KEY_CREATE, $notify_type);
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
            if (!empty($residentUserIgnore)) {
                if ($apartmentMapResidentUser->resident_user_phone == $residentUserIgnore->phone) {
                    continue;
                }
            }

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
            $residentUserNotify->service_booking_id = $this->id;
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

    public function getPaymentIds(){
        $ids = [];
        if(!empty($this->service_payment_fee_id)){
            $ids[] = $this->service_payment_fee_id;
        }
        if(!empty($this->service_payment_fee_deposit_ids)){
            $ids_1 = Json::decode($this->service_payment_fee_deposit_ids, true);
            $ids = array_merge($ids, $ids_1);
        }
        if(!empty($this->service_payment_fee_incurred_ids)){
            $ids_2 = Json::decode($this->service_payment_fee_incurred_ids, true);
            $ids = array_merge($ids, $ids_2);
        }
        return $ids;
    }

    public function setIsPaid(){
        $is_paid = 1;
        $paymentFees = ServicePaymentFee::find()->where(['id' => $this->getPaymentIds()])->all();
        if(!empty($paymentFees)){
            foreach ($paymentFees as $paymentFee){
                if($paymentFee->status == ServicePaymentFee::STATUS_UNPAID){
                    $is_paid = 0;
                }
            }
        }else{
            if($this->status < ServiceUtilityBooking::STATUS_CREATE){
                $is_paid = 0;
            }
        }
        if($this->is_paid != $is_paid){
            $this->is_paid = $is_paid;
            if(!$this->save()){
                Yii::error($this->errors);
            }
        }
        return $this->is_paid;
    }

    /*
    *
    */
    public function sendNotifyToManagementUserChangePhone($residentUser, $is_create = self::UPDATE,$building_cluster_id = null,$apartment_id = null,$oldPhone = null,$newPhone = null)
    {
        try {
            $resident_user_first_name = '';
            if(!empty($residentUser)){
                $residentUserIgnore = ApartmentMapResidentUser::findOne([
                    'resident_user_phone' => $residentUser->phone,
                    'is_deleted' => ApartmentMapResidentUser::NOT_DELETED,
                    'apartment_id' => $apartment_id
                ]);
                if($residentUserIgnore){
                    $resident_user_first_name = $residentUserIgnore->resident_user_first_name ?? '';
                }
            }
            $buildingCluster = BuildingCluster::find()->where(['id'=>$building_cluster_id])->one();
            $ACTION_KEY_CREATE = ManagementNotifyReceiveConfig::ACTION_KEY_CREATE;
            $title = '';
            $title_en = '';
            $description = '';
            $description_en = '';
            $data = [];
            $url = $buildingCluster->domain. '/main/bookinglist/detail/' . $apartment_id. '/info';
            $app_id = $buildingCluster->one_signal_app_id;
            $structure_name = $residentUserIgnore->apartment->buildingArea->parent_path . $residentUserIgnore->apartment->buildingArea->name . '/' . $residentUserIgnore->apartment->name ;
            if ($is_create == self::CHANGE_PHONE) {
                $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::CHANGE_PHONE_NUMBER_FROM, [$resident_user_first_name, $structure_name, $oldPhone,$newPhone]);
                $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::CHANGE_PHONE_NUMBER_FROM_EN, [$resident_user_first_name, $structure_name, $oldPhone,$newPhone]);
                $data = [
                    'type' => 'booking',
                    'apartment_id' => $apartment_id,
                    'booking_id' => $apartment_id,
                    'deep_link' => '/main/booking/apartment'
                ];
            } 
            // $managementUserNotify = new ManagementUserNotify();
            // $managementUserNotify->building_cluster_id  = $building_cluster_id;
            // $managementUserNotify->building_area_id     = $residentUserIgnore->building_area_id;
            // $managementUserNotify->management_user_id   = 1;
            // $managementUserNotify->type                 = self::CHANGE_PHONE;
            // $managementUserNotify->service_booking_id   = 1;
            // $managementUserNotify->title                = $title;
            // $managementUserNotify->description          = $description;
            // $managementUserNotify->title_en             = $title_en;
            // $managementUserNotify->description_en       = $description_en;
            // if (!$managementUserNotify->save()) {
            //     Yii::error($managementUserNotify->getErrors());
            // }
            //gửi thông báo cho các user thuộc nhóm quyền này biết có yêu cầu được cập nhật
            $oneSignalApi = new OneSignalApi();
            $mapAuthGroupsIds = json_decode($buildingCluster->setting_group_receives_notices_financial, true);
            $managementUserIds = [];

            $typeNotify = ManagementUserNotify::CHANGE_PHONE;
            $request_answer_id = null;
            $request_answer_internal_id = null;

            $managementUsers = ManagementUser::find()->where([
                                                                'building_cluster_id' => $building_cluster_id,
                                                                'is_deleted' => ManagementUser::NOT_DELETED])->all();
            foreach ($managementUsers as $managementUser) {
                $managementUserIds[] = $managementUser->id;

                //khởi tạo log cho từng management user
                $managementUserNotify = new ManagementUserNotify();
                $managementUserNotify->building_cluster_id  = $building_cluster_id;
                $managementUserNotify->building_area_id     = $residentUserIgnore->building_area_id ?? 1;
                $managementUserNotify->management_user_id   = $managementUser->id;
                $managementUserNotify->type                 = $typeNotify;
                // $managementUserNotify->service_booking_id   = $apartment_id ?? "";
                $managementUserNotify->title                = $title;
                $managementUserNotify->description          = $description;
                $managementUserNotify->title_en             = $title_en;
                $managementUserNotify->description_en       = $description_en;
                if (!$managementUserNotify->save()) {
                    Yii::error($managementUserNotify->getErrors());
                }
                //end log
            }
            // //gửi thông báo theo device token
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

}
