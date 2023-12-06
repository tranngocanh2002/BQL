<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\PaymentGenCodeItem;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityFree;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBookingFeeResponse")
 * )
 */
class ServiceBookingFeeResponse extends ServiceUtilityBooking
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="booking_code", type="string", description="mã đặt chỗ"),
     * @SWG\Property(property="payment_gen_code", type="string", description="Mã code của yêu cầu thanh toán nếu có"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="service_utility_free_id", type="integer"),
     * @SWG\Property(property="service_utility_free_name", type="string"),
     * @SWG\Property(property="service_utility_config_id", type="integer"),
     * @SWG\Property(property="service_utility_config_name", type="string"),
     * @SWG\Property(property="status", type="integer", description="-1: cư dân hủy, -2: BQL hủy, -3: Hệ thống hủy, 0: mới tạo, 1: đã xác nhận"),
     * @SWG\Property(property="status_name", type="string", description="tên của trạng thái"),
     * @SWG\Property(property="start_time", type="integer"),
     * @SWG\Property(property="end_time", type="integer"),
     * @SWG\Property(property="total_adult", type="integer", description="tổng người lớn"),
     * @SWG\Property(property="total_child", type="integer", description="tổng trẻ em"),
     * @SWG\Property(property="total_slot", type="integer", description="Tổng slot"),
     * @SWG\Property(property="price", type="integer", description="Tổng giá"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="json_desc", type="object"),
     * @SWG\Property(property="book_time", type="object"),
     * @SWG\Property(property="service_payment_fee_id", type="integer"),
     * @SWG\Property(property="service_payment_fee_deposit_ids", type="array", description="service_payment_fee_deposit_ids",
     *      @SWG\Items(type="integer", default=0),
     * ),
     * @SWG\Property(property="service_payment_fee_incurred_ids", type="array", description="service_payment_fee_incurred_ids",
     *      @SWG\Items(type="integer", default=0),
     * ),
     * @SWG\Property(property="service_payment_total_ids", type="array", description="service_payment_total_ids",
     *      @SWG\Items(type="integer", default=0),
     * ),
     * @SWG\Property(property="is_created_fee", type="integer", description="0- chưa tạo phí, 1 - đã tạo phí"),
     * @SWG\Property(property="is_paid", type="integer", description="0 - chưa thanh toán, 1 - đã thanh toán"),
     * @SWG\Property(property="total_deposit_money", type="integer"),
     * @SWG\Property(property="total_incurred_money", type="integer"),
     * @SWG\Property(property="created_at", type="integer", description="Thời gian tạo"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'booking_code' => function($model){
                return $model->code;
            },
            'payment_gen_code' => function($model){
                if(!empty($model->service_payment_fee_id)){
                    $paymentGenCodeItem = PaymentGenCodeItem::findOne(['service_payment_fee_id' => $model->service_payment_fee_id]);
                    if(!empty($paymentGenCodeItem)){
                        return $paymentGenCodeItem->paymentGenCode->code;
                    }
                }
                return null;
            },
            'service_map_management_id',
            'building_cluster_id',
            'apartment_id',
            'apartment_name' => function($model){
                if(!empty($model->apartment)){
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
            'service_utility_free_id',
            'service_utility_free_name' => function($model){
                if(!empty($model->serviceUtilityFree)){
                    return $model->serviceUtilityFree->name;
                }
                return '';
            },
            'service_utility_config_id',
            'service_utility_config_name' => function($model){
                if(!empty($model->serviceUtilityConfig)){
                    return $model->serviceUtilityConfig->name;
                }
                return '';
            },
            'status',
            'status_name' => function($model){
                if(isset(ServiceUtilityBooking::$status_list[$model->status])){
                    return ServiceUtilityBooking::$status_list[$model->status];
                }
                return '';
            },
            'start_time',
            'end_time',
            'book_time' => function($model){
                if(!empty($model->book_time)){
                    return json_decode($model->book_time, true);
                }
                return null;
            },
            'total_adult',
            'total_child',
            'total_slot',
            'price',
            'description',
            'json_desc' => function($model){
                if(!empty($model->json_desc)){
                    return json_decode($model->json_desc, true);
                }
                return null;
            },
            'service_payment_fee_id',
            'service_payment_fee_deposit_ids' => function($model){
                if(!empty($model->service_payment_fee_deposit_ids)){
                    return Json::decode($model->service_payment_fee_deposit_ids, true);
                }
                return null;
            },
            'service_payment_fee_incurred_ids' => function($model){
                if(!empty($model->service_payment_fee_incurred_ids)){
                    return Json::decode($model->service_payment_fee_incurred_ids, true);
                }
                return null;
            },
            'service_payment_total_ids' => function($model){
                return $model->getPaymentIds();
            },
            'is_created_fee',
            'is_paid' => function($model){
//                if(!empty($model->servicePaymentFee)){
//                    return $model->servicePaymentFee->status;
//                }
//                if($model->price == 0){
//                    return 1;
//                }
                return $model->setIsPaid();
            },
            'total_deposit_money',
            'total_incurred_money',
            'created_at',
            'updated_at',
        ];
    }
}
