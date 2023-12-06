<?php

namespace frontend\models;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserDeviceToken;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="LogoutForm")
 * )
 */
class LogoutForm extends Model
{
    /**
     * Logs in a user using the provided email and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function logout()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
//            $accessToken = ApiHelper::getAuthorization();
            ManagementUserAccessToken::deleteAll(['management_user_id' => $user->id]);
            ManagementUserDeviceToken::deleteAll(['management_user_id' => $user->id]);
//            $managementUserAccessToken = ManagementUserAccessToken::findOne(['token_hash' => md5($accessToken), 'type' => ManagementUserAccessToken::TYPE_ACCESS_TOKEN]);
//            ManagementUserDeviceToken::deleteAll(['management_user_access_token_id' => $managementUserAccessToken->id]);
//
//            if(!$managementUserAccessToken->delete()){
//                $transaction->rollBack();
//                return [
//                    'success' => false,
//                    'message' => Yii::t('frontend', "Invalid data"),
//                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                    'errors' => $managementUserAccessToken->getErrors()
//                ];
//            };
            $transaction->commit();
            return [
                'success' => true,
            ];
        } catch (\Exception $e) {
            Yii::error($e, 'Errors logout');
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
            ];
        }
    }
}
