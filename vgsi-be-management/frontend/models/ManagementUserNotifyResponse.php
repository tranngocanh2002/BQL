<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ManagementUserNotify;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserNotifyResponse")
 * )
 */
class ManagementUserNotifyResponse extends ManagementUserNotify
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="title_en", type="string"),
     * @SWG\Property(property="description_en", type="string"),
     * @SWG\Property(property="management_user_id", type="integer"),
     * @SWG\Property(property="type", type="integer", description="0 - request, 1 - request answer, 2 - request answer internal, 3 - service bill, 4 - service payment fee, 5 - apartment create bill, 6 - service booking , 7 security mode"),
     * @SWG\Property(property="is_read", type="integer"),
     * @SWG\Property(property="is_hidden", type="integer"),
     * @SWG\Property(property="request_id", type="integer"),
     * @SWG\Property(property="request_answer_id", type="integer"),
     * @SWG\Property(property="request_answer_internal_id", type="integer"),
     * @SWG\Property(property="service_bill_id", type="integer"),
     * @SWG\Property(property="service_booking_id", type="integer"),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="service_utility_form_id", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'title_en',
            'description',
            'description_en',
            'management_user_id',
            'type',
            'is_read',
            'is_hidden',
            'request_id',
            'request_answer_id',
            'request_answer_internal_id',
            'service_bill_id',
            'service_booking_id',
            'code',
            'service_utility_form_id',
            'created_at',
            'updated_at',
        ];
    }
}
