<?php

namespace frontend\models;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserLoginLog;
use common\models\rbac\AuthGroupResponse;
use yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserRefreshTokenForm")
 * )
 */
class ManagementUserRefreshTokenForm extends Model
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
            $refreshToken = ManagementUserAccessToken::findOne(['type' => ManagementUserAccessToken::TYPE_REFRESH_TOKEN, 'token_hash' => md5($refresh_token)]);
            if(!$refreshToken){
                throw new Exception(Yii::t('frontend', "Refresh Token not found"));
            }
            $userLogin = ManagementUser::findOne(['id' => $refreshToken->management_user_id]);
            if (empty($userLogin)) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', 'Management User invalid'),
                    'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                ];
            }

            if ((int)$userLogin->status != ManagementUser::STATUS_ACTIVE) {
                throw new Exception(Yii::t('frontend', "User not found or is activated"));
            }

            $jwt_config = Yii::$app->params['jwt-config'];
            $payload = array(
                'iss' => $jwt_config['iss'],
                'aud' => $jwt_config['aud'],
                'exp' => $jwt_config['time'],
                'jti' => $userLogin->id,
            );
            $jwt = ManagementUser::generateApiToken($payload);

            //sinh refresh token
            $payloadRefresh = $payload;
            $payloadRefresh['time_refresh'] = $jwt_config['time_refresh'];
            $jwtRefresh = ManagementUser::generateApiToken($payloadRefresh);

            $tokenLogin = new ManagementUserAccessToken();
            $tokenLogin->management_user_id = $userLogin->id;
            $tokenLogin->token = $jwt;
            $tokenLogin->setTokenHash();
            $tokenLogin->expired_at = $jwt_config['time'];
            if (!$tokenLogin->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', 'System busy'),
                    'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                    'error' => $tokenLogin->errors
                ];
            }
            $refreshToken->delete();
            $tokenLoginRefresh = new ManagementUserAccessToken();
            $tokenLoginRefresh->management_user_id = $userLogin->id;
            $tokenLoginRefresh->type = ManagementUserAccessToken::TYPE_REFRESH_TOKEN;
            $tokenLoginRefresh->token = $jwtRefresh;
            $tokenLoginRefresh->setTokenHash();
            $tokenLoginRefresh->expired_at = $jwt_config['time_refresh'];
            if (!$tokenLoginRefresh->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', 'System busy'),
                    'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                    'error' => $tokenLoginRefresh->errors
                ];
            }
            //lấy thông tin auth_group trả xuống
            $authGroup = AuthGroupResponse::findOne(['id' => $userLogin->auth_group_id]);

            $loginLog = new ManagementUserLoginLog();
            $loginLog->deleteAll(['ip' => $loginLog->getRealIpAddr(), 'type' => ManagementUserLoginLog::TYPE_API]);

            #Get building info
            $domain = ApiHelper::getDomainOrigin();
            $building_info = BuildingClusterResponse::findOne(['domain' => $domain]);

            return [
                'success' => true,
                'access_token' => $jwt,
                'refresh_token' => $jwtRefresh,
                'auth_group' => $authGroup,
                'building_info' => $building_info,
                'info_user' => [
                    'id' => $userLogin->id,
                    'email' => $userLogin->email,
                    'first_name' => $userLogin->first_name,
                    'avatar' => $userLogin->avatar,
                    'is_send_email' => $userLogin->is_send_email,
                    'is_send_notify' => $userLogin->is_send_notify,
                ]
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
