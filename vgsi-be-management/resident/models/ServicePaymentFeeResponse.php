<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\PaymentGenCodeItem;
use common\models\ServiceBill;
use common\models\ServiceBillItem;
use common\models\ServiceOldDebitFee;
use common\models\ServiceParkingFee;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServicePaymentFeeResponse")
 * )
 */
class ServicePaymentFeeResponse extends ServicePaymentFee
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_service_name", type="string"),
     * @SWG\Property(property="service_map_management_service_icon_name", type="string"),
     * @SWG\Property(property="service_map_management_service_base_url", type="string"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="building_area_id", type="integer"),
     * @SWG\Property(property="building_area_name", type="string"),
     * @SWG\Property(property="approved_by_name", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="description_en", type="string"),
     * @SWG\Property(property="json_desc", type="object"),
     * @SWG\Property(property="price", type="integer"),
     * @SWG\Property(property="money_collected", type="integer", description="số tiền đã thu được"),
     * @SWG\Property(property="more_money_collecte", type="integer", description="số tiền cần thu thêm"),
     * @SWG\Property(property="is_draft", type="integer", description="0 - không phải nháp, 1 - là nháp"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="for_type", type="integer", description="0 - phí sử dụng, 1 - phí đặt cọc, 2 - phí phát sinh"),
     * @SWG\Property(property="fee_of_month", type="integer"),
     * @SWG\Property(property="service_bills", type="array",
     *     @SWG\Items(type="object",
     *          @SWG\Property(property="id", type="integer"),
     *          @SWG\Property(property="code", type="string"),
     *          @SWG\Property(property="number", type="string"),
     *          @SWG\Property(property="status", type="integer"),
     *      )
     * ),
     * @SWG\Property(property="day_expired", type="integer"),
     * @SWG\Property(property="is_pay", type="integer", description="0 - không được thanh toán nữa, 1 -được thanh toán tiếp"),
     * @SWG\Property(property="type", type="integer", description="0 - Nước, 1 -Dịch vụ, 2 - Xe, 3 - Điện, 4 - Booking, 5 - Nợ cũ"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'service_map_management_id',
            'service_map_management_service_name' => function ($model) {
                $name = '';
                if($model->for_type == ServicePaymentFee::FOR_TYPE_1){
                    $name = 'Đặt cọc - ';
                }else if($model->for_type == ServicePaymentFee::FOR_TYPE_2){
//                    $name = 'Phát sinh - ';
                }
                if (!empty($model->serviceMapManagement)) {
                    $name .= $model->serviceMapManagement->service_name;
                    if($model->type == ServicePaymentFee::TYPE_SERVICE_PARKING_FEE){
                        $serviceParkingFee = ServiceParkingFee::findOne(['service_payment_fee_id' => $model->id]);
                        if(!empty($serviceParkingFee)){
                            if(!empty($serviceParkingFee->serviceManagementVehicle)){
                                $name .= ' - BKS: ' . $serviceParkingFee->serviceManagementVehicle->number;
                            }
                        }
                    }else{
                        // lấy tên của dịch vụ theo type
                        if($model->type === ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE)
                        {
                            //nếu phí từ book sẽ lấy thêm thông tin tiện ích
                            $booking = ServiceUtilityBooking::find()->where(['like', 'service_payment_fee_ids_text_search', ','.$model->id.','])->orderBy(['created_at' => SORT_DESC])->one();
                            if(!empty($booking)){
                                if(!empty($booking->serviceUtilityFree)){
                                    $name .= ' - ' .$booking->serviceUtilityFree->name;
                                }
                            }
                        }
                    }
                }
                return $name;
            },
            'service_map_management_service_name_en' => function ($model) {
                $name_en = '';
                if($model->for_type == ServicePaymentFee::FOR_TYPE_1){
                    $name_en = 'Deposit - ';
                }else if($model->for_type == ServicePaymentFee::FOR_TYPE_2){
//                    $name_en = 'Incurred - ';
                }
                if (!empty($model->serviceMapManagement)) {
                    $name_en .= $model->serviceMapManagement->service_name_en;
                    if($model->type == ServicePaymentFee::TYPE_SERVICE_PARKING_FEE){
                        $serviceParkingFee = ServiceParkingFee::findOne(['service_payment_fee_id' => $model->id]);
                        if(!empty($serviceParkingFee)){
                            if(!empty($serviceParkingFee->serviceManagementVehicle)){
                                $name_en .= ' - Number: ' . $serviceParkingFee->serviceManagementVehicle->number;
                            }
                        }
                    }else{
                        // lấy tên của dịch vụ theo type
                        if($model->type === ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE)
                        {
                            //nếu phí từ book sẽ lấy thêm thông tin tiện ích
                            $booking = ServiceUtilityBooking::find()->where(['like', 'service_payment_fee_ids_text_search', ','.$model->id.','])->orderBy(['created_at' => SORT_DESC])->one();
                            if(!empty($booking)){
                                if(!empty($booking->serviceUtilityFree)){
                                    $name_en .= ' - ' .$booking->serviceUtilityFree->name_en;
                                }
                            }
                        }
                    }
                }
                return $name_en;
            },
            'service_map_management_service_icon_name' => function ($model) {
                if (!empty($model->serviceMapManagement)) {
                    return $model->serviceMapManagement->service_icon_name;
                }
                return '';
            },
            'service_map_management_service_base_url' => function ($model) {
                if (!empty($model->serviceMapManagement)) {
                    return $model->serviceMapManagement->service_base_url;
                }
                return '';
            },
            'apartment_id',
            'apartment_name' => function ($model) {
                if (!empty($model->apartment)) {
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_parent_path' => function ($model) {
                if (!empty($model->apartment)) {
                    return trim($model->apartment->parent_path, '/');
                }
                return '';
            },
            'resident_user_name' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->resident_user_name;
                }
                return '';
            },
            'building_area_id',
            'building_area_name' => function($model){
                if($model->buildingArea){
                    return $model->buildingArea->name;
                }
                return '';
            },
            'service_bills' => function($model){
                if(!empty($model->service_bill_ids)){
                    $ids = json_decode($model->service_bill_ids, true);
                    $ServiceBills = ServiceBill::find()->where(['id' => $ids])->all();
                    $res = [];
                    foreach ($ServiceBills as $serviceBill){
                        $res[] = [
                            'id' => $serviceBill->id,
                            'code' => $serviceBill->code,
                            'number' => $serviceBill->number,
                            'status' => $serviceBill->status,
                        ];
                    }
                    return $res;
                }
                return null;
            },
            'description',
            'description_en',
            'json_desc' => function($model){
                if(!empty($model->json_desc)){
                    return json_decode($model->json_desc, true);
                }
                return null;
            },
            'price',
            'money_collected',
            'more_money_collecte',
            'is_draft',
            'status',
            'fee_of_month',
            'day_expired',
            'approved_by_name' => function($model){
                if(!empty($model->managementUser)){
                    return $model->managementUser->first_name;
                }
                return '';
            },
            'is_pay' => function($model){
                $is_pay = 1;
                if($model->more_money_collecte == 0){
                    $is_pay = 0;
                }
                $paymentGenCodeItem = PaymentGenCodeItem::findOne(['service_payment_fee_id' => $model->id, 'status' => PaymentGenCodeItem::STATUS_UNPAID]);
                if(!empty($paymentGenCodeItem)){
                    $is_pay = 0;
                }
                return $is_pay;
            },
            'for_type',
            'type',
            'created_at',
            'updated_at',
        ];
    }
}
