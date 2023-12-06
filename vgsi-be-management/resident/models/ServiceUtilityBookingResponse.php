<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityFree;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityBookingResponse")
 * )
 */
class ServiceUtilityBookingResponse extends ServiceUtilityBooking
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management", type="object", ref="#/definitions/ServiceMapManagementResponse"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="resident_user_name", type="string", description="tên người đặt"),
     * @SWG\Property(property="resident_user_phone", type="string", description="số dt người đặt"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="service_utility_free_id", type="integer"),
     * @SWG\Property(property="service_utility_free", type="object", ref="#/definitions/ServiceUtilityFreeResponse"),
     * @SWG\Property(property="service_utility_rating", type="object", ref="#/definitions/ServiceUtilityRatingResponse"),
     * @SWG\Property(property="service_utility_config_id", type="integer"),
     * @SWG\Property(property="service_utility_config_name", type="string"),
     * @SWG\Property(property="service_utility_config_name_en", type="string"),
     * @SWG\Property(property="status", type="integer", description="-1: Cư dân hủy, -2: BQL hủy, -3: Hệ thống hủy, 0: mới tạo, 1: đã xác nhận"),
     * @SWG\Property(property="start_time", type="integer"),
     * @SWG\Property(property="end_time", type="integer"),
     * @SWG\Property(property="total_adult", type="integer", description="tổng người lớn"),
     * @SWG\Property(property="total_child", type="integer", description="tổng trẻ em"),
     * @SWG\Property(property="total_slot", type="integer", description="Tổng slot"),
     * @SWG\Property(property="price", type="integer", description="Tổng giá"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="json_desc", type="object"),
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
     * @SWG\Property(property="total_deposit_money", type="integer"),
     * @SWG\Property(property="total_incurred_money", type="integer"),
     * @SWG\Property(property="reason", type="string", description="lý do hủy"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="is_paid", type="integer", description="1: đã thanh toán, 0: chưa thanh toán"),
     */
    public function fields()
    {
        return [
            'id',
            'service_map_management_id',
            'service_map_management' => function($model){
                return ServiceMapManagementResponse::findOne($model->service_map_management_id);
            },
            'building_cluster_id',
            'resident_user_name' => function ($model) {
                /**
                 * @var $model ServiceUtilityBooking
                 */
                if (!empty($model->residentUser)) {
                    $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $model->residentUser->phone, 'apartment_id' => $model->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                    if(!empty($apartmentMapResidentUser)){
                        return $apartmentMapResidentUser->resident_user_first_name;
                    }
                }
                return '';
            },
            'resident_user_phone' => function($model){
                if(!empty($model->residentUser)){
                    return $model->residentUser->phone;
                }
                return '';
            },
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
            'service_utility_free' => function($model){
                return ServiceUtilityFreeResponse::findOne($model->service_utility_free_id);
            },
            'service_utility_rating' => function($model){
                return ServiceUtilityRatingResponse::findOne(['service_utility_booking_id' => $model->id]);
            },
            'service_utility_config_id',
            'service_utility_config_name' => function($model){
                if(!empty($model->serviceUtilityConfig)){
                    return $model->serviceUtilityConfig->name;
                }
                return '';
            },
            'service_utility_config_name_en' => function($model){
                if(!empty($model->serviceUtilityConfig)){
                    return $model->serviceUtilityConfig->name_en;
                }
                return '';
            },
            'status',
            'start_time',
            'end_time',
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
            'book_time' => function($model){
                if(!empty($model->book_time)){
                    return json_decode($model->book_time, true);
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
            'total_deposit_money',
            'total_incurred_money',
            'reason',
            'created_at',
            'updated_at',
            'is_paid',
        ];
    }
}
