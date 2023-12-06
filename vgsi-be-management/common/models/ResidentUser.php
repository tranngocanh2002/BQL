<?php

namespace common\models;

use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\helpers\ErrorCode;
use common\helpers\OneSignalApi;
use Firebase\JWT\JWT;
use frontend\models\ApartmentCreateForm;
use frontend\models\ApartmentMapResidentTypeUpdateForm;
use frontend\models\ApartmentMapResidentUserAddForm;
use frontend\models\ApartmentMapResidentUserUpdateForm;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\filters\RateLimitInterface;
use yii\helpers\Json;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "resident_user".
 *
 * @property int $id
 * @property string $phone
 * @property string $password
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $name_search
 * @property string $display_name
 * @property string $avatar
 * @property string $auth_key
 * @property string $notify_tags Tags gửi notify
 * @property int $gender giới tính : 0 - chưa xác định, 1 - nam, 2 - nữ
 * @property int $birthday ngày sinh
 * @property int $active_app 0 : chưa sử dụng app, 1 : đã sử dụng app
 * @property int $status 0 : chưa kích hoạt, 1 : đã kích hoạt, 2 : bị khóa
 * @property int $status_verify_phone 0 - chưa xác thực , 1 đã xác thực
 * @property int $status_verify_email 0 - chưa xác thực , 1 đã xác thực
 * @property int $is_deleted 0 : chưa xóa, 1 : đã xóa
 * @property int $is_send_email
 * @property int $is_send_notify
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_at
 * @property string $reason
 *
 *
 * @property string $cmtnd
 * @property int $ngay_cap_cmtnd
 * @property string $noi_cap_cmtnd
 * @property string $nationality
 * @property string $work
 * @property string $so_thi_thuc
 * @property int $ngay_het_han_thi_thuc
 * @property int $ngay_dang_ky_tam_chu
 * @property int $ngay_dang_ky_nhap_khau
 *
 * @property ApartmentMapResidentUser[] $apartmentMapResidentUsers
 */
class ResidentUser extends ActiveRecord implements IdentityInterface, RateLimitInterface
{

    const ACTIVE_APP = 1;

    const NOT_DELETED = 0;
    const DELETED = 1;

    public $confirm_password;
    public $old_password;

    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const IS_NOT_SEND_EMAIL = 0;
    const IS_SEND_EMAIL = 1;

    const IS_NOT_SEND_NOTIFY = 0;
    const IS_SEND_NOTIFY = 1;

    const STATUS_NOT_VERIFY = 0;
    const STATUS_VERIFY = 1;

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
        return 'resident_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'password'], 'required'],
            [['ngay_cap_cmtnd', 'ngay_dang_ky_nhap_khau', 'ngay_dang_ky_tam_chu', 'ngay_het_han_thi_thuc', 'is_send_notify', 'is_send_email', 'active_app', 'gender', 'birthday', 'status', 'status_verify_phone', 'status_verify_email', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at'], 'integer'],
            [['password', 'email', 'first_name', 'last_name'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 11, 'message' => Yii::t('common', "SĐT cho phép tối đa 10 ký tự")],
            [['cmtnd', 'noi_cap_cmtnd', 'nationality', 'work', 'avatar', 'auth_key', 'notify_tags' , 'name_search', 'display_name'], 'string', 'max' => 255],
            [['notify_tags', 'reason'], 'string'],
            [['password'], 'required', 'message' => Yii::t('common', 'invalid information, password has more than 6 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['connect', 'reset-password', 'change-password']],
            [['password'], 'string', 'min' => Yii::$app->params['length_password_random'], 'message' => Yii::t('common', 'invalid information, password has more than 6 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['connect', 'reset-password', 'change-password']],
            [['old_password'], 'required', 'message' => Yii::t('common', 'invalid information, password has more than 6 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['change-password']],
            [['confirm_password'], 'required', 'message' => Yii::t('common', 'invalid information, password confirm has more than 6 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()'), 'on' => ['connect', 'reset-password', 'change-password']],
            [
                'password',
                'compare',
                'compareAttribute' => 'confirm_password',
                'message' => Yii::t('common', 'invalid information, password confirm and password not match '),
                'on' => ['connect', 'reset-password', 'change-password']
            ],
            ['old_password', 'validateOldPassword'],
            [['phone'], 'validateMobile']
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('common', 'invalid phone number'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'password' => 'Password',
            'email' => 'Email',
            'display_name' => 'Display Name',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'name_search' => 'Name Search',
            'avatar' => 'Avatar',
            'auth_key' => 'Auth Key',
            'notify_tags' => 'Notify Tags',
            'gender' => 'Gender',
            'birthday' => 'Birthday',
            'status' => 'Status',
            'active_app' => 'Active App',
            'status_verify_phone' => 'Status Verify Phone',
            'status_verify_email' => 'Status Verify Email',
            'is_deleted' => 'Is Deleted',
            'is_send_email' => 'Is Send Email',
            'is_send_notify' => 'Is Send Notify',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'ngay_dang_ky_nhap_khau' => 'Ngày nhập khẩu',
            'ngay_dang_ky_tam_chu' => 'Ngày tạm trú',
            'ngay_het_han_thi_thuc' => 'Ngày hết hạn thị thực',
            'cmtnd' => 'Số chứng minh thư',
            'ngay_cap_cmtnd' => 'Ngày cấp chứng minh thư',
            'noi_cap_cmtnd' => 'Nơi cấp chứng minh thư',
            'nationality' => 'Quốc tịch',
            'work' => 'Công việc',
            'deleted_at' => 'Thời điểm xóa',
            'reason' => 'Lý do xóa',
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

        $userLoginToken = ResidentUserAccessToken::findOne(['token_hash' => md5($token), 'type' => 0]);
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

    public function updateNotifyTags()
    {
        //create/update notify tags in resident user
        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['resident_user_id' => $this->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
        $notifyTags = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
            $notifyTags = [
                'BUILDING_CLUSTER_' . $apartmentMapResidentUser->building_cluster_id,
                'BUILDING_AREA_' . $apartmentMapResidentUser->building_area_id,
                'APARTMENT_' . $apartmentMapResidentUser->apartment_id
            ];
        }
        $this->notify_tags = (!empty($notifyTags)) ? json_encode($notifyTags) : null;
        if (!$this->save()) {
            Yii::info($this->getErrors());
        };
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $name_search = CVietnameseTools::removeSigns2($this->first_name);
        $this->name_search = CVietnameseTools::toLower($name_search);
        return true;
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(!$insert){
            $oneSignalApi = new OneSignalApi();
            if ($this->is_deleted == ResidentUser::NOT_DELETED) {
                //nếu notify_tags thay đổi
                //thực hiện update tags sang onesignal
                if (isset($changedAttributes['notify_tags'])) {
                    $notify_tags_update = [];
                    if (!empty($this->notify_tags)) {
                        $notify_tags = Json::decode($this->notify_tags, true);
                        foreach ($notify_tags as $value) {
                            $notify_tags_update[$value] = $value;
                        }
                    }
                    Yii::info($notify_tags_update);
                    $residentUserDeviceTokens = ResidentUserDeviceToken::find()->where(['resident_user_id' => $this->id])->all();
                    foreach ($residentUserDeviceTokens as $residentUserDeviceToken) {
                        $oneSignalApi->updateDevice($residentUserDeviceToken->device_token, ['tags' => $notify_tags_update]);
                    }
                }
            } else {
                //nếu resident user bị update is_deleted = 1
                //thực hiện xóa tags trên onesignal
                $notify_tags_update = [];
                if (!empty($this->notify_tags)) {
                    $notify_tags = Json::decode($this->notify_tags, true);
                    foreach ($notify_tags as $value) {
                        $notify_tags_update[$value] = null;
                    }
                }
                Yii::info($notify_tags_update);
                $residentUserDeviceTokens = ResidentUserDeviceToken::find()->where(['resident_user_id' => $this->id])->all();
                foreach ($residentUserDeviceTokens as $residentUserDeviceToken) {
                    $oneSignalApi->updateDevice($residentUserDeviceToken->device_token, ['tags' => $notify_tags_update]);
                }
            }
        }
    }

    function afterDelete()
    {
        parent::afterDelete();
        //nếu resident user bị xóa
        //thực hiện xóa tags trên onesignal
    }

    public function getApartmentMapResidentUsers()
    {
        return $this->hasMany(ApartmentMapResidentUser::className(), ['resident_user_id' => 'id'])->where(['is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
    }

    /**
     * @param $params ApartmentMapResidentUserUpdateForm | ApartmentMapResidentUserAddForm | ApartmentCreateForm
     * @return array
     */
    public static function getOrCreate($params)
    {
        $residentUser = self::findByPhone($params->resident_phone);
        if (!$residentUser) {
            $residentUser = new ResidentUser();
            $residentUser->load($params->toArray(), '');
            $residentUser->phone = $params->resident_phone;
            $residentUser->setPassword(time());
            $residentUser->first_name = $params->resident_name;
            $residentUser->status = ResidentUser::STATUS_ACTIVE;
            $residentUser->cmtnd = $params->cmtnd;
            $residentUser->ngay_cap_cmtnd = $params->ngay_cap_cmtnd;
            $residentUser->noi_cap_cmtnd = $params->noi_cap_cmtnd;
            $residentUser->nationality = $params->nationality;
            $residentUser->work = $params->work;
            $residentUser->birthday = $params->birthday;
            $residentUser->gender = $params->gender;
            $residentUser->so_thi_thuc = $params->so_thi_thuc;
            $residentUser->ngay_het_han_thi_thuc = $params->ngay_het_han_thi_thuc;
            $residentUser->ngay_dang_ky_tam_chu = $params->ngay_dang_ky_tam_chu;
            $residentUser->ngay_dang_ky_nhap_khau = $params->ngay_dang_ky_nhap_khau;
            $residentUser->is_send_notify = ResidentUser::IS_SEND_NOTIFY;
            $residentUser->is_send_email = ResidentUser::IS_SEND_EMAIL;
            if (!empty($params->resident_avatar)) {
                $residentUser->avatar = $params->resident_avatar;
            }
            if (isset($params->resident_gender)) {
                $residentUser->gender = $params->resident_gender;
            }
            if (isset($params->gender)) {
                $residentUser->gender = $params->gender;
            }
            if (!empty($params->resident_birthday)) {
                $residentUser->birthday = $params->resident_birthday;
            }
            if (!empty($params->resident_email)) {
                $residentUser->email = $params->resident_email;
            }
            if (!$residentUser->save()) {
                Yii::error($residentUser->getErrors());
                return [
                    'success' => false,
                    'message' => Yii::t('common', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $residentUser->getErrors()
                ];
            }
        } else {
//            return [
//                'success' => true,
//                'residentUser' => $residentUser,
//            ];
//            Yii::warning('in update');
            $ck_update = 0;
//            $residentUser->load($params->toArray(), '');
//            $residentUser->first_name = $params->resident_name;
//            $ck_update = 1;
//            if (isset($params->resident_avatar)) {
//                $residentUser->avatar = $params->resident_avatar;
//                $ck_update = 1;
//            }
//            if (isset($params->resident_gender)) {
//                $residentUser->gender = $params->resident_gender;
//                $ck_update = 1;
//            }
//            if (isset($params->gender)) {
//                $residentUser->gender = $params->gender;
//                $ck_update = 1;
//            }
//            if (isset($params->resident_birthday)) {
//                $residentUser->birthday = $params->resident_birthday;
//                $ck_update = 1;
//            }
//            if (isset($params->resident_email)) {
//                $residentUser->email = $params->resident_email;
//                $ck_update = 1;
//            }
//            if (isset($params->nationality)) {
//                $residentUser->nationality = $params->nationality;
//                $ck_update = 1;
//            }
            if (
                !empty($params->cmtnd)
                || !empty($params->ngay_cap_cmtnd)
                || !empty($params->noi_cap_cmtnd)
                || !empty($params->nationality)
                || !empty($params->work)
                || !empty($params->so_thi_thuc)
                || !empty($params->ngay_het_han_thi_thuc)
                || !empty($params->ngay_dang_ky_tam_chu)
                || !empty($params->ngay_dang_ky_nhap_khau)
            ) {
                $ck_update = 1;
                $residentUser->cmtnd = $params->cmtnd;
                $residentUser->ngay_cap_cmtnd = $params->ngay_cap_cmtnd;
                $residentUser->noi_cap_cmtnd = $params->noi_cap_cmtnd;
                $residentUser->nationality = $params->nationality;
                $residentUser->work = $params->work;
                $residentUser->so_thi_thuc = $params->so_thi_thuc;
                $residentUser->ngay_het_han_thi_thuc = $params->ngay_het_han_thi_thuc;
                $residentUser->ngay_dang_ky_tam_chu = $params->ngay_dang_ky_tam_chu;
                $residentUser->ngay_dang_ky_nhap_khau = $params->ngay_dang_ky_nhap_khau;
            }
            if ($ck_update == 1) {
                Yii::warning('update true');
                $residentUser->save();
//                $residentUser->updateApartmentMap();
            }
        }
        return [
            'success' => true,
            'residentUser' => $residentUser,
        ];
    }

    /**
     * @param string $phone
     * @return ResidentUser
     */
    static function findByPhone($phone)
    {
        return self::find()->where(['phone' => $phone, 'is_deleted' => ResidentUser::NOT_DELETED])->one();
    }

    /**
     * @param string $phone
     * @return ResidentUser
     */
    static function findByPhoneIsDelete($phone)
    {
        return self::find()->where(['phone' => $phone, 'is_deleted' => ResidentUser::DELETED])->one();
    }

    /**
     * @param string $phone
     * @return ResidentUser
     */
    static function findAllByPhone($phone)
    {
        return self::find()->where(['phone' => $phone])->one();
    }

    public function updateApartmentMap()
    {
        $dataUpdate = [
            'resident_user_gender' => $this->gender,
            'resident_user_is_send_email' => $this->is_send_email,
            'resident_user_is_send_notify' => $this->is_send_notify,
            'resident_user_nationality' => $this->nationality,
            'install_app' => $this->active_app,
            'resident_user_phone' => $this->phone,
            'resident_user_email' => $this->email,
            'resident_user_first_name' => $this->first_name,
            'resident_user_last_name' => $this->last_name,
            'resident_name_search' => $this->name_search,
            'resident_user_birthday' => $this->birthday,
            'resident_user_avatar' => $this->avatar,
        ];

//        if(!empty($this->phone)){ $dataUpdate['resident_user_phone'] = $this->phone;}
//        if(!empty($this->email)){ $dataUpdate['resident_user_email'] = $this->email;}
//        if(!empty($this->first_name)){
//            $dataUpdate['resident_user_first_name'] = $this->first_name;
//            $dataUpdate['resident_name_search'] = $this->name_search;
//        }
//        if(!empty($this->last_name)){ $dataUpdate['resident_user_last_name'] = $this->last_name;}
//        if(!empty($this->birthday)){ $dataUpdate['resident_user_birthday'] = $this->birthday;}
//        if(!empty($this->avatar)){ $dataUpdate['resident_user_avatar'] = $this->avatar;}

        //thực hiện update thông tin vào bảng apartment map resident user nến có
        ApartmentMapResidentUser::updateAll(
            $dataUpdate,
            [
                'resident_user_id' => $this->id,
                'is_deleted' => ApartmentMapResidentUser::NOT_DELETED
            ]
        );

        //update thông tin chủ hộ
//        if(!empty($this->first_name)){
            Apartment::updateAll(
                [
                    'resident_user_name' => $this->first_name,
                    'resident_name_search' => $this->name_search
                ],
                ['resident_user_id' => $this->id]
            );
//        }

    }


    /*
     * Rate Limiting
     * giới hạn request theo customer
     */
    public $maxRequestsPerPeriod = 100;
    public $period = 10;
    protected $limitReached = false;

    public function getRateLimit($request, $action)
    {
        // [$this->maxRequestsPerPeriod] times per [$this->period]
//        return [$this->maxRequestsPerPeriod, $this->period];
        return [$this->maxRequestsPerPeriod, $this->period]; // $rateLimit requests per second
    }

    /**
     * Get request redis sorted set key
     *
     * @param \yii\base\Action $action
     * @param string $userId
     *
     * @return string
     */
    protected function getRedisKey($action, $userId)
    {
        return md5('limiter:' . $action->controller->route . ':' . $userId);
    }

    public function loadAllowance($request, $action)
    {
//        return [$this->allowance, $this->allowance_updated_at];

        $redis = Yii::$app->redis;
        // Redis storage key
        $key = $this->getRedisKey($action, $this->id);
        // Minimal actual timestamp value
        $time = time();
        $since = $time - $this->period;

        // Begin commands queue
        $redis->multi();
        // Remove expired values
        $redis->zremrangebyscore($key, 0, $since - 1);
        // Get requests count by period
        $redis->zcount($key, $since, $time);
        // Execute commands queue
        $result = $redis->exec();

        // If error - decline request
        if (!is_array($result) || !isset($result[1])) {
            return [0, $time];
        }
        // Count value
        $count = (int)$result[1];

        if ($count + 1 > $this->maxRequestsPerPeriod) {
            $this->limitReached = true;
        }

        return [$this->maxRequestsPerPeriod - $count, $time];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
//        $this->allowance = $allowance;
//        $this->allowance_updated_at = $timestamp;
//        $this->save();

        if ($this->limitReached) {
            $this->limitReached = false;

            // Don't save "Too many requests" request
            return;
        }

        $redis = Yii::$app->redis;
        // Redis storage key
        $key = $this->getRedisKey($action, $this->id);

        // Begin commands queue
        $redis->multi();
        // Sorted set member score is current timestamp
        $score = $timestamp;
        // Get random unique value for sorted set member value
        $member = uniqid(mt_rand(0, 9));
        $redis->zadd($key, $score, $member);
        // Set expire for key
        $redis->expire($key, $this->period);
        // Execute commands queue
        $redis->exec();
    }

    public function getFullName($apartment_id = null)
    {
        if(!empty($apartment_id)){
            $map = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment_id, 'resident_user_phone' => $this->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(!empty($map)){
                return $map->getFullName();
            }
        }
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
