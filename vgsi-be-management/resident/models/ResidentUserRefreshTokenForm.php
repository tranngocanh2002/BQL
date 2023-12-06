<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use common\models\ResidentUserAccessToken;
use yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserRefreshTokenForm")
 * )
 */
class ResidentUserRefreshTokenForm extends Model
{

    /**
     * @SWG\Property(description="Refresh Token")
     * @var string
     */
    public $refresh_token;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['refresh_token', 'required'],
        ];
    }

    public function refreshAccessToken()
    {
        $refresh_token = $this->refresh_token;
        $jwt = null;
        try {
            $refreshToken = ResidentUserAccessToken::findOne(['type' => ResidentUserAccessToken::TYPE_REFRESH_TOKEN, 'token_hash' => md5($refresh_token)]);
            if(!$refreshToken){
                throw new Exception(Yii::t('resident', "Refresh Token not found"));
            }
            $resident = ResidentUser::findOne(['id' => $refreshToken->resident_user_id]);
            if (empty($resident)) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', 'Management User invalid'),
                    'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                ];
            }

            if ($resident->status != ResidentUser::STATUS_ACTIVE) {
                throw new Exception(Yii::t('resident', "User not found or is activated"));
            }

            $jwt_config = Yii::$app->params['jwt-config'];
            $payload = array(
                'iss' => $jwt_config['iss'],
                'aud' => $jwt_config['aud'],
                'exp' => $jwt_config['time'],
                'jti' => $resident->id
            );
            $jwt = ResidentUser::generateApiToken($payload);

            //sinh refresh token
            $payloadRefresh = $payload;
            $payloadRefresh['time_refresh'] = $jwt_config['time_refresh'];
            $jwtRefresh = ResidentUser::generateApiToken($payloadRefresh);

            $tokenLogin = new ResidentUserAccessToken();
            $tokenLogin->resident_user_id = $resident->id;
            $tokenLogin->token = $jwt;
            $tokenLogin->setTokenHash();
            $tokenLogin->expired_at = $jwt_config['time'];
            if (!$tokenLogin->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', 'System busy'),
                    'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                    'error' => $tokenLogin->errors
                ];
            }
            $refreshToken->delete();
            $tokenLoginRefresh = new ResidentUserAccessToken();
            $tokenLoginRefresh->resident_user_id = $resident->id;
            $tokenLoginRefresh->type = ResidentUserAccessToken::TYPE_REFRESH_TOKEN;
            $tokenLoginRefresh->token = $jwtRefresh;
            $tokenLoginRefresh->setTokenHash();
            $tokenLoginRefresh->expired_at = $jwt_config['time_refresh'];
            if (!$tokenLoginRefresh->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', 'System busy'),
                    'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                    'error' => $tokenLoginRefresh->errors
                ];
            }
            return [
                'success' => true,
                'access_token' => $jwt,
                'refresh_token' => $jwtRefresh,
                'info_user' => ResidentUserResponse::findOne(['id' => $resident->id]),
                'apartments' => ApartmentMapResidentUserResponse::find()->where(['resident_user_id' => $resident->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all()
            ];
        } catch (\Exception $e) {
            Yii::error($e, 'Errors login');
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'statusCode'=>ErrorCode::ERROR_SYSTEM_ERROR,
            ];
        }
    }
}
