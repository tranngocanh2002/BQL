<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityConfig;
use common\models\ServiceUtilityFree;
use common\models\ServiceUtilityPrice;
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
 *   @SWG\Xml(name="ServiceUtilityBookingIncurredFeeForm")
 * )
 */
class ServiceUtilityBookingIncurredFeeForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete, cancel", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="price: số tiền phát sinh", default=0, type="integer")
     * @var integer
     */
    public $price;

    /**
     * @SWG\Property(description="service utility booking id", default=1, type="integer")
     * @var integer
     */
    public $service_utility_booking_id;

    /**
     * @SWG\Property(description="description", default="", type="integer")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="description en", default="", type="integer")
     * @var string
     */
    public $description_en;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'service_utility_booking_id'], 'required', "on" => ['delete']],
            [['price', 'service_utility_booking_id'], 'required', "on" => ['create']],
            [['price', 'service_utility_booking_id'], 'integer'],
            [['description', 'description_en'], 'string'],
        ];
    }

    public function create()
    {
        $user = Yii::$app->user->getIdentity();
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $ServiceUtilityBooking = ServiceUtilityBooking::findOne(['id' => $this->service_utility_booking_id, 'building_cluster_id' => $buildingCluster->id]);
        if (empty($ServiceUtilityBooking) || $this->price <= 0) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid Data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if($ServiceUtilityBooking->status != ServiceUtilityBooking::STATUS_ACTIVE){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Đặt chỗ chưa được duyệt, không thể tạo phí phát sinh"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        //Tạo phí phát sinh
        $fee_of_month = time();
        $ServicePaymentFee = new ServicePaymentFee();
        $ServicePaymentFee->service_map_management_id = $ServiceUtilityBooking->service_map_management_id;
        $ServicePaymentFee->building_cluster_id = $ServiceUtilityBooking->building_cluster_id;
        $ServicePaymentFee->building_area_id = $ServiceUtilityBooking->building_area_id;
        $ServicePaymentFee->approved_by_id = $user->id;
        $ServicePaymentFee->apartment_id = $ServiceUtilityBooking->apartment_id;
        $ServicePaymentFee->description = $this->description;
        $ServicePaymentFee->description_en = $this->description_en;
        $ServicePaymentFee->price = $this->price;
        $ServicePaymentFee->for_type = ServicePaymentFee::FOR_TYPE_2;
        $ServicePaymentFee->is_draft = ServicePaymentFee::IS_NOT_DRAFT;
        $ServicePaymentFee->is_debt = ServicePaymentFee::IS_DEBT;
        $ServicePaymentFee->fee_of_month = $fee_of_month;
        $ServicePaymentFee->day_expired = $ServiceUtilityBooking->end_time;
        $ServicePaymentFee->type = ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE;
        $ServicePaymentFee->start_time = $ServiceUtilityBooking->start_time;
        $ServicePaymentFee->end_time = $ServiceUtilityBooking->end_time;
        if (!$ServicePaymentFee->save()) {
            Yii::error($ServicePaymentFee->errors);
        } else {
            $ServiceUtilityBooking->is_created_fee = ServiceUtilityBooking::IS_CREATED_FEE;
            $service_payment_fee_incurred_ids = [];
            if(!empty($ServiceUtilityBooking->service_payment_fee_incurred_ids)){
                $service_payment_fee_incurred_ids = Json::decode($ServiceUtilityBooking->service_payment_fee_incurred_ids, true);
            }
            $service_payment_fee_incurred_ids[] = $ServicePaymentFee->id;
            $ServiceUtilityBooking->service_payment_fee_incurred_ids = Json::encode($service_payment_fee_incurred_ids);
            $ServiceUtilityBooking->fee_of_month = $fee_of_month;
            $ServiceUtilityBooking->total_incurred_money += $ServicePaymentFee->price;
            if (!$ServiceUtilityBooking->save()) {
                Yii::error($ServiceUtilityBooking->errors);
            }
            if (!$ServicePaymentFee->apartment->updateCurrentDebt()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            };
        }
        $isCheckFee = true;
        $ServiceUtilityBooking->sendNotifyToResidentUser(null, $ServiceUtilityBooking->apartment->residentUser, ServiceUtilityBooking::CREATE,$isCheckFee);
        return ServicePaymentFeeResponse::findOne(['id' => $ServicePaymentFee->id]);
    }

    public function delete()
    {
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $ServiceUtilityBooking = ServiceUtilityBooking::findOne(['id' => $this->service_utility_booking_id, 'building_cluster_id' => $buildingCluster->id]);
        if (empty($ServiceUtilityBooking)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid Data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $ServicePaymentFee = ServicePaymentFeeResponse::findOne($this->id);
        if (!in_array($ServicePaymentFee->status, [ServicePaymentFeeResponse::STATUS_PAID])) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Delete error"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $price = $ServicePaymentFee->price;
        if ($ServicePaymentFee->delete()) {
            $ServiceUtilityBooking->total_incurred_money -= $price;
            $service_payment_fee_incurred_ids = [];
            if(!empty($ServiceUtilityBooking->service_payment_fee_incurred_ids)){
                $service_payment_fee_incurred_ids = Json::decode($ServiceUtilityBooking->service_payment_fee_incurred_ids, true);
            }
            if (($key = array_search($this->id, $service_payment_fee_incurred_ids)) !== false) {
                unset($service_payment_fee_incurred_ids[$key]);
            }
            $ServiceUtilityBooking->service_payment_fee_incurred_ids = Json::encode($service_payment_fee_incurred_ids);
            if(!$ServiceUtilityBooking->save()){
                Yii::error($ServiceUtilityBooking->errors);
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            if (!$ServicePaymentFee->apartment->updateCurrentDebt()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            };
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success")
            ];
        } else {
            Yii::error($ServicePaymentFee->errors);
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
