<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityConfig;
use common\models\ServiceUtilityFree;
use common\models\ServiceWaterFee;
use common\models\ServiceUtilityBooking;
use common\models\ServiceParkingLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityBookingForm")
 * )
 */
class ServiceUtilityBookingForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete, cancel", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="apartment id", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="service utility config id", default=1, type="integer")
     * @var integer
     */
    public $service_utility_config_id;

    /**
     * @SWG\Property(property="book_time", type="array",
     *     @SWG\Items(
     *          @SWG\Property(property="start_time", type="integer"),
     *          @SWG\Property(property="end_time", type="integer"),
     *      )
     * ),
     * @var array
     */
    public $book_time;

    /**
     * @SWG\Property(description="total_adult: số người lớn", default=0, type="integer")
     * @var integer
     */
    public $total_adult;

    /**
     * @SWG\Property(description="total_child: số trẻ nhỏ", default=0, type="integer")
     * @var integer
     */
    public $total_child;

    /**
     * @SWG\Property(description="description", default="", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="reason: lý do hủy", default="", type="string")
     * @var string
     */
    public $reason;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'apartment_id'], 'required', "on" => ['update', 'delete', 'cancel']],
            [['apartment_id', 'service_utility_config_id', 'book_time'], 'required', "on" => ['create']],
            [['service_utility_config_id', 'book_time', 'apartment_id'], 'required', "on" => ['checkSlot']],
            [['service_utility_config_id', 'apartment_id'], 'required', "on" => ['checkTime']],
            [['service_utility_config_id', 'book_time', 'total_adult', 'total_child', 'apartment_id'], 'required', "on" => ['checkPrice']],
            [['id', 'apartment_id', 'service_utility_config_id', 'total_adult', 'total_child'], 'integer'],
            [['description', 'reason'], 'string'],
            [['book_time'], 'safe'],
        ];
    }

    public function create()
    {
        $user = Yii::$app->user->getIdentity();

        $apartment = Apartment::findOne(['id' => $this->apartment_id, 'is_deleted' => Apartment::NOT_DELETED, 'status' => Apartment::STATUS_LIVE]);
        if (empty($apartment)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Căn hộ chưa có chủ hộ, không được đặt chỗ"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            Yii::error('apartmentMapResidentUser empty');
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        $serviceUtilityConfig = ServiceUtilityConfig::findOne(['id' => $this->service_utility_config_id, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id]);
        if (empty($serviceUtilityConfig)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid Service Utility Config"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if(empty($serviceUtilityConfig->serviceUtilityFree) || empty($serviceUtilityConfig->serviceUtilityFree->serviceMapManagement) || ($serviceUtilityConfig->serviceUtilityFree->serviceMapManagement->status == ServiceMapManagement::STATUS_INACTIVE)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Dịch vụ đang ngừng cung cấp")
            ];
        }
        if($apartmentMapResidentUser->apartment->countBookByMonth($serviceUtilityConfig->service_utility_free_id) >= $serviceUtilityConfig->serviceUtilityFree->limit_book_apartment){
            return [
                'success' => false,
                'message' => Yii::t('resident', "limit book apartment"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if($apartmentMapResidentUser->apartment->countBookByDay($serviceUtilityConfig->id,$this->book_time) >= 1){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Khung giờ bạn chọn đã hết chỗ trống"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $ServiceUtilityBooking = new ServiceUtilityBooking();
        $ServiceUtilityBooking->load(CUtils::arrLoad($this->attributes), '');
        $ServiceUtilityBooking->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
        $ServiceUtilityBooking->building_area_id = $apartmentMapResidentUser->building_area_id;
        $ServiceUtilityBooking->service_utility_free_id = $serviceUtilityConfig->service_utility_free_id;
        $ServiceUtilityBooking->service_map_management_id = $serviceUtilityConfig->serviceUtilityFree->service_map_management_id;
        if (isset($this->book_time) && is_array($this->book_time)) {
            $ServiceUtilityBooking->book_time = !empty($this->book_time) ? json_encode($this->book_time) : null;
        }
        $ServiceUtilityBooking->setStartEndTime();
        $ServiceUtilityBooking->setTotalSlot();
        $ServiceUtilityBooking->setPrice();
        $ServiceUtilityBooking->setJsonDesc();

        //check thời gian đặt chỗ
        $checkTimeBook = $serviceUtilityConfig->checkTimeBook($ServiceUtilityBooking->book_time);
        if (empty($checkTimeBook)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid Start Time Or End Time"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        //check chỗ trống
        $slot_null = $serviceUtilityConfig->getSlotNull($ServiceUtilityBooking->start_time, $ServiceUtilityBooking->end_time);
        if (($slot_null - $ServiceUtilityBooking->total_slot) < 0) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Not Enough Slot"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        if (!$ServiceUtilityBooking->save()) {
            Yii::error($ServiceUtilityBooking->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceUtilityBooking->getErrors()
            ];
        } else {
            $fee_of_month = time();
            if ($ServiceUtilityBooking->price > 0) { // nếu book có giá lớn hơn 0 mới tạo phí
                $ServicePaymentFee = new ServicePaymentFee();
                $ServicePaymentFee->service_map_management_id = $ServiceUtilityBooking->service_map_management_id;
                $ServicePaymentFee->building_cluster_id = $ServiceUtilityBooking->building_cluster_id;
                $ServicePaymentFee->building_area_id = $ServiceUtilityBooking->building_area_id;
                $ServicePaymentFee->apartment_id = $ServiceUtilityBooking->apartment_id;
//                $ServicePaymentFee->description = $ServiceUtilityBooking->description;
//                $ServicePaymentFee->json_desc = $ServiceUtilityBooking->json_desc;
                $ServicePaymentFee->price = $ServiceUtilityBooking->price;
                $ServicePaymentFee->service_utility_booking_id = $ServiceUtilityBooking->id;
                $ServicePaymentFee->is_draft = ServicePaymentFee::IS_NOT_DRAFT;
                $ServicePaymentFee->is_debt = ServicePaymentFee::IS_NOT_DEBT;
                $ServicePaymentFee->fee_of_month = $fee_of_month;
                $ServicePaymentFee->day_expired = $ServiceUtilityBooking->end_time;
                $ServicePaymentFee->type = ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE;
                $ServicePaymentFee->start_time = $ServiceUtilityBooking->start_time;
                $ServicePaymentFee->end_time = $ServiceUtilityBooking->end_time;
                if (!$ServicePaymentFee->save()) {
                    Yii::error($ServicePaymentFee->errors);
                }else{
                    $ServiceUtilityBooking->is_created_fee = ServiceUtilityBooking::IS_CREATED_FEE;
                    $ServiceUtilityBooking->service_payment_fee_id = $ServicePaymentFee->id;
                    $ServiceUtilityBooking->fee_of_month = $fee_of_month;
                    if(!$ServiceUtilityBooking->save()){
                        Yii::error($ServiceUtilityBooking->errors);
                    }
                }
            }
            if($serviceUtilityConfig->serviceUtilityFree->deposit_money > 0){
                $ServicePaymentFee = new ServicePaymentFee();
                $ServicePaymentFee->service_map_management_id = $ServiceUtilityBooking->service_map_management_id;
                $ServicePaymentFee->building_cluster_id = $ServiceUtilityBooking->building_cluster_id;
                $ServicePaymentFee->building_area_id = $ServiceUtilityBooking->building_area_id;
                $ServicePaymentFee->apartment_id = $ServiceUtilityBooking->apartment_id;
//                $ServicePaymentFee->description = $ServiceUtilityBooking->description;
//                $ServicePaymentFee->json_desc = $ServiceUtilityBooking->json_desc;
                $ServicePaymentFee->price = $serviceUtilityConfig->serviceUtilityFree->deposit_money;
                $ServicePaymentFee->service_utility_booking_id = $ServiceUtilityBooking->id;
                $ServicePaymentFee->for_type = ServicePaymentFee::FOR_TYPE_1;
                $ServicePaymentFee->is_draft = ServicePaymentFee::IS_NOT_DRAFT;
                $ServicePaymentFee->is_debt = ServicePaymentFee::IS_NOT_DEBT;
                $ServicePaymentFee->fee_of_month = $fee_of_month;
                $ServicePaymentFee->day_expired = $ServiceUtilityBooking->end_time;
                $ServicePaymentFee->type = ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE;
                $ServicePaymentFee->start_time = $ServiceUtilityBooking->start_time;
                $ServicePaymentFee->end_time = $ServiceUtilityBooking->end_time;
                if (!$ServicePaymentFee->save()) {
                    Yii::error($ServicePaymentFee->errors);
                }else{
                    $ServiceUtilityBooking->is_created_fee = ServiceUtilityBooking::IS_CREATED_FEE;
                    $ServiceUtilityBooking->service_payment_fee_deposit_ids = Json::encode([$ServicePaymentFee->id]);
                    $ServiceUtilityBooking->fee_of_month = $fee_of_month;
                    $ServiceUtilityBooking->total_deposit_money += $ServicePaymentFee->price;
                    if(!$ServiceUtilityBooking->save()){
                        Yii::error($ServiceUtilityBooking->errors);
                    }
                }
            }
            $ServiceUtilityBooking->sendNotifyToManagementUser(null, $user, ServiceUtilityBooking::CREATE);
            return ServiceUtilityBookingResponse::findOne(['id' => $ServiceUtilityBooking->id]);
        }
    }

    public function update()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            Yii::error('apartmentMapResidentUser empty');
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        $ServiceUtilityBooking = ServiceUtilityBookingResponse::findOne(['id' => (int)$this->id, 'apartment_id' => $apartmentMapResidentUser->apartment_id]);
        if ($ServiceUtilityBooking) {
            if (!in_array($ServiceUtilityBooking->status, [ServiceUtilityBooking::STATUS_CREATE])) {
                Yii::error('ServiceUtilityBooking status invalid');
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $ServiceUtilityBooking->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->book_time) && is_array($this->book_time)) {
                $ServiceUtilityBooking->book_time = !empty($this->book_time) ? json_encode($this->book_time) : null;
            }
            $ServiceUtilityBooking->setStartEndTime();
            $ServiceUtilityBooking->setTotalSlot();
            $ServiceUtilityBooking->setPrice();
            $ServiceUtilityBooking->setJsonDesc();

            $serviceUtilityConfig = $ServiceUtilityBooking->serviceUtilityConfig;

            //check thời gian đặt chỗ
            $checkTimeBook = $serviceUtilityConfig->checkTimeBook($ServiceUtilityBooking->book_time);
            if (empty($checkTimeBook)) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid Start Time Or End Time"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            //check chỗ trống
            $slot_null = $serviceUtilityConfig->getSlotNull($ServiceUtilityBooking->start_time, $ServiceUtilityBooking->end_time, $ServiceUtilityBooking->id);
            if (($slot_null - $ServiceUtilityBooking->total_slot) < 0) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Not Enough Slot"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if (!$ServiceUtilityBooking->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceUtilityBooking->getErrors()
                ];
            } else {
                $ServicePaymentFee = ServicePaymentFee::findOne($ServiceUtilityBooking->service_payment_fee_id);
                if(!empty($ServicePaymentFee)){
                    $ServicePaymentFee->description = $ServiceUtilityBooking->description;
                    $ServicePaymentFee->json_desc = $ServiceUtilityBooking->json_desc;
                    $ServicePaymentFee->price = $ServiceUtilityBooking->price;
                    $ServicePaymentFee->start_time = $ServiceUtilityBooking->start_time;
                    $ServicePaymentFee->end_time = $ServiceUtilityBooking->end_time;
                    if (!$ServicePaymentFee->save()) {
                        Yii::error($ServicePaymentFee->errors);
                    }
                }
                return $ServiceUtilityBooking;
            }
        } else {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function delete()
    {
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            Yii::error('apartmentMapResidentUser empty');
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        $ServiceUtilityBooking = ServiceUtilityBooking::findOne(['id' => $this->id, 'apartment_id' => $apartmentMapResidentUser->apartment_id, 'created_by' => $user->id]);
        if (!in_array($ServiceUtilityBooking->status, [ServiceUtilityBooking::STATUS_CANCEL, ServiceUtilityBooking::STATUS_CREATE])) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Delete error"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $servicePaymentFee = ServicePaymentFee::findOne($ServiceUtilityBooking->service_payment_fee_id);
        if(!empty($servicePaymentFee)){
            if($servicePaymentFee->is_debt != ServicePaymentFee::IS_NOT_DEBT){
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                ];
            }
            if(!$servicePaymentFee->delete()){
                Yii::error($servicePaymentFee->errors);
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                ];
            }
        }
        if ($ServiceUtilityBooking->delete()) {
            return [
                'success' => true,
                'message' => Yii::t('resident', "Delete Success")
            ];
        } else {
            Yii::error($ServiceUtilityBooking->errors);
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function cancel()
    {
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            Yii::error('apartmentMapResidentUser empty');
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        $ServiceUtilityBooking = ServiceUtilityBooking::findOne(['id' => $this->id, 'apartment_id' => $apartmentMapResidentUser->apartment_id, 'created_by' => $user->id]);
        if (!in_array($ServiceUtilityBooking->status, [ServiceUtilityBooking::STATUS_CREATE, ServiceUtilityBooking::STATUS_ACTIVE])) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Cancel error"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        if($ServiceUtilityBooking->timeoutCancelBook()){
            return [
                'success' => false,
                'message' => Yii::t('resident', "timeout cancel book"),
            ];
        }

        if ($ServiceUtilityBooking->cancelBook(ServiceUtilityBooking::STATUS_CANCEL, $this->reason)) {
            $ServiceUtilityBooking->sendNotifyToManagementUser(null, $user, ServiceUtilityBooking::UPDATE_STATUS);
            return [
                'success' => true,
                'message' => Yii::t('resident', "Delete Success")
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function checkSlot()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            Yii::error('apartmentMapResidentUser empty');
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $serviceUtilityConfig = ServiceUtilityConfig::findOne(['id' => $this->service_utility_config_id, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id]);
        if (empty($serviceUtilityConfig)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        //check thời gian đặt chỗ
        if ($this->start_time < $serviceUtilityConfig->start_time || $this->end_time > $serviceUtilityConfig->end_time) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid Start Time Or End Time"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        //check chỗ trống
        $slot_null = $serviceUtilityConfig->getSlotNull($this->start_time, $this->end_time);
        return [
            'success' => true,
            'message' => Yii::t('resident', "Success"),
            'slot_null' => $slot_null,
        ];
    }

    public function checkTime()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            Yii::error('apartmentMapResidentUser empty');
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $serviceUtilityConfig = ServiceUtilityConfig::findOne(['id' => $this->service_utility_config_id, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id]);
        if (empty($serviceUtilityConfig)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        //check thời gian đặt chỗ
        if ($this->start_time < $serviceUtilityConfig->start_time || $this->end_time > $serviceUtilityConfig->end_time) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid Start Time Or End Time"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        //check thời gian không trống
        // $time_null = $serviceUtilityConfig->getTimeNull($serviceUtilityConfig->service_utility_free_id);
        $time_null = $serviceUtilityConfig->getTimeNull($this->start_time, $this->end_time,$serviceUtilityConfig->id);
        return [
            'success' => true,
            'message' => Yii::t('resident', "Success"),
            'time_null' => $time_null,
        ];
    }

    public function checkPrice()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            Yii::error('apartmentMapResidentUser empty');
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $serviceUtilityConfig = ServiceUtilityConfig::findOne(['id' => $this->service_utility_config_id, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id]);
        if (empty($serviceUtilityConfig)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $price = 0;
        if($serviceUtilityConfig->type == ServiceUtilityConfig::TYPE_NOT_FREE){
            if(!empty($this->book_time)){
                foreach ($this->book_time as $item){
                    $price += $serviceUtilityConfig->getPrice($item['start_time'], $item['end_time'], $this->total_adult, $this->total_child);
                }
            }
        }
        return [
            'success' => true,
            'message' => Yii::t('resident', "Success"),
            'price' => $price,
        ];
    }

}
