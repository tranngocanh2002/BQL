<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\NotifySendConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="NotifySendConfigResponse")
 * )
 */
class NotifySendConfigResponse extends NotifySendConfig
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="type", type="integer", description="0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc"),
     * @SWG\Property(property="type_name", type="string", description="0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc"),
     * @SWG\Property(property="send_email", type="integer", description="0 - ko gửi, 1 - có gửi"),
     * @SWG\Property(property="send_sms", type="integer", description="0 - ko gửi, 1 - có gửi"),
     * @SWG\Property(property="send_notify_app", type="integer", description="0 - ko gửi, 1 - có gửi"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'building_cluster_id',
            'type',
            'type_name' => function($model){
                return NotifySendConfig::$type_list[$model->type];
            },
            'send_email',
            'send_sms',
            'send_notify_app',
            'updated_at',
        ];
    }
}
