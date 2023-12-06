<?php

namespace resident\models;

use common\helpers\AccountKit;
use common\helpers\CgvVoiceOtp;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\QueueLib;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use common\models\ResidentUserAccessToken;
use common\models\ResidentUserDeviceToken;
use common\models\VerifyCode;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use Anhskohbo\AccountKit\Config;
use Anhskohbo\AccountKit\Client;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="LoginPasswordForm")
 * )
 */
class LoginPasswordForm extends Model
{
    /**
     * @SWG\Property(description="phone")
     * @var string
     */
    public $phone;

    /**
     * @SWG\Property(description="password")
     * @var string
     */
    public $password;

    private $_user;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'password'], 'required'],
            [['phone', 'password'], 'string'],
            ['phone', 'validateMobile'],
            ['password', 'validatePassword'],
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('resident', 'Số điện thoại không hợp lệ'));
        }
    }
    
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Tài khoản hoặc mật khẩu không đúng'));
            }
        }
    }

    public function login()
    {
        //check số điện thoại đã được gán căn hộ thì mới cho đăng nhập
        // $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $this->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE]);
        // if(empty($apartmentMapResidentUser)){
        //     return [
        //         'success' => false,
        //         'message' => Yii::t('resident', 'Số điện thoại chưa được khai báo căn hộ'),
        //     ];
        // }
        try {
            if ($this->_user->status != ResidentUser::STATUS_ACTIVE) {
                throw new Exception(Yii::t('resident', "Tài khoản không đúng hoặc chưa được kích hoạt"));
            }            
            if ($this->_user->active_app !== ResidentUser::ACTIVE_APP) {
                $this->_user->active_app = ResidentUser::ACTIVE_APP;
                if (!$this->_user->save()) {
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', 'System busy'),
                        'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                        'error' => $this->_user->errors
                    ];
                }
            }
            $jwt_config = Yii::$app->params['jwt-config'];
            $payload = array(
                'iss' => $jwt_config['iss'],
                'aud' => $jwt_config['aud'],
                'exp' => $jwt_config['time'],
                'jti' => $this->_user->id
            );
            $jwt = ResidentUser::generateApiToken($payload);

            //sinh refresh token
            $payloadRefresh = $payload;
            $payloadRefresh['time_refresh'] = $jwt_config['time_refresh'];
            $jwtRefresh = ResidentUser::generateApiToken($payloadRefresh);

            //Hủy đang nhập trên các thiết bị khác
            ResidentUserAccessToken::deleteAll(['resident_user_id' => $this->_user->id]);
            ResidentUserDeviceToken::deleteAll(['resident_user_id' => $this->_user->id]);

            //Tạo token login mới
            $tokenLogin = new ResidentUserAccessToken();
            $tokenLogin->resident_user_id = $this->_user->id;
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
            $tokenLoginRefresh->resident_user_id = $this->_user->id;
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
            ApartmentMapResidentUser::updateAll(['install_app' => ApartmentMapResidentUser::INSTALL_APP], ['resident_user_phone' => $this->_user->phone]);
            return [
                'success' => true,
                'access_token' => $jwt,
                'refresh_token' => $jwtRefresh,
                'info_user' => ResidentUserResponse::findOne(['id' => $this->_user->id]),
                'apartments' => ApartmentMapResidentUserResponse::find()->where(['resident_user_phone' => $this->_user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all()
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

    /**
     * Finds user by [[username]]
     *
     * @return ResidentUser|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = ResidentUser::findByPhone($this->phone);
        }
        return $this->_user;
    }
    /**
     * Finds user by [[username]]
     *
     * @return ResidentUser|null
     */
    protected function getUserLoginPasswordForm()
    {
        if ($this->_user === null) {
            $this->_user = ResidentUser::findByPhone($this->phone);
        }
        return $this->_user;
    }
}
