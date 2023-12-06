<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ManagementNotifyReceiveConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementNotifyReceiveConfigCreateForm")
 * )
 */
class ManagementNotifyReceiveConfigCreateForm extends Model
{
    /**
     * @SWG\Property(description="0 - notify app, 1 - email, 2 - sms", default=0, type="integer")
     * @var integer
     */
    public $channel;

    /**
     * @SWG\Property(description="0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc", default=0, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="0 - ko nhận, 1 - có nhận", default=0, type="integer")
     * @var integer
     */
    public $action_create;

    /**
     * @SWG\Property(description="0 - ko nhận, 1 - có nhận", default=0, type="integer")
     * @var integer
     */
    public $action_update;

    /**
     * @SWG\Property(description="0 - ko nhận, 1 - có nhận", default=0, type="integer")
     * @var integer
     */
    public $action_cancel;

    /**
     * @SWG\Property(description="0 - ko nhận, 1 - có nhận", default=0, type="integer")
     * @var integer
     */
    public $action_delete;

    /**
     * @SWG\Property(description="0 - ko nhận, 1 - có nhận", default=0, type="integer")
     * @var integer
     */
    public $action_approved;

    /**
     * @SWG\Property(description="0 - ko nhận, 1 - có nhận", default=0, type="integer")
     * @var integer
     */
    public $action_comment;

    /**
     * @SWG\Property(description="0 - ko nhận, 1 - có nhận", default=0, type="integer")
     * @var integer
     */
    public $action_rate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel', 'type'], 'required', "on" => ['update']],
            [['channel', 'action_create', 'type', 'action_update', 'action_cancel', 'action_delete', 'action_approved', 'action_comment', 'action_rate'], 'integer'],
        ];
    }

    public function update()
    {
        $user = Yii::$app->user->identity;
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $model = ManagementNotifyReceiveConfigResponse::findOne(['type' => $this->type, 'channel' => $this->channel, 'building_cluster_id' => $buildingCluster->id, 'management_user_id' => $user->id]);
        if(empty($model)){
            $model = new ManagementNotifyReceiveConfigResponse();
        }
        $model->load(CUtils::arrLoad($this->attributes), '');
        $model->building_cluster_id = $buildingCluster->id;
        $model->management_user_id = $user->id;
        if (!$model->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model;
    }
}
