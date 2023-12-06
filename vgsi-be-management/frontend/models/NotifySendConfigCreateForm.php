<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\NotifySendConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="NotifySendConfigCreateForm")
 * )
 */
class NotifySendConfigCreateForm extends Model
{
    /**
     * @SWG\Property(description="0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc", default=0, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="0 - ko gửi, 1 - có gửi", default=0, type="integer")
     * @var integer
     */
    public $send_email;

    /**
     * @SWG\Property(description="0 - ko gửi, 1 - có gửi", default=0, type="integer")
     * @var integer
     */
    public $send_sms;

    /**
     * @SWG\Property(description="0 - ko gửi, 1 - có gửi", default=1, type="integer")
     * @var integer
     */
    public $send_notify_app;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required', "on" => ['update']],
            [['send_email', 'send_sms', 'type', 'send_notify_app'], 'integer'],
        ];
    }

    public function update()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $model = NotifySendConfigResponse::findOne(['type' => $this->type, 'building_cluster_id' => $buildingCluster->id]);
        if(empty($model)){
            $model = new NotifySendConfigResponse();
        }
        $model->load(CUtils::arrLoad($this->attributes), '');
        $model->building_cluster_id = $buildingCluster->id;
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
