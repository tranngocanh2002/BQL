<?php

namespace common\models;

use Yii;
use common\helpers\CUtils;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\UserRole;
use backendQltt\models\LoggerUser;
use backendQltt\models\LogBehavior;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property integer $role_id
 * @property string $phone
 * @property string $confirm_password
 * @property string $code_user
 */
class User extends ActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    const MALE = 0;
    const FEMALE = 1;

    const NOT_LOGGED = 0;
    const LOGGED = 1;

    public $confirm_password;
    public $password;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public static $sex = [
        self::MALE => 'Nam',
        self::FEMALE => 'Nữ',
    ];

    public static $status = [
        self::STATUS_INACTIVE => 'Dừng hoạt động',
        self::STATUS_ACTIVE => 'Đang hoạt động',
    ];

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            // LogBehavior::className(),
            'log' => [
                'class' => LogBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'role_id', 'username', 'phone', 'full_name','code_user'], 'required'],
            [['role_id', 'sex', 'logged'], 'integer'],
            ['email', 'email', 'message' => Yii::t('common', 'Email Invalid')],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_INACTIVE]],
            [['username', 'password_hash', 'password_reset_token', 'avatar'], 'string', 'max' => 255],
            // ['phone', 'validatePhoneNumber'],
            ['birthday', 'safe'],
            [['password'], 'required', 'message' => Yii::t('common', 'Mật khẩu không được để trống'), 'on' => ['create', 'connect', 'reset-password', 'change-password']],
            [['password'], 'string', 'min' => 8, 'message' => Yii::t('common', 'invalid information, password has more than 8 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['create', 'connect', 'reset-password', 'change-password']],
            [['old_password'], 'required', 'message' => Yii::t('common', 'invalid information, password has more than 6 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['change-password']],
            [['confirm_password'], 'required', 'message' => Yii::t('common', 'Nhập lại mật khẩu không được để trống.'), 'on' => ['create', 'connect', 'reset-password', 'change-password']],
            [
                ['confirm_password'],
                'compare',
                'compareAttribute' => 'password',
                'message' => Yii::t('common', 'invalid information, password confirm and password not match '),
                'on' => ['create', 'connect', 'reset-password', 'change-password']
            ],
            [['full_name'], 'string', 'max' => 50],
            ['phone', 'match', 'pattern' => '/^(03[2-9]|05[2-9]|07[0-9]|08[1-9]|09[0-9])[0-9]{7}$/', 'message' => Yii::t('backendQltt', 'Số điện thoại không đúng định dạng.')],
            [['email'], 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('backendQltt', 'This email address has already been taken.')],
            [['phone'], 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('backendQltt', 'This phone has already been taken.')],
            ['phone', 'number', 'integerOnly' => true],
            ['full_name', 'match', 'pattern' => '/^[a-zA-Z\sáàảãạÁÀẢÃẠâấầẩẫậÂẤẦẨẪẬăắằẳẵặĂẮẰẲẴẶéèẻẽẹÉÈẺẼẸêếềểễệÊẾỀỂỄỆíìỉĩịÍÌỈĨỊóòỏõọÓÒỎÕỌôốồổỗộÔỐỒỔỖỘơớờởỡợƠỚỜỞỠỢúùủũụÚÙỦŨỤưứừửữựƯỨỪỬỮỰýỳỷỹỵÝỲỶỸỴđĐ]+$/u', 'message' => Yii::t('backendQltt', 'Họ và tên không được chứa ký tự đặc biệt và số')],
            ['email', 'match', 'pattern' => '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'message' => Yii::t('backendQltt', 'Email không đúng định dạng.')],
            [['email'], 'string', 'max' => 50],
            ['code_user', 'match', 'pattern' => '/^[a-zA-Z0-9\sáàảãạÁÀẢÃẠâấầẩẫậÂẤẦẨẪẬăắằẳẵặĂẮẰẲẴẶéèẻẽẹÉÈẺẼẸêếềểễệÊẾỀỂỄỆíìỉĩịÍÌỈĨỊóòỏõọÓÒỎÕỌôốồổỗộÔỐỒỔỖỘơớờởỡợƠỚỜỞỠỢúùủũụÚÙỦŨỤưứừửữựƯỨỪỬỮỰýỳỷỹỵÝỲỶỸỴđĐ0-9]+$/u', 'message' => Yii::t('common', 'Mã nhân viên không được chứa ký tự đặc biệt')],
            [['code_user'], 'string', 'max' => 10,'message' => Yii::t('common', 'invalid information, code manager has max 10 characters')],
            [['phone'], 'integer', 'message' => Yii::t('backendQltt', 'Invalid phone number.')],
            [['code_user'], 'unique', 'targetClass' => 'common\models\User', 'message' => Yii::t('backendQltt', 'Mã nhân viên đã tồn tại')],

        ];
    }

    public function validatePhoneNumber($attribute, $params)
    {
        $pattern = '/^(0|\+84)\d{9,10}$/'; // Vietnamese phone number pattern

        if (!preg_match($pattern, $this->$attribute)) {
            $this->addError($attribute, 'Invalid phone number.');
            throw new NotSupportedException('Invalid phone number.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'full_name' => Yii::t('common', 'Họ và tên'),
            'email' => Yii::t('common', 'Email'),
            'phone' => Yii::t('common', 'Số điện thoại'),
            'birthday' => Yii::t('common', 'Ngày sinh'),
            'sex' => Yii::t('common', 'Giới tính'),
            'role_id' => Yii::t('common', 'Nhóm quyền'),
            'status' => Yii::t('common', 'Trạng thái'),
            'code_user' => Yii::t('backendQltt', 'Mã nhân viên'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
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
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * 
     * @param type $role_id
     */
    public function setRole_id($role_id)
    {
        $this->role_id = $role_id;
    }

    /**
     * @return integer Role_id
     */
    function getRole_id()
    {
        return $this->role_id;
    }

    /**
     * 
     * @param type $phone
     */
    public function setPhone($phone)
    {
        $this->phone;
    }

    /**
     * 
     * @return type
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * 
     * @return type
     */
    public function getUserRole()
    {
        return $this->hasOne(UserRole::className(), ['id' => 'role_id']);
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword($isResetPassQltt = false)
    {
        $this->setPassword($this->password);

        if ($isResetPassQltt) {
            $this->logged = User::NOT_LOGGED;
        }

        return $this->save(false);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Reset password by email
     * @param $is_web
     * 
     * @return mixed 
     */
    public function resetPasswordSendEmail($is_web = true)
    {
        $session = Yii::$app->session;
        $code = CUtils::generateRandomNumber(6);

        $verifyCode = VerifyCode::find()->where(['status' => VerifyCode::STATUS_NOT_VERIFY, 'type' => VerifyCode::TYPE_FORGOT_PASSWORD_ADMIN])->andWhere(['like', 'payload', $this->email])->one();
        if (!$verifyCode) {
            $verifyCode = new VerifyCode();
            $verifyCode->code = $code;
            $verifyCode->type = VerifyCode::TYPE_FORGOT_PASSWORD_ADMIN;
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

        $session->set('time_otp_expired', $verifyCode->expired_at);

        return self::sendEmailOtp($verifyCode->code);
    }


    /**
     * Send Mail
     * @param $code
     * 
     * @return mixed
     */
    private function sendEmailOtp($code)
    {
        try {
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'forgotPasswordOtpAdmin-html'],
                    [
                        'user' => $this,
                        'otp' => $code
                    ]
                )
                ->setFrom(['Banquanly_MeyHomes@tanadaithanh.vn' => "Tân Á Đại Thành"])
                ->setTo($this->email)
                ->setSubject('Email của Admin BQL')
                ->send();
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return true; // tạm thời để là true vì config smtp mail đang config gặp exception, vẫn gửi mail nhưng bắn ra exception
        }
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->birthday && gettype($this->birthday) === 'string') {
            $this->birthday = strtotime(str_replace('/', '-', $this->birthday));
        }

        return true;
    }

    public function sendEmailResetPasswordByAdmin($password)
    {
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'reset-password-by-admin'],
                    [
                        'user' => $this,
                        'password' => $password,
                    ]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($this->email)
                ->setSubject('Tài khoản truy cập Web QLTT')
                ->send();
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }

    public function sendEmailCreatePassword($password, $subTitle = 'Tài khoản truy cập Web/App BQL')
    {
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'createUserNew'],
                    [
                        'user' => $this,
                        'password' => $password
                    ]
                )
                ->setFrom(['Banquanly_MeyHomes@tanadaithanh.vn' => "Tân Á Đại Thành"])
                ->setTo($this->email)
                ->setSubject($subTitle)
                ->send();
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return false;
        }
    }

    public static function getSexList()
    {
        return [
            self::MALE => Yii::t('backendQltt', 'Male'),
            self::FEMALE => Yii::t('backendQltt', 'Female'),
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('backendQltt', 'Đang hoạt động'),
            self::STATUS_INACTIVE => Yii::t('backendQltt', 'Dừng hoạt động'),
        ];
    }
}