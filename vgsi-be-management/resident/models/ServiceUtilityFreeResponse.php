<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityFree;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityFreeResponse")
 * )
 */
class ServiceUtilityFreeResponse extends ServiceUtilityFree
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="regulation", type="string"),
     * @SWG\Property(property="hotline", type="string"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_service_name", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="booking_type", type="integer", description="0 - đặt theo lượt, 1 - đặt theo slot, 2 - đặt theo khung giờ"),
     * @SWG\Property(property="timeout_pay_request", type="integer", description="Thời gian chờ tạo yêu cầu thanh toán"),
     * @SWG\Property(property="timeout_cancel_book", type="integer", description="Thời gian chờ được phép hủy"),
     * @SWG\Property(property="limit_book_apartment", type="integer", description="Giới hạn lượt book của căn hộ trong 1 tháng"),
     * @SWG\Property(property="deposit_money", type="integer", description="Số tiền đặt cọc dịch vụ"),
     * @SWG\Property(property="status", type="integer", description="0 : ngừng hoạt động, 1 : hoạt động"),
     * @SWG\Property(property="is_paid", type="integer", description="0 : chưa thanh toán, 1 : đã thanh toán"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'name_en',
            'code',
            'description',
            'regulation',
            'hours_open',
            'hours_close',
            'hotline',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
            'service_map_management_id',
            'service_map_management_service_name' => function($model){
                if(!empty($model->serviceMapManagement)){
                    return $model->serviceMapManagement->service_name;
                }
                return '';
            },
            'service_map_management_service_name_en' => function($model){
                if(!empty($model->serviceMapManagement)){
                    return $model->serviceMapManagement->service_name_en;
                }
                return '';
            },
            'booking_type',
            'timeout_pay_request',
            'timeout_cancel_book',
            'limit_book_apartment',
            'deposit_money',
            'status',
            'is_paid' => function($model){
                if(!empty($model->serviceUtilityBooking)){
                    return $model->serviceUtilityBooking->is_paid;
                }
                return ServiceUtilityBooking::IS_UNPAID;
            },
            'created_at',
            'updated_at',
        ];
    }
}
