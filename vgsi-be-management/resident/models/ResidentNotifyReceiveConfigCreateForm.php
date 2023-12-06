<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentNotifyReceiveConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentNotifyReceiveConfigCreateForm")
 * )
 */
class ResidentNotifyReceiveConfigCreateForm extends Model
{
    /**
     * @SWG\Property(description="id căn hộ", default=0, type="integer")
     * @var integer
     */
    public $apartment_id;

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
            [['apartment_id', 'channel', 'type'], 'required', "on" => ['update']],
            [['channel', 'action_create', 'type', 'action_update', 'action_cancel', 'action_delete', 'action_approved', 'action_comment', 'action_rate'], 'integer'],
        ];
    }

    public function update()
    {
        $user = Yii::$app->user->identity;
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
        $model = ResidentNotifyReceiveConfigResponse::findOne(['type' => $this->type, 'channel' => $this->channel, 'building_cluster_id' => $building_cluster_id, 'resident_user_id' => $user->id]);
        if(empty($model)){
            $model = new ResidentNotifyReceiveConfigResponse();
        }
        $model->load(CUtils::arrLoad($this->attributes), '');
        $model->building_cluster_id = $building_cluster_id;
        $model->resident_user_id = $user->id;
        if (!$model->save()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model;
    }
}
