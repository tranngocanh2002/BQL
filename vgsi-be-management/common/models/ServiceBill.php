<?php

namespace common\models;

use common\helpers\CUtils;
use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use common\helpers\StringUtils;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "service_bill".
 *
 * @property int $id
 * @property string $code Mã phiếu thu
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $management_user_id Người tạo phiếu
 * @property int $resident_user_id Chủ hộ ở thời điểm hiện tại
 * @property string $resident_user_name Chủ hộ ở thời điểm hiện tại
 * @property string $payer_name Người nộp tiền
 * @property string $number Số phiếu
 * @property int $type_payment 0 - Tiền mặt, 1 - chuyển khoản
 * @property int $status 0 - chưa thanh toán, 1 - đã thanh toán, 10 - Block
 * @property int $total_price
 * @property int $is_deleted 0 - chưa xóa, 1 - đã xóa
 * @property int $service_provider_id
 * @property string $management_user_name
 * @property string $description
 * @property int $payment_date
 * @property int $execution_date
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $type 0 - phiếu thu, 1 - phiếu chi
 * @property int $payment_gen_code_id
 * @property string $bank_name
 * @property string $bank_account
 * @property string $bank_holders
 * @property string $note
 *
 * @property Apartment $apartment
 * @property ServiceProvider $serviceProvider
 * @property ServiceBillItem[] $serviceBillItems
 * @property ManagementUser $managementUser
 * @property BuildingCluster $buildingCluster
 * @property BuildingArea $buildingArea
 */
class ServiceBill extends \yii\db\ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    const STATUS_DRAFT = -1;
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCEL = 2;
    const STATUS_BLOCK = 10;

    public static $status_lst = [
        self::STATUS_DRAFT => "Nháp",
        self::STATUS_UNPAID => "Chờ thanh toán",
        self::STATUS_PAID => "Đã thanh toán",
        self::STATUS_CANCEL => "Đã hủy",
        self::STATUS_BLOCK => "Chốt sổ",
    ];

//bao gồm: tiền mặt, chuyển khoản, cà thẻ, ví momo nhé a
    const TYPE_PAYMENT_CASH = 0;
    const TYPE_PAYMENT_INTERNET_BANKING = 1;
    const TYPE_PAYMENT_ONLINE = 2;
    const TYPE_PAYMENT_MOMO = 3;
    const TYPE_PAYMENT_VNPAY = 4;
//    const TYPE_PAYMENT_CA_THE = 4;

    public static $type_payment_lst = [
        self::TYPE_PAYMENT_CASH => "Tiền mặt",
        self::TYPE_PAYMENT_INTERNET_BANKING => "Chuyển khoản",
        self::TYPE_PAYMENT_ONLINE => "Momo",
        self::TYPE_PAYMENT_MOMO => "Payoo",
        self::TYPE_PAYMENT_VNPAY => "VNPay",
//        self::TYPE_PAYMENT_CA_THE => "Cà thẻ",
    ];

    const TYPE_0 = 0;
    const TYPE_1 = 1;
    public static $type_lst = [
        self::TYPE_0 => "Phiếu thu",
        self::TYPE_1 => "Phiếu chi",
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_bill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'building_cluster_id'], 'required'],
            [['payment_date', 'execution_date', 'building_cluster_id', 'building_area_id', 'service_provider_id', 'total_price', 'apartment_id', 'management_user_id', 'resident_user_id', 'type_payment', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type', 'payment_gen_code_id'], 'integer'],
            [['code', 'resident_user_name', 'payer_name', 'number', 'management_user_name', 'note', 'bank_name', 'bank_account', 'bank_holders'], 'string', 'max' => 255],
            [['description'], 'string'],
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
            'management_user_id' => Yii::t('common', 'Management User ID'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'resident_user_name' => Yii::t('common', 'Resident User Name'),
            'payer_name' => Yii::t('common', 'Payer Name'),
            'type_payment' => Yii::t('common', 'Type Payment'),
            'status' => Yii::t('common', 'Status'),
            'type' => Yii::t('common', 'Type'),
            'total_price' => Yii::t('common', 'Total Price'),
            'service_provider_id' => Yii::t('common', 'Service Provider'),
            'number' => Yii::t('common', 'Number'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'management_user_name' => Yii::t('common', 'Management User Name'),
            'description' => Yii::t('common', 'Description'),
            'payment_date' => Yii::t('common', 'Payment Date'),
            'execution_date' => Yii::t('common', 'Execution Date'),
            'note' => Yii::t('common', 'Note'),
            'bank_name' => Yii::t('common', 'Bank Name'),
            'bank_account' => Yii::t('common', 'Bank Account'),
            'bank_holders' => Yii::t('common', 'Bank Holders'),
            'payment_gen_code_id' => Yii::t('common', 'Payment Gen Code ID'),
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

    private function generateCodeNew($code, $length)
    {
        $apartment = $this->apartment;
        if (!empty($apartment)) {
            $serviceBill = ServiceBill::findOne(['code' => $code, 'building_cluster_id' => $apartment->building_cluster_id, 'is_deleted' => ServiceBill::NOT_DELETED]);
            if (!empty($serviceBill)) {
                $code_new = StringUtils::randomStr($length);
                return self::generateCodeNew($code_new, $length);
            }
            return $code;
        }
        return null;
    }

    /**
     * Generates new code
     */
    public function generateCode()
    {
        $this->code = strtoupper(self::generateCodeNew(StringUtils::randomStr(6), 6));
    }

    /**
     * Generates new code
     */
    public function generateCodeVnpay($code = "")
    {
        $this->code = strtoupper(self::generateCodeNew($code, 8));
    }

    /**
     * Generates new number
     * number: <TM|CK>.<Tên tòa>.<Năm>.<Số tăng dần>
     */
    public function generateNumber()
    {
        if(!isset($this->type)){
            $this->type = ServiceBill::TYPE_0;
        }
        $year = date('Y');
        $type_name = 'CK';
        $type_payment = ServiceBillNumber::TYPE_PAYMENT_INTERNET_BANKING;
        if($this->type_payment == self::TYPE_PAYMENT_CASH){
            $type_name = 'TM';
            $type_payment = ServiceBillNumber::TYPE_PAYMENT_CASH;
        }
        $index_number = 1;
        $serviceBillNumberCheck = ServiceBillNumber::find()->where(['building_cluster_id' => $this->building_cluster_id, 'service_bill_type_payment' => $type_payment, 'type' => $this->type, 'year' => (int)$year])->orderBy(['index_number' => SORT_DESC])->one();
        if(!empty($serviceBillNumberCheck)){
            $index_number = $serviceBillNumberCheck->index_number + 1;
        }
        $short_name = $this->buildingArea->short_name;
        $building = BuildingArea::findOne(['id' => $this->buildingArea->parent_id]);
        if(!empty($building)){
            $short_name = $building->short_name;
        }
        $this->number = $type_name.'.'.$short_name.'.'.$year.'.'.str_pad($index_number, 8, '0', STR_PAD_LEFT);

        $serviceBillNumber = new ServiceBillNumber();
        $serviceBillNumber->building_cluster_id = $this->building_cluster_id;
        $serviceBillNumber->year = (int)$year;
        $serviceBillNumber->index_number = $index_number;
        $serviceBillNumber->type = $this->type;
        $serviceBillNumber->service_bill_id = $this->id;
        $serviceBillNumber->service_bill_number = $this->number;
        $serviceBillNumber->service_bill_type_payment = $type_payment;
        if(!$serviceBillNumber->save()){
            Yii::error($serviceBillNumber->errors);
            return false;
        }
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
    public function getServiceProvider()
    {
        return $this->hasOne(ServiceProvider::className(), ['id' => 'service_provider_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagementUser()
    {
        return $this->hasOne(ManagementUser::className(), ['id' => 'management_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceBillItems()
    {
        return $this->hasMany(ServiceBillItem::className(), ['service_bill_id' => 'id']);
    }

    function afterDelete()
    {
        parent::afterDelete();
        ServiceBillItem::deleteAll(['service_bill_id' => $this->id]);
    }

    /**
     * @param $servicePaymentFees ServicePaymentFee[]
     * @return boolean
     */
    public function updatePaymentFees($servicePaymentFees, $feePriceIds, $need_update = false)
    {
        $total_price = 0;
        $serviceProvider = null;
        foreach ($servicePaymentFees as $servicePaymentFee) {
            //update trạng thái được tạo công nợ cho fee
            //thời điểm được tạo công nợ là thời điểm fee của tháng
            if($servicePaymentFee->is_debt == ServicePaymentFee::IS_NOT_DEBT){
                $servicePaymentFee->is_debt = ServicePaymentFee::IS_DEBT;
                $servicePaymentFee->fee_of_month = time();
            }
            $serviceBillItem = new ServiceBillItem();
            $serviceBillItem->service_bill_id = $this->id;
            $serviceBillItem->service_payment_fee_id = $servicePaymentFee->id;
            $serviceBillItem->service_map_management_id = $servicePaymentFee->service_map_management_id;
//            $serviceBillItem->description = $servicePaymentFee->description;
            $serviceBillItem->price = $feePriceIds[$servicePaymentFee->id];
            $serviceBillItem->fee_of_month = $servicePaymentFee->fee_of_month;
            if (!$serviceBillItem->save()) {
                Yii::error($servicePaymentFee->getErrors());
                return false;
            }
            $total_price += $feePriceIds[$servicePaymentFee->id];
            if ($serviceProvider == null) {
                $servicePaymentFee->serviceMapManagement;
                $serviceProvider = ServiceProvider::findOne(['id' => $servicePaymentFee->serviceMapManagement->service_provider_id]);
            }
            //update đã thu cho payment fee
            $servicePaymentFee->money_collected = $servicePaymentFee->money_collected + $feePriceIds[$servicePaymentFee->id];
            if($servicePaymentFee->type != ServicePaymentFee::TYPE_SERVICE_OLD_DEBIT_FEE){
                if($servicePaymentFee->money_collected > $servicePaymentFee->price){
                    Yii::error('Sai giá thu phí thường');
                    return false;
                }
            }else{
                if($servicePaymentFee->price > 0){
                    if($servicePaymentFee->money_collected > $servicePaymentFee->price){
                        Yii::error('Sai giá thu nợ cũ dương');
                        return false;
                    }
                }else{
                    if($servicePaymentFee->money_collected < $servicePaymentFee->price){
                        Yii::error('Sai giá thu nợ cũ âm');
                        return false;
                    }
                }
            }
            //update trạng thái
            if($this->status == self::STATUS_PAID){
                if($servicePaymentFee->money_collected == $servicePaymentFee->price){
                    $servicePaymentFee->status = ServicePaymentFee::STATUS_PAID;
                }
            }

            $ids = [];
            $codes = [];
            if(!empty($servicePaymentFee->service_bill_ids)){
                $ids = json_decode($servicePaymentFee->service_bill_ids, true);
            }
            if(!empty($servicePaymentFee->service_bill_codes)){
                $codes = json_decode($servicePaymentFee->service_bill_codes, true);
            }
            $ids[] = $this->id;
            $codes[] = $this->code;
            $servicePaymentFee->service_bill_ids = json_encode($ids);
            $servicePaymentFee->service_bill_codes = json_encode($codes);

            if(!$servicePaymentFee->save()){
                Yii::error($servicePaymentFee->getErrors());
                return false;
            }
        }

        if ($this->total_price != $total_price) {
            $this->total_price = $total_price;
            $need_update = true;
        }
        if ($serviceProvider && $this->service_provider_id != $serviceProvider->id) {
            $this->service_provider_id = $serviceProvider->id;
            $need_update = true;
        }
        if ($need_update) {
            if (!$this->save()) {
                Yii::error($this->getErrors());
                return false;
            }
        }
        return true;
    }

    public function resetPaymentFees($is_del = false)
    {
        if($this->type == ServiceBill::TYPE_1){//Nếu là phiếu chi thì ko xử lý gì thêm
            if($this->status == self::STATUS_CANCEL){
                $servicePaymentFee = ServicePaymentFee::findOne(['service_bill_invoice_id' => $this->id]);
                if(!empty($servicePaymentFee)){
                    $servicePaymentFee->service_bill_invoice_id = null;
                    if(!$servicePaymentFee->save()){
                        return false;
                    }
                }
            }
            return true;
        }

        $serviceBillItems = ServiceBillItem::find()->where(['service_bill_id' => $this->id])->all();
        foreach ($serviceBillItems as $serviceBillItem){
            $servicePaymentFee = ServicePaymentFee::findOne(['id' => $serviceBillItem->service_payment_fee_id]);
            $servicePaymentFee->money_collected = $servicePaymentFee->money_collected - $serviceBillItem->price;
            if($servicePaymentFee->money_collected < 0){
                Yii::error('Sai giá thu');
                return false;
            }
            if(!empty($servicePaymentFee->service_bill_ids)){
                $ids = json_decode($servicePaymentFee->service_bill_ids, true);
                $ids = array_diff( $ids, [$this->id] );
                $ids_new = [];
                foreach ($ids as $id){
                    $ids_new[] = $id;
                }
                $servicePaymentFee->service_bill_ids = json_encode($ids_new);
            }
            if(!empty($servicePaymentFee->service_bill_codes)){
                $codes = json_decode($servicePaymentFee->service_bill_codes, true);
                $codes = array_diff( $codes, [$this->code] );
                $codes_new = [];
                foreach ($codes as $code){
                    $codes_new[] = $code;
                }
                $servicePaymentFee->service_bill_codes = json_encode($codes_new);
            }
            $servicePaymentFee->status = ServicePaymentFee::STATUS_UNPAID;
            if(!$servicePaymentFee->save()){
                Yii::error($servicePaymentFee->errors);
                return false;
            }
            if($is_del == true){
                if(!$serviceBillItem->delete()){
                    Yii::error($serviceBillItem->errors);
                    return false;
                }
            }
        }
        return true;
    }

    /*
     *thông báo về tài chính
     */
    public function sendNotifyToManagementUser()
    {
        try {
            // nếu tồn tài config không cho gửi thì sẽ không gửi
//            $notifySendConfig = NotifySendConfig::findOne(['building_cluster_id' => $this->buildingCluster->id, 'type' => NotifySendConfig::TYPE_FEE, 'send_notify_app' => NotifySendConfig::NOT_SEND]);
//            if(!empty($notifySendConfig)){
//                return false;
//            }

            $url = $this->buildingCluster->domain . '/main/finance/bills/detail/' . $this->id;
            $app_id = $this->buildingCluster->one_signal_app_id;
            if (empty($this->buildingCluster->setting_group_receives_notices_financial)) {
                return false;
            }
            $title = $title_en = NotificationTemplate::vsprintf(NotificationTemplate::APARTMENT_CREATE_BILL_TO_MANAGEMENT, [$this->code, CUtils::formatPrice($this->total_price) . ' đ']);
            $description = $description_en = $this->payer_name . " (" . $this->apartment->name . " - " . trim($this->apartment->parent_path, '/') . "): Tạo phiếu thanh toán với số tiền là " . CUtils::formatPrice($this->total_price) . ' đ, mã phiếu ' . $this->code;
            $data = [
                'type' => 'payment',
                'action' => "pay",
                'order_id' => $this->id,
                'order_code' => $this->code
            ];
            //gửi thông báo cho các user thuộc nhóm quyền này biết có yêu cầu được cập nhật
            $oneSignalApi = new OneSignalApi();
            $mapAuthGroupsIds = json_decode($this->buildingCluster->setting_group_receives_notices_financial, true);
            $managementUserIds = [];

            $typeNotify = ManagementUserNotify::TYPE_APARTMENT_CREATE_BILL;
            $request_answer_id = null;
            $request_answer_internal_id = null;

            $managementUsers = ManagementUser::find()->where(['auth_group_id' => $mapAuthGroupsIds, 'building_cluster_id' => $this->buildingCluster->id, 'is_deleted' => ManagementUser::NOT_DELETED, 'status' => ManagementUser::STATUS_ACTIVE])->all();
            foreach ($managementUsers as $managementUser) {
                //kiểm tra xem user này có cấu hình nhận thông báo tạo phí hay không không
//                $checkReceive = $managementUser->checkNotifyReceiveConfig(ManagementNotifyReceiveConfig::CHANNEL_NOTIFY_APP, ManagementNotifyReceiveConfig::TYPE_FEE, [ManagementNotifyReceiveConfig::ACTION_KEY_CREATE => ManagementNotifyReceiveConfig::NOT_RECEIVED]);
//                if(!empty($checkReceive)){
//                    continue;
//                }

                $managementUserIds[] = $managementUser->id;

                //khởi tạo log cho từng management user
                $managementUserNotify = new ManagementUserNotify();
                $managementUserNotify->building_cluster_id = $this->building_cluster_id;
                $managementUserNotify->building_area_id = $this->building_area_id;
                $managementUserNotify->management_user_id = $managementUser->id;
                $managementUserNotify->type = $typeNotify;
                $managementUserNotify->service_bill_id = $this->id;
                $managementUserNotify->request_answer_id = $request_answer_id;
                $managementUserNotify->request_answer_internal_id = $request_answer_internal_id;
                $managementUserNotify->title = $title;
                $managementUserNotify->description = $description;
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
}
