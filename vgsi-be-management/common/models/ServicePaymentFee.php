<?php

namespace common\models;

use common\helpers\CUtils;
use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

/**
 * This is the model class for table "service_payment_fee".
 *
 * @property int $id
 * @property int $service_map_management_id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property string $description
 * @property string $description_en
 * @property string $json_desc
 * @property int $price
 * @property int $is_draft
 * @property int $status 0 : chưa thanh toán, 1 - đã thanh toán
 * @property int $fee_of_month Thành toán phí tháng ?
 * @property int $day_expired Ngày hết hạn thanh toán
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property string $service_bill_ids
 * @property string $service_bill_codes
 * @property int $type
 * @property int $start_time
 * @property int $end_time
 * @property int $money_collected
 * @property int $more_money_collecte
 * @property int $approved_by_id
 * @property int $is_debt
 * @property int $for_type
 * @property int $service_bill_invoice_id
 * @property int $service_utility_booking_id
 *
 * @property Apartment $apartment
 * @property ManagementUser $managementUser
 * @property ServiceMapManagement $serviceMapManagement
 * @property BuildingCluster $buildingCluster
 * @property BuildingArea $buildingArea
 */
class ServicePaymentFee extends \yii\db\ActiveRecord
{
    const STATUS_CANCEL = -1;
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    public static $statusList = [
        self::STATUS_CANCEL => "Đã hủy",
        self::STATUS_UNPAID => "Chưa thanh toán",
        self::STATUS_PAID => "Đã thanh toán",
    ];

    const IS_NOT_DRAFT = 0;
    const IS_DRAFT = 1;

    const IS_NOT_DEBT = 0;
    const IS_DEBT = 1;

    public $total_debt;
    public $apartment_name;
    public $apartment_parent_path;

    const TYPE_SERVICE_WATER_FEE = 0;
    const TYPE_SERVICE_BUILDING_FEE = 1;
    const TYPE_SERVICE_PARKING_FEE = 2;
    const TYPE_SERVICE_ELECTRIC_FEE = 3;
    const TYPE_SERVICE_BOOKING_FEE = 4;
    const TYPE_SERVICE_OLD_DEBIT_FEE = 5;

    public static $typeList = [
        self::TYPE_SERVICE_WATER_FEE => "Nước",
        self::TYPE_SERVICE_BUILDING_FEE => "Phí quản lý",
        self::TYPE_SERVICE_PARKING_FEE => "Gửi xe",
        self::TYPE_SERVICE_ELECTRIC_FEE => "Điện",
        self::TYPE_SERVICE_BOOKING_FEE => "Tiện ích",
        self::TYPE_SERVICE_OLD_DEBIT_FEE => "Nợ cũ chuyển giao",
    ];

    public static $typeList_en = [
        self::TYPE_SERVICE_WATER_FEE => "Water",
        self::TYPE_SERVICE_BUILDING_FEE => "Management fee",
        self::TYPE_SERVICE_PARKING_FEE => "Parking",
        self::TYPE_SERVICE_ELECTRIC_FEE => "Electricity",
        self::TYPE_SERVICE_BOOKING_FEE => "Amenity",
        self::TYPE_SERVICE_OLD_DEBIT_FEE => "Outstanding liabilities",
    ];

    const FOR_TYPE_0 = 0;
    const FOR_TYPE_1 = 1;
    const FOR_TYPE_2 = 2;

    public static $forTypeList = [
        self::FOR_TYPE_0 => "Phí sử dụng",
        self::FOR_TYPE_1 => "Phí đặt cọc",
        self::FOR_TYPE_2 => "Phí phát sinh",
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_payment_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_map_management_id', 'building_cluster_id', 'apartment_id', 'price'], 'required'],
            [['service_bill_invoice_id', 'for_type', 'is_debt', 'approved_by_id','service_utility_booking_id','money_collected', 'more_money_collecte', 'type', 'start_time', 'end_time', 'is_draft', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'price', 'status', 'fee_of_month', 'day_expired', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description', 'description_en', 'json_desc'], 'string'],
            [['total_debt', 'apartment_name', 'apartment_parent_path', 'service_bill_codes', 'service_bill_ids'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'service_utility_booking_id' => Yii::t('common', 'Service utility booking'),
            'description' => Yii::t('common', 'Description'),
            'json_desc' => Yii::t('common', 'Json Desc'),
            'price' => Yii::t('common', 'Price'),
            'is_draft' => Yii::t('common', 'Is Draft'),
            'status' => Yii::t('common', 'Status'),
            'fee_of_month' => Yii::t('common', 'Fee Of Month'),
            'day_expired' => Yii::t('common', 'Day Expired'),
            'service_bill_ids' => Yii::t('common', 'Service Bill Id'),
            'service_bill_codes' => Yii::t('common', 'Service Bill Code'),
            'type' => Yii::t('common', 'Type'),
            'start_time' => Yii::t('common', 'Start Time'),
            'end_time' => Yii::t('common', 'End Time'),
            'money_collected' => Yii::t('common', 'Money Collected'),
            'more_money_collecte' => Yii::t('common', 'More Money Collecte'),
            'approved_by_id' => Yii::t('common', 'Approved By Id'),
            'for_type' => Yii::t('common', 'For Type'),
            'service_bill_invoice_id' => Yii::t('common', 'Service Bill Invoice Id'),
            'service_utility_booking_id' => Yii::t('common', 'Service utility booking'),
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
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }

 /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingArea()
    {
        return $this->hasOne(BuildingArea::className(), ['id' => 'building_area_id']);
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
    public function getManagementUser()
    {
        return $this->hasOne(ManagementUser::className(), ['id' => 'approved_by_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }

    /*
     *
     */
    public function sendNotifyToResidentUser($titleBooking = "",$titleBookingEn = "")
    {
        try {
//            $url = $this->buildingCluster->domain . '/main/finance/bills/detail/' . $this->id;
            $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_AUTO, [$titleBooking]);
            $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::MANAGEMENT_CHANGE_STATUS_AUTO_EN, [$titleBookingEn]);
//            $description = $this->payer_name . " (" . $this->apartment->name . " - " . trim($this->apartment->parent_path, '/') . "): Tạo phiếu thanh toán với số tiền là " . CUtils::formatPrice($this->total_price) . ' đ, mã phiếu ' . $this->code;
//            $title = '['.$this->buildingCluster->name.'] Thông báo có phí cần thanh toán';
//            $description = $title;
            $data = [
                'type' => 'payment_fee',
                'action' => "pay",
                'order_id' => $this->id,
                'apartment_id' => $this->apartment_id,
            ];
            //gửi thông báo cho các user thuộc nhóm quyền này biết có yêu cầu được cập nhật
            $oneSignalApi = new OneSignalApi();

            $typeNotify = ResidentUserNotify::TYPE_MANAGEMENT_CREATE_PAYMENT_FEE;
            $request_answer_id = null;
            $request_answer_internal_id = null;

            $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
            $residentUserIds = [];
            foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
                if ($apartmentMapResidentUser->resident_user_is_send_notify == ResidentUser::IS_SEND_NOTIFY) {
                    $residentUserIds[] = $apartmentMapResidentUser->resident->id ?? null;
                }

                //khởi tạo log cho từng resident user
                $residentUserNotify = new ResidentUserNotify();
                $residentUserNotify->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
                $residentUserNotify->building_area_id = $apartmentMapResidentUser->building_area_id;
                $residentUserNotify->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
                $residentUserNotify->type = $typeNotify;
                $residentUserNotify->service_payment_fee_id = $this->id;
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
            $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, null);
            //end gửi thông báo theo device token
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }

    public function updateEndTime($id)
    {
        $model = $this;
        if (empty($model->id)) {
            $model = self::findOne(['id' => $id]);
        }

        if ($model->type == self::TYPE_SERVICE_WATER_FEE) {
            $serviceWaterFee = ServiceWaterFee::findOne(['service_payment_fee_id' => $model->id]);
            if(!empty($serviceWaterFee)){
                return $serviceWaterFee->resetInfo();
            }
        } else if ($model->type == self::TYPE_SERVICE_ELECTRIC_FEE) {
            $serviceElectricFee = ServiceElectricFee::findOne(['service_payment_fee_id' => $model->id]);
            if(!empty($serviceElectricFee)){
                return $serviceElectricFee->resetInfo();
            }
        } else if ($model->type == self::TYPE_SERVICE_BUILDING_FEE) {
            $serviceBuildingFee = ServiceBuildingFee::findOne(['service_payment_fee_id' => $model->id]);
            if(!empty($serviceBuildingFee)){
                return $serviceBuildingFee->resetInfo();
            }
        } else if ($model->type == self::TYPE_SERVICE_PARKING_FEE) {
            $serviceParkingFee = ServiceParkingFee::findOne(['service_payment_fee_id' => $model->id]);
            if(!empty($serviceParkingFee)){
                return $serviceParkingFee->resetInfo();
            }
        } else if ($model->type == self::TYPE_SERVICE_BOOKING_FEE) {
            $serviceUtilityBooking = ServiceUtilityBooking::findOne(['service_payment_fee_id' => $model->id]);
            if(!empty($serviceUtilityBooking)){
                return $serviceUtilityBooking->resetInfo();
            }
        } else if ($model->type == self::TYPE_SERVICE_OLD_DEBIT_FEE) {
            $serviceOldDebitFee = ServiceOldDebitFee::findOne(['service_payment_fee_id' => $model->id]);
            if(!empty($serviceOldDebitFee)){
                return $serviceOldDebitFee->resetInfo();
            }
        }
        return false;
    }

    public function updatePriceService($id)
    {
        $model = $this;
        if (empty($model->id)) {
            $model = self::findOne(['id' => $id]);
        }
        if ($model->type == self::TYPE_SERVICE_WATER_FEE) {
            $serviceFee = ServiceWaterFee::findOne(['service_payment_fee_id' => $model->id]);
        } else if ($model->type == self::TYPE_SERVICE_ELECTRIC_FEE) {
            $serviceFee = ServiceElectricFee::findOne(['service_payment_fee_id' => $model->id]);
        } else if ($model->type == self::TYPE_SERVICE_BUILDING_FEE) {
            $serviceFee = ServiceBuildingFee::findOne(['service_payment_fee_id' => $model->id]);
        } else if ($model->type == self::TYPE_SERVICE_PARKING_FEE) {
            $serviceFee = ServiceParkingFee::findOne(['service_payment_fee_id' => $model->id]);
        } else if ($model->type == self::TYPE_SERVICE_BOOKING_FEE) {
            $serviceFee = ServiceUtilityBooking::find()->where(['like', 'service_payment_fee_ids_text_search', ','.$model->id.','])->one();
            if(!empty($serviceFee)){
                if($model->for_type == ServicePaymentFee::FOR_TYPE_1){
                    $serviceFee->total_deposit_money = $model->price;
                }else if($model->for_type == ServicePaymentFee::FOR_TYPE_2){
                    $price = $model->price;
                    if(!empty($serviceFee->service_payment_fee_incurred_ids)){
                        $feeIds = Json::decode($serviceFee->service_payment_fee_incurred_ids, true);
                        $servicePaymentFees = ServicePaymentFee::find()->where(['id' => $feeIds])->all();
                        foreach ($servicePaymentFees as $servicePaymentFee){
                            if($servicePaymentFee->id !== $model->id){
                                $price = $price + $servicePaymentFee->price;
                            }
                        }
                    }
                    $serviceFee->total_incurred_money = $price;
                }else {
                    $serviceFee->price = $model->price;
                    $serviceFee->description = $model->description;
                }
                if(!$serviceFee->save()){
                    Yii::error($serviceFee->errors);
                    return false;
                }
                return true;
            }
        } else if ($model->type == self::TYPE_SERVICE_OLD_DEBIT_FEE) {
            $serviceFee = ServiceOldDebitFee::findOne(['service_payment_fee_id' => $model->id]);
        }
        if(!empty($serviceFee)){
            $serviceFee->total_money = $model->price;
            $serviceFee->description = $model->description;
            if(!$serviceFee->save()){
                Yii::error($serviceFee->errors);
                return false;
            }
            return true;
        }
        return false;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        // Place your custom code here
        $this->more_money_collecte = $this->price - $this->money_collected;
        return true;
    }
}
