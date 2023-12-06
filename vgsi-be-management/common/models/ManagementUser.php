<?php

namespace common\models;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use common\models\rbac\AuthAssignment;
use common\models\rbac\AuthGroup;
use common\models\rbac\AuthGroupResponse;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\web\IdentityInterface;
use Firebase\JWT\JWT;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\UnauthorizedHttpException;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "management_user".
 *
 * @property int $id
 * @property string $password
 * @property string $email
 * @property string $phone
 * @property string $first_name
 * @property string $last_name
 * @property string $avatar
 * @property string $auth_key
 * @property string $code_management_user
 * @property int $gender giới tính : 0 - chưa xác định, 1 - nam, 2 - nữ
 * @property int $birthday ngày sinh
 * @property int $parent_id
 * @property int $status 0 : chưa kích hoạt, 1 : đã kích hoạt, 2 : bị khóa
 * @property int $status_verify_phone 0 - chưa xác thực , 1 đã xác thực
 * @property int $status_verify_email 0 - chưa xác thực , 1 đã xác thực
 * @property int $auth_group_id
 * @property int $is_deleted 0 : chưa xóa, 1 : đã xóa
 * @property int $building_cluster_id
 * @property int $is_send_email
 * @property int $is_send_notify
 * @property int $role_type
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property BuildingCluster $buildingCluster
 * @property AuthGroup $authGroup
 * @property ManagementUserDeviceToken[] $managementUserDeviceTokens
 * @property string $fullName
 */
class ManagementUser extends ActiveRecord implements IdentityInterface
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    public $confirm_password;
    public $old_password;

    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_NOT_VERIFY = 0;
    const STATUS_VERIFY = 1;


    const IS_NOT_SEND_EMAIL = 0;
    const IS_SEND_EMAIL = 1;

    const IS_NOT_SEND_NOTIFY = 0;
    const IS_SEND_NOTIFY = 1;

    const DEFAULT_ADMIN = 0;
    const SUPPER_ADMIN = 1;

    const GENDER_0 = 0;
    const GENDER_1 = 1;
    const GENDER_2 = 2;
    public static $gender_list = [
//        self::GENDER_0 => 'Chưa xác định',
        self::GENDER_1 => 'Nam',
        self::GENDER_2 => 'Nữ'
    ];

    use \damirka\JWT\UserTrait;

    /**
     * Getter for secret key that's used for generation of JWT
     * @return string secret key used to generate JWT
     */
    protected static function getSecretKey()
    {
        return Yii::$app->params['jwt-config']['key'];
    }

    /**
     * Getter for encryption algorytm used in JWT generation and decoding
     * Override this method to set up other algorytm.
     * @return string needed algorytm
     */
    public static function getAlgo()
    {
        return Yii::$app->params['jwt-config']['alt'];
    }

    /**
     * Generate access token with jwt with params are loaded from config
     *
     * @param array $payload
     * @return string
     */
    public static function generateApiToken($payload = [])
    {
        $alg = Yii::$app->params['jwt-config']['alt']; // get encode algorithm from config file
        $key = Yii::$app->params['jwt-config']['key']; // get secret key from config file

        $jwt = JWT::encode($payload, $key, $alg); // generate access token

        return $jwt;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'management_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'required', 'on' => ['update', 'reset-password']],
            ['id', 'integer', 'on' => ['update', 'reset-password']],
            ['id', 'exist', 'on' => ['update', 'reset-password'], 'filter' => ['is_deleted' => self::NOT_DELETED]],
            [['password', 'email', 'building_cluster_id', 'auth_group_id'], 'required'],
            [['email', 'building_cluster_id'], 'unique', 'targetAttribute' => ['email'], 'message' => Yii::t('common', "Email has exist!"), 'on' => 'create'],
            [['gender', 'birthday', 'parent_id', 'status', 'status_verify_phone', 'status_verify_email', 'auth_group_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'building_cluster_id', 'is_send_notify', 'is_send_email', 'role_type'], 'integer'],
            [['password', 'first_name', 'last_name'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 50, 'message' => Yii::t('common', 'Email cho phép tối đa 50 ký tự')],
            [['phone'], 'string', 'max' => 11, 'message' => Yii::t('common', "SĐT cho phép tối đa 10 ký tự")],
            [['code_management_user'], 'string', 'max' => 10, 'message' => Yii::t('common', "Mã nhân viên cho phép tối đa 10 ký tự")],
            [['avatar', 'auth_key'], 'string', 'max' => 255],
            [['password'], 'required', 'message' => Yii::t('common', 'invalid information, password has more than 6 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['connect', 'reset-password', 'change-password']],
            [['password'], 'string', 'min' => Yii::$app->params['length_password_random'], 'message' => Yii::t('common', 'invalid information, password has more than 6 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['connect', 'reset-password', 'change-password']],
            [['old_password'], 'required', 'message' => Yii::t('common', 'invalid information, password has more than 6 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['change-password']],
            [['confirm_password'], 'required', 'message' => Yii::t('common', 'invalid information, password confirm has more than 6 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['connect', 'reset-password', 'change-password']],
            [
                'confirm_password',
                'compare',
                'compareAttribute' => 'password',
                'message' => Yii::t('common', 'invalid information, password confirm and password not match '),
                'on' => ['connect', 'reset-password', 'change-password']
            ],
            ['old_password', 'validateOldPassword'],
            [['phone'], 'validateMobile'],
            [['code_management_user'], 'unique'],
            ['email', 'match', 'pattern' => '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'message' => Yii::t('common', 'Email không đúng định dạng.')],
            ['code_management_user', 'match', 'pattern' => '/^[a-zA-Z0-9\sáàảãạÁÀẢÃẠâấầẩẫậÂẤẦẨẪẬăắằẳẵặĂẮẰẲẴẶéèẻẽẹÉÈẺẼẸêếềểễệÊẾỀỂỄỆíìỉĩịÍÌỈĨỊóòỏõọÓÒỎÕỌôốồổỗộÔỐỒỔỖỘơớờởỡợƠỚỜỞỠỢúùủũụÚÙỦŨỤưứừửữựƯỨỪỬỮỰýỳỷỹỵÝỲỶỸỴđĐ0-9]+$/u', 'message' => Yii::t('common', 'Mã nhân viên chỉ cho phép nhập chữ và số')],
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        if ($this->is_deleted == ManagementUser::DELETED) {
            return true;
        };
        if (!empty($this->$attribute)) {
            $this->$attribute = CUtils::validateMsisdn($this->$attribute);
            if (empty($this->$attribute)) {
                $this->addError($attribute, Yii::t('common', 'invalid phone number'));
            }
        } else {
            return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'password' => 'Password',
            'email' => 'Email',
            'phone' => 'Phone',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'avatar' => 'Avatar',
            'auth_key' => 'Auth Key',
            'gender' => 'Gender',
            'birthday' => 'Birthday',
            'parent_id' => 'Parent ID',
            'status' => 'Status',
            'status_verify_phone' => 'Status Verify Phone',
            'status_verify_email' => 'Status Verify Email',
            'auth_group_id' => 'Auth Group ID',
            'is_deleted' => 'Is Deleted',
            'building_cluster_id' => 'Building Cluster Id',
            'is_send_email' => 'Is Send Email',
            'is_send_notify' => 'Is Send Notify',
            'role_type' => 'Role Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'code_management_user' => 'Code management user',
        ];
    }


    /**
     * @inheritdoc
     */
    function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'time',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ]
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => ['updated_by'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ],
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        return static::find()->where(['id' => $id])->one();
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
        $secret = static::getSecretKey();

        $userLoginToken = ManagementUserAccessToken::findOne(['token_hash' => md5($token), 'type' => 0]);
        if (empty($userLoginToken)) {
            throw new UnauthorizedHttpException(Yii::t('common', 'Your request was made with invalid credentials'));
        }
        // Decode token and transform it into array.
        // Firebase\JWT\JWT throws exception if token can not be decoded
        try {
            $decoded = JWT::decode($token, $secret, [static::getAlgo()]);
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException(Yii::t('common', 'Your request was made with invalid credentials'));
        }
        static::$decodedToken = (array)$decoded;

        // If there's no jti param - exception
        if (!isset(static::$decodedToken['jti'])) {
            throw new UnauthorizedHttpException(Yii::t('common', 'Your request was made with invalid credentials'));
        }

        $userInfo = self::find()->where(['id' => static::$decodedToken['jti']])->one();
        if ($userInfo == null) {
            throw new UnauthorizedHttpException(Yii::t('common', 'Your request was made with invalid credentials'));
        }

        //check user với origin
        $origin = ApiHelper::getDomainOrigin();
        $buildingCluster = BuildingCluster::findOne(['id' => $userInfo->building_cluster_id, 'domain' => $origin]);
        if (empty($buildingCluster)) {
            throw new UnauthorizedHttpException(Yii::t('common', 'Your request was made with invalid credentials'));
        }

        return $userInfo;

    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash("$password");
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    function validateOldPassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = self::findOne(['id' => $this->id]);
            if ($user->validatePassword($this->$attribute) == false) {
                $this->addError($attribute, Yii::t('common', 'Old password invalid'));
            }
        }
    }


    /**
     * @param string $email
     */
    static function findByEmail($email)
    {
        $building = Yii::$app->building->BuildingCluster;
        if ($building) {
            return self::find()->where(['email' => $email, 'is_deleted' => ManagementUser::NOT_DELETED, 'building_cluster_id' => $building->id])->one();
        } else {
            return null;
        }
    }

    static function findByEmailAndClusterId($email, $cluster_id)
    {
        return self::find()->where(['email' => $email, 'is_deleted' => ManagementUser::NOT_DELETED, 'building_cluster_id' => $cluster_id])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthGroup()
    {
        return $this->hasOne(AuthGroup::className(), ['id' => 'auth_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagementUserDeviceTokens()
    {
        return $this->hasMany(ManagementUserDeviceToken::className(), ['management_user_id' => 'id']);
    }

    public function activeAccountSendEmail()
    {
        $verifyCode = VerifyCode::find()->where(['status' => VerifyCode::STATUS_NOT_VERIFY, 'type' => VerifyCode::TYPE_FORGOT_PASSWORD_MANAGEMENT_USER])->andWhere(['like', 'payload', $this->email])->one();
        /**
         * @var $verifyCode VerifyCode
         */
        if (!$verifyCode) {
            $verifyCode = new VerifyCode();
            $verifyCode->code = VerifyCode::generateCode();
            $verifyCode->type = VerifyCode::TYPE_FORGOT_PASSWORD_MANAGEMENT_USER;
            $verifyCode->expired_at = time() + 30 * 60;
            $verifyCode->payload = json_encode(['email' => $this->email]);
            if (!$verifyCode->save()) {
                return false;
            }
        } else {
            $verifyCode->code = VerifyCode::generateCode();
            $verifyCode->expired_at = time() + 30 * 60;
            if ($verifyCode->update() === false) {
                return false;
            }
        }
        $buildingCluster = BuildingCluster::findOne(['id' => $this->building_cluster_id]);
        $domain = (!empty($buildingCluster)) ? $buildingCluster->domain : '';
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'activeAccount-html'],
                    ['user' => $this, 'linkWeb' => $domain . '/user/createpassword?token=' . $verifyCode->code]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => $buildingCluster->name])
                ->setTo($this->email)
                ->setSubject('Kích hoạt tài khoản quản trị viên')
                ->send();
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }

    public function resetPasswordSendEmail($is_web = true,$email = null )
    {
//        $code = Yii::$app->security->generateRandomString(6);
        $code = CUtils::generateRandomNumber(6);
        $verifyCode = VerifyCode::find()->where(['status' => VerifyCode::STATUS_NOT_VERIFY, 'type' => VerifyCode::TYPE_FORGOT_PASSWORD_MANAGEMENT_USER])->andWhere(['like', 'payload', $this->email])->one();
        if (!$verifyCode) {
            $verifyCode = new VerifyCode();
            $verifyCode->code = $code;
            $verifyCode->type = VerifyCode::TYPE_FORGOT_PASSWORD_MANAGEMENT_USER;
            $verifyCode->expired_at = time() + 15 * 60;
            $verifyCode->payload = json_encode(['email' => $this->email]);
            if (!$verifyCode->save()) {
                return false;
            }
        } else {
            $verifyCode->code = $code;
            $verifyCode->expired_at = time() + 15 * 60;
            if ($verifyCode->update() === false) {
                return false;
            }
        }
        self::sendEmailOtp($verifyCode->code,$email);
    }

    public function sendEmailCreatePassword($password_new,$email = null){
        $buildingCluster = BuildingCluster::findOne(['id' => $this->building_cluster_id]);
        if(empty($email))
        {
            $email = $this->email;
        }
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'createPasswordNew-html'],
                    [
                        'buildingCluster' => $buildingCluster,
                        'user' => $this,
                        'password' => $password_new
                    ]
                )
                ->setFrom(['Banquanly_MeyHomes@tanadaithanh.vn' => $buildingCluster->name])
                ->setTo([$email => 'Tan A Dai Thanh'])
                ->setSubject('Tài khoản truy cập Web/App BQL')
                ->send();
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }

    private function sendEmailOtp($code,$email = null){
        $buildingCluster = BuildingCluster::findOne(['id' => $this->building_cluster_id]);
        if(empty($email))
        {
            $email = $this->email;
        }
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'forgotPasswordOtp-html'],
                    [
                        'buildingCluster' => $buildingCluster,
                        'user' => $this,
                        'otp' => $code
                    ]
                )
                ->setFrom(['Banquanly_MeyHomes@tanadaithanh.vn' => $buildingCluster->name])
                ->setTo([$email => 'Tan A Dai Thanh'])
                ->setSubject('Quên mật khẩu')
                ->send();
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }

    private function sendEmailLink($code){
        $buildingCluster = BuildingCluster::findOne(['id' => $this->building_cluster_id]);
        $domain = (!empty($buildingCluster)) ? $buildingCluster->domain : '';
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'forgotPassword-html'],
                    ['user' => $this, 'linkWeb' => $domain . '/user/createpassword?token=' . $code]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => $buildingCluster->name])
                ->setTo($this->email)
                ->setSubject('Thay đổi mật khẩu tài khoản')
                ->send();
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $password = $this->password;
        $this->setPassword($password);
        $resp = $this->save(false);
        return $resp;
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //set quyền rbac cho user theo auth_group
        //lấy list quyền mới
        if ($this->is_deleted == self::NOT_DELETED) {
//            if(isset($changedAttributes['auth_group_id'])){
            //xóa quyền cũ
            AuthAssignment::deleteAll(['user_id' => $this->id]);

            $authGroup = AuthGroup::findOne(['id' => $this->auth_group_id]);
            if ($authGroup) {
                //add quyền mới
                $auth = Yii::$app->authManager;
                foreach ($authGroup->getDataRoleArray() as $role) {
                    $perRole = $auth->getRole($role);
                    if (!empty($perRole)) {
                        $auth->assign($perRole, $this->id);
                    }
                }
            }
//            }
        } else {
            //xóa quyền cũ
            AuthAssignment::deleteAll(['user_id' => $this->id]);
        }
    }

    /*
     * Gửi email thông báo có phí cần duyệt
     * check chỉ gửi 1 lần trong 1 đợt gửi thông báo phí
     */
    public function activeFeeNotices()
    {
        $key = 'FeeNotices_' . $this->id;
        $send_check = Yii::$app->cache->get($key);
        if (!$send_check) {
            $exp_time = strtotime(date('Y-m-d 23:59:59', time())) - time();
            Yii::$app->cache->set($key, Json::encode(['management_user_id' => $this->id]), $exp_time);
            if(!empty($this->buildingCluster)){
                Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'activeFee-html'],
                        ['user' => $this]
                    )
                    ->setFrom([Yii::$app->params['supportEmail'] => $this->buildingCluster->name])
                    ->setTo($this->email)
                    ->setSubject('Thông báo có phí cần duyệt')
                    ->send();
                self::sendNotifyFee();
            }
        }
    }

    /*
     * gửi thông báo có phí cần duyệt
     */
    public function sendNotifyFee()
    {
        try {
            $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::SERVICE_PAYMENT_FEE, [$this->buildingCluster->name]);
            $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::SERVICE_PAYMENT_FEE, [$this->buildingCluster->name]);
            $data = [
                'type' => 'service_payment_fee',
                'action' => 'new_fee',
                'management_user_id' => $this->id
            ];
            $url = $this->buildingCluster->domain . '/main/service/detail/generate';
            $typeNotify = ManagementUserNotify::TYPE_SERVICE_PAYMENT_FEE;
            self::sendOneSignal($typeNotify, $title, $description, $title_en, $description_en, $data, $url);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }

    /*
     * gửi thông báo có yêu cầu thanh toán cần duyệt
     */
    public function sendNotifyPaymentGenCode($residentUser)
    {
        try {
            $title = $description = NotificationTemplate::vsprintf(NotificationTemplate::SERVICE_PAYMENT_GEN_CODE, [$residentUser->first_name]);
            $title_en = $description_en = NotificationTemplate::vsprintf(NotificationTemplate::SERVICE_PAYMENT_GEN_CODE_EN, [$residentUser->first_name]);
            $data = [
                'type' => 'service_payment_fee',
                'action' => 'new_fee',
                'management_user_id' => $this->id
            ];
            $url = $this->buildingCluster->domain . '/main/finance/payment-request';
            $typeNotify = ManagementUserNotify::TYPE_PAYMENT_GEN_CODE;
            self::sendOneSignal($typeNotify, $title, $description, $title_en, $description_en, $data, $url);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }

    private function sendOneSignal($typeNotify, $title, $description, $title_en, $description_en, $data, $url){
        $app_id = $this->buildingCluster->one_signal_app_id;

        //khởi tạo log cho từng management user
        $managementUserNotify = new ManagementUserNotify();
        $managementUserNotify->building_cluster_id = $this->building_cluster_id;
        $managementUserNotify->management_user_id = $this->id;
        $managementUserNotify->type = $typeNotify;
        $managementUserNotify->title = $title;
        $managementUserNotify->description = $description;
        $managementUserNotify->title_en = $title_en;
        $managementUserNotify->description_en = $description_en;
        if (!$managementUserNotify->save()) {
            Yii::error($managementUserNotify->getErrors());
        }
        //end log

        //gửi thông báo cho các user thuộc nhóm quyền này biết có phí cần duyệt
        $oneSignalApi = new OneSignalApi();
        //gửi thông báo theo device token
        $player_ids = [];
        foreach ($this->managementUserDeviceTokens as $managementUserDeviceToken) {
            $player_ids[] = $managementUserDeviceToken->device_token;
        }
        $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, $url, $app_id);
        //end gửi thông báo theo device token
    }

    public function checkNotifyReceiveConfig($channel, $type, $action = [])
    {
        //kiểm tra xem user này có cấu hình nhận thông báo tạo phí hay không không
        $query = [
            'building_cluster_id' => $this->building_cluster_id,
            'management_user_id' => $this->id,
            'channel' => $channel,
            'type' => $type,
        ];
        $query = array_merge($query, $action);
        return ManagementNotifyReceiveConfig::findOne($query);
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setTokenLogin($is_web = 0){
        // $is_web = $is_web ? 0 : 1 ;
        $jwt_config = Yii::$app->params['jwt-config'];
        $payload = array(
            'iss' => $jwt_config['iss'],
            'aud' => $jwt_config['aud'],
            'exp' => $jwt_config['time'],
            'jti' => $this->id,
        );
        $jwt = ManagementUser::generateApiToken($payload);

        //sinh refresh token
        $payloadRefresh = $payload;
        $payloadRefresh['time_refresh'] = $jwt_config['time_refresh'];
        $jwtRefresh = ManagementUser::generateApiToken($payloadRefresh);

        $tokenLogin = new ManagementUserAccessToken();
        $tokenLogin->management_user_id = $this->id;
        $tokenLogin->token = $jwt;
        $tokenLogin->is_web = (int)$is_web ?? 0;
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

        $tokenLoginRefresh = new ManagementUserAccessToken();
        $tokenLoginRefresh->management_user_id = $this->id;
        $tokenLoginRefresh->type = ManagementUserAccessToken::TYPE_REFRESH_TOKEN;
        $tokenLoginRefresh->token = $jwtRefresh;
        $tokenLoginRefresh->setTokenHash();
        $tokenLoginRefresh->is_web = (int)$is_web ?? 0;
        $tokenLoginRefresh->expired_at = $jwt_config['time_refresh'];
        if (!$tokenLoginRefresh->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', 'System busy'),
                'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                'error' => $tokenLoginRefresh->errors
            ];
        }
        return [
            'success' => true,
            'jwt' => $jwt,
            'jwtRefresh' => $jwtRefresh,
        ];
    }
}
