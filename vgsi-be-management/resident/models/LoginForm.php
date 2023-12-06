<?php

namespace resident\models;

use common\helpers\AccountKit;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use common\models\ResidentUserAccessToken;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use Anhskohbo\AccountKit\Config;
use Anhskohbo\AccountKit\Client;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="LoginForm")
 * )
 */
class LoginForm extends Model
{
    /**
     * @SWG\Property(description="code")
     * @var string
     */
    public $code;

    /**
     * @SWG\Property(description="phone")
     * @var string
     */
    public $phone;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['phone'], 'string'],
        ];
    }

    /**
     * Logs in a user using the provided email and password.
     *
     * @return [] whether the user is logged in successfully
     */
    public function login()
    {
        $jwt = null;
        try {
            $whitelistPhone = Yii::$app->params['white_list_phone'];
            $is_whitelist = false;
            if (!empty($this->phone) && in_array($this->phone, $whitelistPhone)){
                $is_whitelist = true;
            }
            $access_token = null;
            if($is_whitelist === false){
                $accountKit = new AccountKit();
                $access_token = $accountKit->getAccessToken($this->code);
            }
            if (!empty($access_token) || $is_whitelist === true) {
                $userInfo = [];
                if($is_whitelist === true){
                    $userInfo['phone'] = CUtils::validateMsisdn($this->phone);
                }else{
                    $userInfo = $accountKit->getUserInfo($access_token);
                    $userInfo['phone'] = CUtils::validateMsisdn($userInfo['phone']);
                }
                if (!empty($userInfo['phone'])) {
                    $resident = ResidentUser::findByPhone($userInfo['phone']);
                    if (empty($resident)) {
                        $resident = new ResidentUser();
                        $resident->phone = $userInfo['phone'];
                        $resident->status_verify_phone = ResidentUser::STATUS_VERIFY;
                        $resident->email = $userInfo['email'];
                        $resident->status = ResidentUser::STATUS_ACTIVE;
                        $resident->active_app = ResidentUser::ACTIVE_APP;
                        $resident->setPassword(time());
                        if (!$resident->save()) {
                            return [
                                'success' => false,
                                'message' => Yii::t('resident', 'System busy'),
                                'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                                'error' => $resident->errors
                            ];
                        }
                    }
                    //đánh dấu cài app vào bảng apartmentMap
                    ApartmentMapResidentUser::updateAll(['install_app' => ApartmentMapResidentUser::INSTALL_APP], ['resident_user_phone' => $resident->phone]);

                    if ($resident->status != ResidentUser::STATUS_ACTIVE) {
                        throw new Exception(Yii::t('resident', "User not found or is activated"));
                    }
                    if ($resident->active_app !== ResidentUser::ACTIVE_APP) {
                        $resident->active_app = ResidentUser::ACTIVE_APP;
                        if (!$resident->save()) {
                            return [
                                'success' => false,
                                'message' => Yii::t('resident', 'System busy'),
                                'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                                'error' => $resident->errors
                            ];
                        }
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
                }
            }
            return [
                'success' => false,
                'message' => Yii::t('resident', 'Phone empty'),
            ];
        } catch (\Exception $e) {
            Yii::error($e, 'Errors login');
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
            ];
        }

    }
}
