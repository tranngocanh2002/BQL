<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ResidentUserNotify;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserNotifyResponse")
 * )
 */
class ResidentUserNotifyResponse extends ResidentUserNotify
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="title_en", type="string"),
     * @SWG\Property(property="description_en", type="string"),
     * @SWG\Property(property="type", type="integer", description="0 - request, 1 - request answer, 2 - request answer internal, 3 - service bill, 4 - announcement"),
     * @SWG\Property(property="is_read", type="integer"),
     * @SWG\Property(property="is_hidden", type="integer"),
     * @SWG\Property(property="request_id", type="integer"),
     * @SWG\Property(property="request_answer_id", type="integer"),
     * @SWG\Property(property="request_answer_internal_id", type="integer"),
     * @SWG\Property(property="service_bill_id", type="integer"),
     * @SWG\Property(property="service_booking_id", type="integer"),
     * @SWG\Property(property="announcement_item_id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="service_utility_form_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="created_at", type="integer", description="Thời gian tạo"),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'description',
            'title_en',
            'description_en',
            'type',
            'is_read',
            'is_hidden',
            'request_id',
            'request_answer_id',
            'request_answer_internal_id',
            'service_bill_id',
            'service_booking_id',
            'announcement_item_id',
            'apartment_id',
            'service_utility_form_id',
            'apartment_name' => function($model){
                if (!empty($model->apartment)) {
                    return $model->apartment->name;
                }
                return '';
            },
            'created_at',
        ];
    }
}
