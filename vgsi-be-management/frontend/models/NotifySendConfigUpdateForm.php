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
 *   @SWG\Xml(name="NotifySendConfigUpdateForm")
 * )
 */
class NotifySendConfigUpdateForm extends Model
{
    /**
     * @SWG\Property(description="0 - hủy tất cả, 1 - chọn tất cả", default=0, type="integer")
     * @var integer
     */
    public $check_all;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['check_all'], 'required'],
            [['check_all'], 'integer'],
        ];
    }

    public function update()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $res = NotifySendConfig::updateAll(
            [
                'send_email' => $this->check_all,
                'send_sms' => $this->check_all,
                'send_notify_app' => $this->check_all,
            ],
            ['building_cluster_id' => $buildingCluster->id]
        );
//        if (!$res) {
//            return [
//                'success' => false,
//                'message' => Yii::t('frontend', "Update Error"),
//                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//            ];
//        }
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Update Success"),
        ];
    }
}
