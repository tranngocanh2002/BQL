<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ManagementNotifyReceiveConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementNotifyReceiveConfigResponse")
 * )
 */
class ManagementNotifyReceiveConfigResponse extends ManagementNotifyReceiveConfig
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="management_user_id", type="integer"),
     * @SWG\Property(property="channel", type="integer", description=" 0 - notify app, 1 - email, 2 - sms"),
     * @SWG\Property(property="channel_name", type="string", description=" 0 - notify app, 1 - email, 2 - sms"),
     * @SWG\Property(property="type", type="integer", description="0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc"),
     * @SWG\Property(property="type_name", type="string", description="0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc"),
     * @SWG\Property(property="action_create", type="integer", description="0 - ko nhận, 1 - có nhận"),
     * @SWG\Property(property="action_update", type="integer", description="0 - ko nhận, 1 - có nhận"),
     * @SWG\Property(property="action_cancel", type="integer", description="0 - ko nhận, 1 - có nhận"),
     * @SWG\Property(property="action_delete", type="integer", description="0 - ko nhận, 1 - có nhận"),
     * @SWG\Property(property="action_approved", type="integer", description="0 - ko nhận, 1 - có nhận"),
     * @SWG\Property(property="action_comment", type="integer", description="0 - ko nhận, 1 - có nhận"),
     * @SWG\Property(property="action_rate", type="integer", description="0 - ko nhận, 1 - có nhận"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'building_cluster_id',
            'management_user_id',
            'channel',
            'channel_name' => function($model){
                return ManagementNotifyReceiveConfig::$channel_list[$model->channel];
            },
            'type',
            'type_name' => function($model){
                return ManagementNotifyReceiveConfig::$type_list[$model->type];
            },
            'action_create',
            'action_update',
            'action_cancel',
            'action_delete',
            'action_approved',
            'action_comment',
            'action_rate',
            'updated_at',
        ];
    }
}
