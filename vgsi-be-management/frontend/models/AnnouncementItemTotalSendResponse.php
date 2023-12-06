<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\ServiceDebt;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementItemTotalSendResponse")
 * )
 */
class AnnouncementItemTotalSendResponse extends AnnouncementItem
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="email", type="string", description="email"),
     * @SWG\Property(property="phone", type="string", description="phone"),
     * @SWG\Property(property="device_token", type="string", description="device token"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="content_sms", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="status_sms", type="integer", description="0- đã gửi, 1 - gửi thành công, 2 - gửi lỗi"),
     * @SWG\Property(property="status_email", type="integer", description="0- đã gửi, 1 - gửi thành công, 2 - gửi lỗi"),
     * @SWG\Property(property="status_notify", type="integer", description="0- đã gửi, 1 - gửi thành công, 2 - gửi lỗi"),
     */
    public function fields()
    {
        return [
            'id',
            'email',
            'phone',
            'device_token',
            'created_at',
            'description',
            'content',
            'content_sms',
            'status_sms',
            'status_email',
            'status_notify',
        ];
    }
}
