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
 *   @SWG\Xml(name="ManagementNotifyReceiveConfigUpdateForm")
 * )
 */
class ManagementNotifyReceiveConfigUpdateForm extends Model
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
        $user = Yii::$app->user->identity;
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $res = ManagementNotifyReceiveConfig::updateAll(
            [
                'action_create' => $this->check_all,
                'action_update' => $this->check_all,
                'action_cancel' => $this->check_all,
                'action_delete' => $this->check_all,
                'action_approved' => $this->check_all,
                'action_comment' => $this->check_all,
                'action_rate' => $this->check_all,
            ],
            ['building_cluster_id' => $buildingCluster->id, 'management_user_id' => $user->id]
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
