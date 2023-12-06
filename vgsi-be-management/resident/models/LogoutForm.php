<?php

namespace resident\models;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use common\models\ResidentUserAccessToken;
use common\models\ResidentUserDeviceToken;
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
            $accessToken = ApiHelper::getAuthorization();
            $residentUserAccessToken = ResidentUserAccessToken::findOne(['token_hash' => md5($accessToken), 'type' => ResidentUserAccessToken::TYPE_ACCESS_TOKEN]);
            ResidentUserDeviceToken::deleteAll(['resident_user_access_token_id' => $residentUserAccessToken->id]);

            if(!$residentUserAccessToken->delete()){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $residentUserAccessToken->getErrors()
                ];
            };
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
