<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserDeviceToken;
use Yii;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserChangeStatusForm")
 * )
 */
class ManagementUserChangeStatusForm extends Model
{
    /**
     * @SWG\Property(description="management user id")
     * @var integer
     */
    public $management_user_id;

    /**
     * @SWG\Property(description="status = 1 is_active, 0 not active")
     * @var integer
     */
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['management_user_id', 'status'], 'required'],
            [['management_user_id', 'status'], 'integer'],
            ['status', 'in', 'range' => [ManagementUser::STATUS_ACTIVE, ManagementUser::STATUS_NOT_ACTIVE]],
        ];
    }

    public function changeStatus()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $managementUser = ManagementUser::findOne(['id' => $this->management_user_id, 'is_deleted' => ManagementUser::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
            if(empty($managementUser)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $managementUser->status = $this->status;
            if(!$managementUser->save()){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if($managementUser->status == ManagementUser::STATUS_NOT_ACTIVE){
                ManagementUserDeviceToken::deleteAll(['management_user_id' => $managementUser->id]);
                ManagementUserAccessToken::deleteAll(['management_user_id' => $managementUser->id]);
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update success")
            ];
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
