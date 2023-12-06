<?php

namespace frontend\models;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use common\models\BuildingCluster;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserLoginLog;
use common\models\rbac\AuthGroupResponse;
use riskivy\captcha\CaptchaHelper;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="LoginForm")
 * )
 */
class LoginForm extends Model
{
    const CONFIRM_LOGIN = 1;
    /**
     * @SWG\Property(description="Email")
     * @var string
     */
    public $email;

    /**
     * @SWG\Property(description="Password")
     * @var string
     */
    public $password;

    /**
     * @SWG\Property(description="Confirm login: dùng cho login app, nếu = 0 sẽ check login trên thiết bị khác, nếu = 1 là xác nhận login")
     * @var integer
     */
    public $confirm_login;

    /**
     * @var $_user ManagementUser
     */
    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['confirm_login'], 'integer'],
            ['password', 'validatePassword'],
        ];
    }

    public function beforeValidate() {
        if (!parent::beforeValidate()) {
            return false;
        }
//        $HeaderKey = Yii::$app->params['HeaderKey'];
//        $api_key = Yii::$app->request->headers->get($HeaderKey['HEADER_API_KEY']);
//        if ($api_key == $HeaderKey['API_KEY_WEB']) {
//            $loginLog = new ManagementUserLoginLog();
//            $chk = $loginLog->validateCountLogin(ManagementUserLoginLog::TYPE_API);
//            if ($chk !== TRUE && empty($this->captcha_code)) {
//                $this->addError('captcha_code', Yii::t('frontend', 'Incorrect captcha code.'));
//                return false;
//            }
//        }
        return true;
    }

    public function afterValidate()
    {
        parent::afterValidate();
    //    $HeaderKey = Yii::$app->params['HeaderKey'];
    //    $api_key = Yii::$app->request->headers->get($HeaderKey['HEADER_API_KEY']);
    //    if ($api_key == $HeaderKey['API_KEY_WEB']) {
    //        $loginLog = new ManagementUserLoginLog();
    //        $loginLog->insertLogFailed(ManagementUserLoginLog::TYPE_API);
    //    }
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
            $user = ManagementUser::findByEmail($this->email);
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('frontend','Incorrect email or password.'));
            }
        }
    }

//    public function validateCaptcha($attribute, $params)
//    {
//        if (!$this->hasErrors()) {
//            if (!empty($this->captcha_code) && !(new CaptchaHelper())->verify($this->captcha_code)) {
//                $this->addError($attribute, Yii::t('frontend','Incorrect captcha code.'));
//            }
//        }
//    }

    /**
     * Logs in a user using the provided email and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login($is_web = true)
    {
        if (!$this->validate()) {
            $dataError = [
                'success' => false,
                'message' => Yii::t('frontend', 'Email or password invalid'),
                'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                'errors' => $this->errors,
            ];
//            if ($is_web) {
//                $loginLog = new ManagementUserLoginLog();
//                $chk = $loginLog->validateCountLogin(ManagementUserLoginLog::TYPE_API);
//                if ($chk !== TRUE) {
//                    $this->addError('email', $chk);
//                    $dataError['countCallFailed'] = 3;
//                    $dataError['captchaImage'] = (new CaptchaHelper())->generateImage();
//                    $dataError['errors'] = $this->errors;
//                }
//            }
            return $dataError;
        }

        $jwt = null;
        try {
            $userLogin = ManagementUser::findByEmail($this->email); // chi lấy thông tin cần thiết
            if (empty($userLogin)) {
                return [                    'success' => false,
                    'message' => Yii::t('frontend', 'Email or password invalid'),
                    'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                ];
            }
//            if (!$is_web) { // check login trên thiết bị khác
                if($this->confirm_login !== self::CONFIRM_LOGIN){
                    $checkTokenLogin = ManagementUserAccessToken::find()
                        ->where([
                            'management_user_id' => $userLogin->id,
                            'type'               => ManagementUserAccessToken::TYPE_ACCESS_TOKEN,
                            'is_web'             => $is_web
                        ])
                        ->one();
                    // $checkWebOrApp = ManagementUserDeviceToken::findOne(['management_user_id' => $userLogin->id, 'device_token' => $this->device_token,'type'=>$this->$is_web]);
                    if($checkTokenLogin){
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', 'Tài khoản hiện được đăng nhập vào một thiết bị khác. Bạn có muốn tiếp tục đăng nhập vào thiết bị này không?'),
                            'statusCode' => ErrorCode::ERROR_ALREADY_ACCEPT,
                        ];
                    }
                }else{
                    ManagementUserAccessToken::deleteAll(['management_user_id' => $userLogin->id,'is_web'=>$is_web]);
                }
//            }

            if ((int)$userLogin->status != ManagementUser::STATUS_ACTIVE) {
                throw new Exception(Yii::t('frontend', "User not found or is activated"));
            }

            $resTokenLogin = $userLogin->setTokenLogin($is_web);
            if($resTokenLogin['success'] == false){
                return $resTokenLogin;
            }else{
                $jwt = $resTokenLogin['jwt'];
                $jwtRefresh = $resTokenLogin['jwtRefresh'];
            }
            //lấy thông tin auth_group trả xuống
            $authGroup = AuthGroupResponse::findOne(['id' => $userLogin->auth_group_id]);

            $loginLog = new ManagementUserLoginLog();
            $loginLog->deleteAll(['ip' => $loginLog->getRealIpAddr(), 'type' => ManagementUserLoginLog::TYPE_API]);

            #Get building info
            $domain = ApiHelper::getDomainOrigin();
            $building_info = BuildingClusterResponse::findOne(['domain' => $domain]);
            if($this->confirm_login){
                $actionLogForm = new ActionLogForm(); 
                $actionLogForm ->user_id = $userLogin->id; 
                $actionLogForm->create(); 
            }
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
                'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
            ];
        }

    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = ManagementUser::findByEmail($this->email);
        }

        return $this->_user;
    }
}
