<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserDeviceToken;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use common\models\User;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserSetPasswordForm")
 * )
 */
class ManagementUserSetPasswordForm extends Model {

    /**
     * @SWG\Property(description="Password")
     * @var string
     */
    public $password;

    /**
     * @SWG\Property(description="management_user_id")
     * @var integer
     */
    public $management_user_id;

    /**
     * @SWG\Property(description="is_send_email: 0 - không gửi email thông báo, 1 - có gửi email thông báo")
     * @var integer
     */
    public $is_send_email;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['management_user_id', 'password'], 'required'],
            ['password', 'string', 'min' => 6],
            [['is_send_email'], 'integer']
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function setPassword() {
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
            // check mật khẩu mới có trùng mật khẩu cũ
            if($managementUser->validatePassword($this->password))
            {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "New password not equals old password"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $managementUser->setPassword($this->password);
            if(!$managementUser->save()){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Set password error"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $managementUser->getErrors()
                ];
            };
            ManagementUserAccessToken::deleteAll(['management_user_id' => $managementUser->id]);
            ManagementUserDeviceToken::deleteAll(['management_user_id' => $managementUser->id]);
            $transaction->commit();
            if($this->is_send_email === 1){
                $managementUser->sendEmailCreatePassword($this->password);
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Set password success")
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

