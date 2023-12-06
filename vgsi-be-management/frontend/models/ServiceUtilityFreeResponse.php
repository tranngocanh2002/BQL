<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityFree;
use frontend\models\ServiceUtilityRatingResponse;
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
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="regulation", type="string", description="quy định"),
     * @SWG\Property(property="json_desc", type="object"),
     * @SWG\Property(property="hotline", type="string"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_map_management_service_name", type="string"),
     * @SWG\Property(property="qrcode", type="string"),
     * @SWG\Property(property="booking_type", type="integer", description="0 - đặt theo lượt, 1 - đặt theo slot, 2 - đặt theo khung giờ"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="total_booking_month", type="integer", description="Tổng yêu cầu đặt trong tháng hiện tại"),
     * @SWG\Property(property="timeout_pay_request", type="integer", description="Thời gian chờ tạo yêu cầu thanh toán"),
     * @SWG\Property(property="timeout_cancel_book", type="integer", description="Thời gian chờ được phép hủy"),
     * @SWG\Property(property="limit_book_apartment", type="integer", description="Giới hạn lượt book của căn hộ trong 1 tháng"),
     * @SWG\Property(property="deposit_money", type="integer", description="Số tiền cần đặt cọc khi đặt dịch vụ"),
     * @SWG\Property(property="status", type="integer", description="Trạng thái: -1 Dừng hoạt động, 0 Tạm dừng hoạt động, 1 Đang hoạt động"),
     * @SWG\Property(property="service_utility_ratting", type="object", ref="#/definitions/ServiceUtilityRatingResponse", description="Đánh giá tiện ích"),
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
            'json_desc' => function($model){
                if(!empty($model->json_desc)){
                    return json_decode($model->json_desc, true);
                }
                return null;
            },
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
            'qrcode' => function ($model) {
                $qr = Yii::$app->qr;
                $imageLogo = Yii::$app->getUrlManager()->getBaseUrl() . 'logo.png';
                return $qr->setText($model->code)->useLogo($imageLogo)->setLogoWidth('65')->writeDataUri();
            },
            'booking_type',
            'total_booking_month' => function($model){
                $start_time = strtotime(date('Y-m-01 00:00:00', time()));
                $end_time = strtotime(date('Y-m-t 23:59:59', time()));
                $serviceUtilityBookingCount = ServiceUtilityBooking::find()
                    ->where(['service_utility_free_id' => $model->id])
                    ->andWhere(['>=', 'created_at', $start_time])
                    ->andWhere(['<=', 'created_at', $end_time])->count();
                return (int)$serviceUtilityBookingCount;
            },
            'timeout_pay_request',
            'timeout_cancel_book',
            'limit_book_apartment',
            'deposit_money',
            'status',
            'service_utility_ratting' => function($model){
                return ServiceUtilityRatingResponse::findOne(['service_utility_free_id' => $model->id]);
            },
            'created_at',
            'updated_at',
        ];
    }
}
