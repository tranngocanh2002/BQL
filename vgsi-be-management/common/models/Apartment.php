<?php

namespace common\models;

use common\helpers\CVietnameseTools;
use common\helpers\StringUtils;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "apartment".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $medias
 * @property string $code
 * @property string $parent_path
 * @property string $handover
 * @property int $status  0 - chưa o, 1 - đã o
 * @property int $is_deleted 0 : chưa xóa, 1 : đã xóa
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $resident_user_id chủ hộ
 * @property string $resident_user_name chủ hộ
 * @property string $resident_name_search
 * @property float $capacity diện tich : m2
 * @property int $date_received
 * @property int $date_delivery
 * @property int $total_members
 * @property string $short_name
 * @property int $reminder_debt
 * @property int $set_water_level 0: chưa khai báo định mức nước, 1: đã khai báo định mức nước
 * @property int $form_type
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property BuildingArea $buildingArea
 * @property BuildingCluster $buildingCluster
 * @property ApartmentFormType $apartmentFormType
 * @property ResidentUser $residentUser
 * @property ApartmentMapResidentUser $apartmentMapResidentUser
 * @property ApartmentMapResidentUserReceiveNotifyFee[] $apartmentMapResidentUserReceiveNotifyFees
 */
class Apartment extends \yii\db\ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    const STATUS_EMPTY = 0;
    const STATUS_LIVE = 1;

    public static $status_list = [
        self::STATUS_EMPTY => "Chưa ở",
        self::STATUS_LIVE => "Đã ở"
    ];

    const STATUS_HANDE = 0;
    const STATUS_HANDED = 1;

    public static $handed_list = [
        self::STATUS_HANDE => "Chưa bàn giao",
        self::STATUS_HANDED => "Đã bàn giao"
    ];

    public static $handed_list_text = [
        "Chưa bàn giao" => self::STATUS_HANDE,
        "Đã bàn giao" => self::STATUS_HANDED
    ];

    const REMINDER_DEBT_PREPAID = -1; // Trả trước
    const REMINDER_DEBT_PAID = 0; // không nợ
    const REMINDER_DEBT_UNPAID = 1; // có nợ
    const REMINDER_DEBT_UNPAID_1 = 2; // thông báo phí
    const REMINDER_DEBT_UNPAID_2 = 3; // nhắc nợ lần 1
    const REMINDER_DEBT_UNPAID_3 = 4; // nhắc nợ lần 2
    const REMINDER_DEBT_UNPAID_4 = 5; // nhắc nợ lần 3
    const REMINDER_DEBT_UNPAID_5 = 6; // thông báo dừng dịch vụ

    const FORM_TYPE_0 = 0;
    const FORM_TYPE_1 = 1;
    const FORM_TYPE_2 = 2;
    const FORM_TYPE_3 = 3;
    const FORM_TYPE_4 = 4;
//    const FORM_TYPE_5 = 5;
//    const FORM_TYPE_6 = 6;
//    const FORM_TYPE_7 = 7;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apartment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'capacity', 'form_type'], 'required', 'message' => Yii::t('common', '{attribute} không được để trống')],
            [['description', 'medias', 'code', 'parent_path', 'resident_user_name', 'handover', 'resident_name_search'], 'string'],
            [['form_type', 'reminder_debt', 'set_water_level', 'date_received', 'date_delivery', 'status', 'is_deleted', 'building_cluster_id', 'building_area_id', 'resident_user_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 10, 'message' => Yii::t('common', 'Tên bất động sản chỉ cho phép nhập tối đa 10 ký tự')],
            [['short_name'], 'string', 'max' => 20],
            [['total_members'], 'integer', 'max' => 9999, 'message' => Yii::t('common', 'Số thành viên cho phép tối đa 5 ký tự')],
            [['capacity'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Tên bất động sản'),
            'code' => Yii::t('common', 'Code'),
            'parent_path' => Yii::t('common', 'Parent Path'),
            'description' => Yii::t('common', 'Description'),
            'medias' => Yii::t('common', 'Medias'),
            'status' => Yii::t('common', 'Status'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'resident_user_name' => Yii::t('common', 'Resident User Name'),
            'resident_name_search' => Yii::t('common', 'Resident Name Search'),
            'handover' => Yii::t('common', 'Handover'),
            'capacity' => Yii::t('common', 'Capacity'),
            'date_received' => Yii::t('common', 'Date Received'),
            'date_delivery' => Yii::t('common', 'Date Delivery'),
            'total_members' => Yii::t('common', 'Total Members'),
            'short_name' => Yii::t('common', 'Short Name'),
            'reminder_debt' => Yii::t('common', 'Reminder Debt'),
            'set_water_level' => Yii::t('common', 'Set Water Level'),
            'form_type' => Yii::t('common', 'Form Type'),
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

    private function generateCodeNew($code, $length, $building_cluster_id = null)
    {
        if (empty($building_cluster_id)) {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            if (!empty($buildingCluster)) {
                $building_cluster_id = $buildingCluster->id;
            }
        }
        if (empty($building_cluster_id)) {
            return null;
        }
        $apartment = Apartment::findOne(['code' => $code, 'building_cluster_id' => $building_cluster_id, 'is_deleted' => Apartment::NOT_DELETED]);
        if (!empty($apartment)) {
            $code_new = StringUtils::randomStr($length);
            return self::generateCodeNew($code_new, $length);
        }
        return $code;
    }

    /**
     * Generates new code
     */
    public function generateCode($building_cluster_id = null)
    {
        $this->code = strtoupper(self::generateCodeNew(StringUtils::randomStr(6), 6, $building_cluster_id));
    }

    /**
     * setParentPath
     */
    public function setParentPath()
    {
        $this->parent_path = $this->buildingArea->parent_path . $this->buildingArea->name . '/';
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $name_search = CVietnameseTools::removeSigns2($this->resident_user_name);
        $this->resident_name_search = CVietnameseTools::toLower($name_search);
        return true;
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //update thông tin vào bảng map resident
        ApartmentMapResidentUser::updateAll(
            [
                'apartment_name' => $this->name,
                'apartment_code' => $this->code,
                'apartment_parent_path' => $this->parent_path,
                'apartment_capacity' => $this->capacity,
                'apartment_short_name' => $this->short_name,
                'building_cluster_id' => $this->building_cluster_id,
                'building_area_id' => $this->building_area_id
            ],
            [
                'apartment_id' => $this->id,
                'is_deleted' => ApartmentMapResidentUser::NOT_DELETED
            ]
        );

        //update lại building cluster cho tất cả các bảng liên quan
        AnnouncementItem::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        AnnouncementItemSend::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceBill::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceBuildingInfo::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceBuildingFee::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceDebt::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceElectricInfo::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceElectricFee::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceManagementVehicle::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceParkingFee::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServicePaymentFee::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceOldDebitFee::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceUtilityBooking::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceWaterInfo::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
        ServiceWaterFee::updateAll(['building_area_id' => $this->building_area_id], ['apartment_id' => $this->id]);
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
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartmentFormType()
    {
        return $this->hasOne(ApartmentFormType::className(), ['id' => 'form_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResidentUser()
    {
        return $this->hasOne(ResidentUser::className(), ['id' => 'resident_user_id']);
    }

    //nọ đầu kỳ
    public function getEarlyDebt($fee_of_month){
        //check công nợ tháng trước đó
        $pre_fee_of_month =  strtotime('-1 month', $fee_of_month);
        $serviceDebt = ServiceDebt::findOne(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->id, 'month' => $pre_fee_of_month]);
        if(!empty($serviceDebt)){
            return $serviceDebt->end_debt;
        }

        //Tổng phí của những tháng trước chưa thanh toán
        $res_early_debt = ServicePaymentFee::find()
            ->select(["SUM(more_money_collecte) as more_money_collecte"])
            ->where(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_debt' => ServicePaymentFee::IS_DEBT, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])
            ->andWhere(['<', 'fee_of_month', $fee_of_month])
            ->groupBy(['apartment_id'])->one();
        $early_debt = 0;
        if (!empty($res_early_debt)) {
            $early_debt += (int)$res_early_debt->more_money_collecte;
        }

        //Tổng phí của tháng trước nhưng đã thanh toán ở tháng sau
        $servicePaymentFees = ServicePaymentFee::find()
            ->where(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->id, 'status' => ServicePaymentFee::STATUS_PAID, 'is_debt' => ServicePaymentFee::IS_DEBT, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])
            ->andWhere(['<', 'fee_of_month', $fee_of_month])
            ->andWhere(['>=', 'updated_at', $fee_of_month])->all();
        $feeIds = [];
        $billIds = [];
        foreach ($servicePaymentFees as $servicePaymentFee){
            $feeIds[] = $servicePaymentFee->id;
            if(!empty($servicePaymentFee->service_bill_ids)){
                $billIds = array_merge($billIds, json_decode($servicePaymentFee->service_bill_ids, true));
            };
        }
        $billIds = array_values($billIds);
        if(!empty($billIds)){
            $serviceBills = ServiceBill::find()->where(['building_cluster_id' => $this->building_cluster_id, 'id' => $billIds, 'is_deleted' => ServiceBill::NOT_DELETED, 'status' => [ServiceBill::STATUS_PAID, ServiceBill::STATUS_BLOCK], 'type' => ServiceBill::TYPE_0])
                ->andWhere(['>=', 'payment_date', $fee_of_month])->all();
            foreach ($serviceBills as $serviceBill){
                $serviceBillItems = ServiceBillItem::find()->where(['service_bill_id' => $serviceBill->id, 'service_payment_fee_id' => $feeIds])->all();
                foreach ($serviceBillItems as $serviceBillItem){
                    $early_debt += $serviceBillItem->price;
                }
            }
        }

        //Tổng phí của tháng sau nhưng đã thanh toán ở tháng trước => trường hợp nợ đầu kỳ có thể âm
        $servicePaymentFeePrepays = ServicePaymentFee::find()
            ->where(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->id, 'status' => [ServicePaymentFee::STATUS_UNPAID, ServicePaymentFee::STATUS_PAID], 'is_debt' => ServicePaymentFee::IS_DEBT, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])
            ->andWhere(['>=', 'fee_of_month', strtotime('+1 month',$fee_of_month)])->all();
        $feePrepayIds = [];
        $billPrepayIds = [];
        $prepay = 0;
        foreach ($servicePaymentFeePrepays as $servicePaymentFeePrepay){
            $feePrepayIds[] = $servicePaymentFeePrepay->id;
            if(!empty($servicePaymentFeePrepay->service_bill_ids)){
                $billPrepayIds = array_merge($billPrepayIds, json_decode($servicePaymentFeePrepay->service_bill_ids, true));
            };
        }
        $billPrepayIds = array_values($billPrepayIds);
        if(!empty($billPrepayIds)){
            $servicePrepayBills = ServiceBill::find()->where(['building_cluster_id' => $this->building_cluster_id, 'id' => $billPrepayIds, 'is_deleted' => ServiceBill::NOT_DELETED, 'status' => [ServiceBill::STATUS_PAID, ServiceBill::STATUS_BLOCK], 'type' => ServiceBill::TYPE_0])
                ->andWhere(['<', 'payment_date', $fee_of_month])->all();
            foreach ($servicePrepayBills as $servicePrepayBill){
                $serviceBillPrepayItems = ServiceBillItem::find()->where(['service_bill_id' => $servicePrepayBill->id, 'service_payment_fee_id' => $feePrepayIds])->all();
                foreach ($serviceBillPrepayItems as $serviceBillPrepayItem){
                    $prepay += $serviceBillPrepayItem->price;
                }
            }
        }

        //nợ đầu kỳ sẽ bằng tổng nợ trừ trả trước
        return $early_debt - $prepay;
    }


    //nợ phải thu trong tháng
    public function getReceivablesDebt($start_old_month, $end_old_month){
        $res_receivables = ServicePaymentFee::find()
            ->select(["SUM(price) as price"])
            ->where(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->id, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])
            ->andWhere(['is_debt' => ServicePaymentFee::IS_DEBT])
            ->andWhere(['>=', 'fee_of_month', $start_old_month])
            ->andWhere(['<=', 'fee_of_month', $end_old_month])->one();

        $receivables = 0;
        if (!empty($res_receivables)) {
            $receivables = (int)$res_receivables->price;
        }
        return $receivables;
    }

    //nợ đã thu trong tháng
    public function getCollectedDebt($start_old_month, $end_old_month){
        $res_collected = ServiceBill::find()
            ->select(["SUM(total_price) as total_price"])
            ->where(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->id, 'status' => [ServiceBill::STATUS_PAID, ServiceBill::STATUS_BLOCK], 'type' => ServiceBill::TYPE_0])
            ->andWhere(['>=', 'payment_date', $start_old_month])
            ->andWhere(['<=', 'payment_date', $end_old_month])->one();

        $collected = 0;
        if (!empty($res_collected)) {
            $collected = (int)$res_collected->total_price;
        }
        return $collected;
    }

    //nợ hiện tại chưa thanh toán
    public function getCurrentDebt(){
        $res_collecte = ServicePaymentFee::find()
            ->select(["SUM(more_money_collecte) as more_money_collecte"])
            ->where(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_debt' => ServicePaymentFee::IS_DEBT, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])
            ->andWhere(['is_debt' => ServicePaymentFee::IS_DEBT])->one();

        $collecte = 0;
        if (!empty($res_collecte)) {
            $collecte = (int)$res_collecte->more_money_collecte;
        }
        return $collecte;
    }

    //update trạng thái nợ
    public function changeReminderDebt(){
        $collecte = self::getCurrentDebt();
        $is_update = 0;
        //xóa trạng thái nhắc nợ đầu tháng
        if(time() <= strtotime(date('Y-m-02 23:59:59', time()))){
            $this->reminder_debt = self::REMINDER_DEBT_PAID;
            $is_update = 1;
        }
        //check trạng thái nợ
        if($collecte == 0 && ($this->reminder_debt > self::REMINDER_DEBT_PAID)){
            $this->reminder_debt = self::REMINDER_DEBT_PAID;
            $is_update = 1;
        }else if($collecte > 0 && ($this->reminder_debt <= self::REMINDER_DEBT_PAID)){
            $this->reminder_debt = self::REMINDER_DEBT_UNPAID;
            $is_update = 1;
        }
        if($is_update == 1){
            if(!$this->save()){
                Yii::error($this->errors);
                return false;
            }
        }
        return true;
    }

    /*
     * cập nhật lại công nợ hiện tại của căn hộ
     * nếu có thay đổi về payment fee sẽ gọi lại hàm này
     */
    public function updateCurrentDebt($time = null, $type = ServiceDebt::TYPE_CURRENT_MONTH){
        if($type == ServiceDebt::TYPE_CURRENT_MONTH){
            if(!self::changeReminderDebt()){
                return false;
            }
        }

        $time_current = time();
        if(!empty($time)){
            $time_current = $time;
        }
        $current_month = strtotime(date('Y-m-01', $time_current));
        $start_old_month = strtotime(date('Y-m-01 00:00:00', $current_month));
        $d = new \DateTime(date('Y-m-01 00:00:00', $current_month));
        $end_old_month = strtotime($d->format( 'Y-m-t 23:59:59' ));

        $serviceDebt = ServiceDebt::findOne(['apartment_id' => $this->id, 'type' => $type, 'month' => $current_month]);
        if(empty($serviceDebt)){
            $serviceDebt = new ServiceDebt();
            $serviceDebt->building_cluster_id = $this->building_cluster_id;
            $serviceDebt->building_area_id = $this->building_area_id;
            $serviceDebt->apartment_id = $this->id;
            $serviceDebt->type = $type;
            $serviceDebt->month = $current_month;
            $serviceDebt->early_debt = $this->getEarlyDebt($current_month);
        }
        //Phải thu
        $serviceDebt->receivables = $this->getReceivablesDebt($start_old_month, $end_old_month);

        //Đã thu
        $serviceDebt->collected = $this->getCollectedDebt($start_old_month, $end_old_month);

        //Nợ cuối kỳ
        $serviceDebt->end_debt = $serviceDebt->early_debt + $serviceDebt->receivables - $serviceDebt->collected;

        $serviceDebt->status = Apartment::REMINDER_DEBT_PAID;
        if ($serviceDebt->end_debt > 0) {
            $serviceDebt->status = Apartment::REMINDER_DEBT_UNPAID;
            if ($this->reminder_debt > 0) {
                $serviceDebt->status = $this->reminder_debt;
            }
        }else if($serviceDebt->end_debt < 0){
            $this->reminder_debt = ServiceDebt::STATUS_PREPAID;
            $serviceDebt->status = ServiceDebt::STATUS_PREPAID;
            if (!$this->save()) {
                Yii::error($this->errors);
                return false;
            }
        }

        if (!$serviceDebt->save()) {
            Yii::error($serviceDebt->errors);
            return false;
        }

        if($current_month < strtotime(date('Y-m-01', time()))){
            Yii::warning('1');
            $time = strtotime('+1 month', $current_month);
            if($time == strtotime(date('Y-m-01', time()))){
                Yii::warning('2');
                self::updateCurrentDebt();
            }else{
                Yii::warning('3');
                self::updateCurrentDebt($time, $type);
            }
        }

        return true;
    }

    public function countBookByMonth($service_utility_free_id){
        $current_start = strtotime(date('Y-m-01 00:00:00', time()));
        $current_end = strtotime('+1 month', $current_start);
        $serviceUtilityBookingCount = ServiceUtilityBooking::find()
            ->where(['>=', 'status', ServiceUtilityBooking::STATUS_CREATE])
            ->andWhere(['apartment_id' => $this->id, 'service_utility_free_id' => $service_utility_free_id])
            ->andWhere(['>=', 'created_at', $current_start])
            ->andWhere(['<', 'created_at', $current_end])
            ->count();
        return (int)$serviceUtilityBookingCount;
    }

    public function countBookByDay($service_utility_config_id,$book_time = null){
        $serviceUtilityBookingCount = 0;
        // $jsonString = json_encode($book_time);
        // foreach($book_time as $item)
        //     {
        //         $serviceUtilityBookingCount += ServiceUtilityBooking::find()
        //         ->where(['=', 'status', ServiceUtilityBooking::STATUS_ACTIVE])
        //         ->andWhere(['service_utility_free_id' => $service_utility_free_id])
        //         ->andWhere(['=', 'book_time', $jsonString])
        //         // ->andWhere(['>=', 'start_time', $item['start_time']])
        //         // ->andWhere(['<=', 'end_time', $item['end_time']])
        //         ->count();
        //     }    
        $bookTimes = ServiceUtilityBooking::find()
        ->select('book_time')
        ->where(['=', 'status', ServiceUtilityBooking::STATUS_ACTIVE])
        ->andWhere(['service_utility_config_id' => $service_utility_config_id])
        ->all();
        foreach ($bookTimes as $booking) {
            $jsonTime = json_decode($booking->book_time, true);
            foreach ($jsonTime as $jsonTimes) {
                foreach ($book_time as $book_times) {
                    if ($jsonTimes['start_time'] == $book_times['start_time'] && $jsonTimes['end_time'] == $book_times['end_time']) {
                        $serviceUtilityBookingCount++;
                    }
                }
            }
        }
        return (int)$serviceUtilityBookingCount;
    }

    public function getFormTypeList(){
        return Yii::$app->params['Apartment_form_type_list'];
    }

    public function getFormTypeEnList(){
        return Yii::$app->params['Apartment_form_type_en_list'];
    }

    public function getFormTypeNameList(){
        return Yii::$app->params['Apartment_form_type_name_list'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartmentMapResidentUserReceiveNotifyFees()
    {
        return $this->hasMany(ApartmentMapResidentUserReceiveNotifyFee::className(), ['apartment_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartmentMapResidentUser()
    {
        return $this->hasMany(ApartmentMapResidentUser::className(), ['apartment_id' => 'id','resident_user_id' => 'resident_user_id']);
    }
}
