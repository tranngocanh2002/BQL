<?php

namespace common\models;

use common\helpers\CVietnameseTools;
use common\helpers\ErrorCode;
use common\helpers\QueueLib;
use DateTime;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use backendQltt\models\LoggerUser;
use backendQltt\models\LogBehavior;

/**
 * This is the model class for table "apartment_map_resident_user".
 *
 * @property int $id
 * @property int $apartment_id
 * @property string $apartment_name
 * @property string $apartment_code
 * @property string $apartment_parent_path
 * @property int $apartment_capacity
 * @property int $resident_user_id
 * @property string $resident_user_phone
 * @property string $resident_user_email
 * @property string $resident_user_first_name
 * @property string $resident_user_last_name
 * @property string $resident_name_search
 * @property string $resident_user_avatar
 * @property string $resident_user_nationality
 * @property int $resident_user_gender
 * @property int $resident_user_birthday
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $type 0 - thành viên, 1 - chủ hộ, 2 - khách
 * @property int $status  0 - chưa active, 1 - đã active
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $install_app
 * @property int $resident_user_is_send_email
 * @property int $resident_user_is_send_notify
 * @property int $type_relationship
 * @property string $apartment_short_name
 * @property string $cmtnd
 * @property string $noi_cap_cmtnd
 * @property int $ngay_cap_cmtnd
 * @property string $work
 * @property string $so_thi_thuc
 * @property int $ngay_dang_ky_nhap_khau
 * @property int $ngay_dang_ky_tam_chu
 * @property int $ngay_het_han_thi_thuc
 * @property int $last_active
 * @property int $is_deleted
 * @property int $deleted_at
 * @property int $is_check_cmtnd
 *
 * @property Apartment $apartment
 * @property ResidentUser $resident
 * @property BuildingCluster $buildingCluster
 * @property BuildingArea $buildingArea
 * @property ResidentUserDeviceToken[] $residentUserDeviceTokens
 */
class ApartmentMapResidentUser extends \yii\db\ActiveRecord
{

    const NOT_DELETED = 0;
    const DELETED = 1;
    const LAST_NOT_ACTIVE = 0;
    const LAST_ACTIVE = 1;

    const IS_MEMBER = 1;

    const TYPE_MEMBER = 0;
    const TYPE_HEAD_OF_HOUSEHOLD = 1;
    const TYPE_GUEST = 2;
    const TYPE_MEMBER_GUEST = 3;

    public static $type_list = [
        self::TYPE_MEMBER => 'Gia đình chủ hộ',
        self::TYPE_HEAD_OF_HOUSEHOLD => 'Chủ hộ',
        self::TYPE_GUEST => 'Khách thuê',
        self::TYPE_MEMBER_GUEST => 'Gia đình khách thuê',
    ];

    public static $type_list_text = [
        'Gia đình chủ hộ' => self::TYPE_MEMBER,
        'Chủ hộ' => self::TYPE_HEAD_OF_HOUSEHOLD,
        'Khách thuê' => self::TYPE_GUEST,
        'Gia đình khách thuê' => self::TYPE_MEMBER_GUEST,
    ];

    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const NOT_INSTALL_APP = 0;
    const INSTALL_APP = 1;

    const IS_NOT_SEND_EMAIL = 0;
    const IS_SEND_EMAIL = 1;

    const IS_NOT_SEND_NOTIFY = 0;
    const IS_SEND_NOTIFY = 1;

    const TYPE_RELATIONSHIP_HEAD_OF_HOUSEHOLD = 0;
    const TYPE_RELATIONSHIP_GRANDPARENTS = 1;
    const TYPE_RELATIONSHIP_PARENTS = 2;
    const TYPE_RELATIONSHIP_MATE = 3;
    const TYPE_RELATIONSHIP_SON = 4;
    const TYPE_RELATIONSHIP_SIBLINGS = 5;
    const TYPE_RELATIONSHIP_FRIEND = 6;
//    const TYPE_RELATIONSHIP_RENTER = 7;
    const TYPE_RELATIONSHIP_OTHER = 7;

    public static $type_relationship_list = [
        self::TYPE_RELATIONSHIP_HEAD_OF_HOUSEHOLD => "Chủ Hộ",
        self::TYPE_RELATIONSHIP_GRANDPARENTS => "Ông/Bà",
        self::TYPE_RELATIONSHIP_PARENTS => "Bố/Mẹ",
        self::TYPE_RELATIONSHIP_MATE => "Vợ/Chồng",
        self::TYPE_RELATIONSHIP_SON => "Con",
        self::TYPE_RELATIONSHIP_SIBLINGS => "Anh/Chị/Em",
        self::TYPE_RELATIONSHIP_FRIEND => "Bạn",
//        self::TYPE_RELATIONSHIP_RENTER => "Khách thuê",
        self::TYPE_RELATIONSHIP_OTHER => "Khác",
    ];

    public static $type_relationship_en_list = [
        self::TYPE_RELATIONSHIP_HEAD_OF_HOUSEHOLD => "Owner",
        self::TYPE_RELATIONSHIP_GRANDPARENTS => "Grandparents",
        self::TYPE_RELATIONSHIP_PARENTS => "Father/Mother",
        self::TYPE_RELATIONSHIP_MATE => "Wife/Husband",
        self::TYPE_RELATIONSHIP_SON => "Children",
        self::TYPE_RELATIONSHIP_SIBLINGS => "Siblings",
        self::TYPE_RELATIONSHIP_FRIEND => "Friends",
//        self::TYPE_RELATIONSHIP_RENTER => "Renter",
        self::TYPE_RELATIONSHIP_OTHER => "Others",
    ];

    public static $type_relationship_list_text = [
        "Chủ Hộ" => self::TYPE_RELATIONSHIP_HEAD_OF_HOUSEHOLD,
        "Ông/Bà" => self::TYPE_RELATIONSHIP_GRANDPARENTS,
        "Bố/Mẹ" => self::TYPE_RELATIONSHIP_PARENTS,
        "Vợ/Chồng" => self::TYPE_RELATIONSHIP_MATE,
        "Con" => self::TYPE_RELATIONSHIP_SON,
        "Anh/Chị/Em" => self::TYPE_RELATIONSHIP_SIBLINGS,
        "Bạn" => self::TYPE_RELATIONSHIP_FRIEND,
        "Khác" => self::TYPE_RELATIONSHIP_OTHER,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apartment_map_resident_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'resident_user_gender',
                'resident_user_birthday',
                'apartment_id',
                'install_app',
                'resident_user_is_send_email',
                'resident_user_is_send_notify',
                'type_relationship',
                'apartment_capacity',
                'resident_user_id',
                'building_cluster_id',
                'building_area_id',
                'type',
                'status',
                'ngay_cap_cmtnd',
                'ngay_dang_ky_nhap_khau',
                'ngay_dang_ky_tam_chu',
                'ngay_het_han_thi_thuc',
                'last_active',
                'is_deleted',
                'deleted_at',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_check_cmtnd'
            ], 'integer'],
            [[
                'apartment_name',
                'apartment_code',
                'apartment_parent_path',
                'resident_user_phone',
                'resident_user_email',
                'resident_user_first_name',
                'resident_user_last_name',
                'resident_name_search',
                'resident_user_avatar',
                'resident_user_nationality',
                'cmtnd',
                'noi_cap_cmtnd',
                'work',
                'so_thi_thuc',
            ], 'string'],
            [['apartment_short_name'], 'string', 'max' => 20],
            [['resident_user_phone'], 'string', 'max' => 11, 'message' => Yii::t('common', "SĐT cho phép tối đa 10 ký tự")],
            [['resident_user_email'], 'email', 'message' => Yii::t('common', 'Email không đúng định dạng')],
            // [['type'], 'required', 'message' => Yii::t('common', 'Vai trò không được để trống')],
            // [['type_relationship'], 'required', 'message' => Yii::t('common', 'Mối quan hệ không được để trống')],
        ];
    }

    public function validateDateOfBirth($str_date)
    {
        $dateTime = DateTime::createFromFormat('d/m/Y', $str_date);
        $errors = DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return false;
        }
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'apartment_id' => Yii::t('common','Apartment ID'),
            'apartment_capacity' => Yii::t('common','Apartment Capacity'),
            'apartment_name' => Yii::t('common','Apartment Name'),
            'apartment_code' => Yii::t('common','Apartment Code'),
            'apartment_parent_path' => Yii::t('common','Apartment Parent Path'),
            'apartment_short_name' => Yii::t('common', 'Apartment Short Name'),
            'resident_user_id' => Yii::t('common','Resident User ID'),
            'resident_user_phone' => Yii::t('common','Resident User Phone'),
            'resident_user_email' => Yii::t('common','Resident User Email'),
            'resident_user_first_name' => Yii::t('common','Resident User First Name'),
            'resident_user_last_name' => Yii::t('common','Resident User Last Name'),
            'resident_name_search' => Yii::t('common','Resident Name Search'),
            'resident_user_gender' => Yii::t('common','Resident User Gender'),
            'resident_user_birthday' => Yii::t('common','Resident User Birthday'),
            'resident_user_avatar' => Yii::t('common','Resident User Avatar'),
            'resident_user_nationality' => Yii::t('common','Resident User Nationality'),
            'building_cluster_id' => Yii::t('common','Building Cluster ID'),
            'building_area_id' => Yii::t('common','Building Area ID'),
            'type' => Yii::t('common','Type'),
            'type_relationship' => Yii::t('common','Type Relationship'),
            'resident_user_is_send_email' => Yii::t('common','Resident User Is Send Email'),
            'resident_user_is_send_notify' => Yii::t('common','Resident User Is Send Notify'),
            'status' => Yii::t('common','Status'),
            'install_app' => Yii::t('common','Install App'),
            'ngay_cap_cmtnd' => Yii::t('common','Ngày cấp cmtnd'),
            'ngay_dang_ky_nhap_khau' => Yii::t('common','Ngày đăng ký nhập khẩu'),
            'ngay_dang_ky_tam_chu' => Yii::t('common','Ngày đăng ký tạm chú'),
            'ngay_het_han_thi_thuc' => Yii::t('common','Ngày hết hạn thị thực'),
            'cmtnd' => Yii::t('common','Cmtnd'),
            'noi_cap_cmtnd' => Yii::t('common','Nơi cấp cmtnd'),
            'work' => Yii::t('common','Công việc'),
            'so_thi_thuc' => Yii::t('common','Số thị thực'),
            'last_active' => Yii::t('common','Last Active'),
            'is_deleted' => Yii::t('common','Is Deleted'),
            'deleted_at' => Yii::t('common','Deleted At'),
            'created_at' => Yii::t('common','Created At'),
            'updated_at' => Yii::t('common','Updated At'),
            'created_by' => Yii::t('common','Created By'),
            'updated_by' => Yii::t('common','Updated By'),
            'building_name_and_total_apartment' => Yii::t('backendQltt','Tên dự án/ Số lượng bất động sản'),
            'first_name' => Yii::t('backendQltt','Họ'),
            'last_name' => Yii::t('backendQltt','Tên'),
            'phone' => Yii::t('backend','Phone'),
            'is_check_cmtnd' => Yii::t('backend','Checkbox cmtnd'),
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
            // 'log' => [
            //     'class' => LogBehavior::class,
            // ],
        ];
    }

    /**
     * @param $apartment Apartment
     * @param $residentUser ResidentUser
     * @param $param
     * @return array
     */
    public static function getOrCreate($apartment, $residentUser, $param)
    {
        if(empty($residentUser)){
            $resident_user_phone = $param->resident_phone ?? null;
        }else{
            $resident_user_phone = $residentUser->resident_user_phone;
        }
        if(10 == strlen($resident_user_phone))
        {
            $resident_user_phone =  '84'.substr($resident_user_phone, 1);
        }
        $fakeSdtValidate = preg_replace("/^84/", '0', $resident_user_phone);
        // return [
        //     'success' => false,
        //     'message' => Yii::t('common', "Invalid data"),
        //     'data' => $resident_user_phone 
        // ];
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment->id, 'resident_user_phone' => $resident_user_phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        $apartmentMapResidentUserFakeSdtValidate = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment->id, 'resident_user_phone' => $fakeSdtValidate, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        $is_new = false;
        if (empty($apartmentMapResidentUser) && empty($apartmentMapResidentUserFakeSdtValidate)) {
            $apartmentMapResidentUser = new ApartmentMapResidentUser();
            $apartmentMapResidentUser->apartment_id         = $apartment->id;
            $apartmentMapResidentUser->apartment_name       = $apartment->name;
            $apartmentMapResidentUser->apartment_capacity   = (int)$apartment->capacity;
            $apartmentMapResidentUser->apartment_code       = $apartment->code;
            $apartmentMapResidentUser->apartment_parent_path= $apartment->parent_path;
            $apartmentMapResidentUser->building_cluster_id  = $apartment->building_cluster_id;
            $apartmentMapResidentUser->building_area_id     = $apartment->building_area_id;
            $is_new = true;
        }
        if(empty($residentUser)){
            $apartmentMapResidentUser->resident_user_phone       = $resident_user_phone ?? null;
            $apartmentMapResidentUser->resident_user_email       = $param->resident_email ?? null;
            $apartmentMapResidentUser->resident_user_first_name  = $param->resident_name ?? null;
            $apartmentMapResidentUser->resident_user_nationality = $param->nationality ?? null;
            $apartmentMapResidentUser->resident_user_gender      = $param->gender ?? null;
            $apartmentMapResidentUser->resident_user_birthday    = $param->birthday ?? null;
            $apartmentMapResidentUser->ngay_cap_cmtnd            = $param->ngay_cap_cmtnd ?? null;
            $apartmentMapResidentUser->ngay_dang_ky_nhap_khau    = $param->ngay_dang_ky_nhap_khau ?? null;
            $apartmentMapResidentUser->ngay_dang_ky_tam_chu      = $param->ngay_dang_ky_tam_chu ?? null;
            $apartmentMapResidentUser->ngay_het_han_thi_thuc     = $param->ngay_het_han_thi_thuc ?? null;
            $apartmentMapResidentUser->cmtnd                     = $param->cmtnd ?? null;
            $apartmentMapResidentUser->noi_cap_cmtnd             = $param->noi_cap_cmtnd ?? null;
            $apartmentMapResidentUser->work                      = $param->work ?? null;
            $apartmentMapResidentUser->so_thi_thuc               = $param->so_thi_thuc ?? null;
            $apartmentMapResidentUser->is_check_cmtnd            = $param->is_check_cmtnd ?? null;
        }else{
            $apartmentMapResidentUser->resident_user_id = $residentUser->id;
            $apartmentMapResidentUser->resident_user_phone = $resident_user_phone;
            $apartmentMapResidentUser->resident_user_email = $residentUser->resident_user_email;
            $apartmentMapResidentUser->resident_user_first_name = $residentUser->resident_user_first_name;
            $apartmentMapResidentUser->resident_user_last_name = $residentUser->resident_user_last_name;
            $apartmentMapResidentUser->resident_user_avatar = $residentUser->resident_user_avatar;
            $apartmentMapResidentUser->resident_user_nationality = $residentUser->resident_user_nationality;
            $apartmentMapResidentUser->resident_user_gender = $residentUser->resident_user_gender;
            $apartmentMapResidentUser->resident_user_birthday = $residentUser->resident_user_birthday;
            $apartmentMapResidentUser->resident_user_is_send_email = $residentUser->resident_user_is_send_email;
            $apartmentMapResidentUser->resident_user_is_send_notify = $residentUser->resident_user_is_send_notify;
            $apartmentMapResidentUser->install_app = $residentUser->install_app;
            $apartmentMapResidentUser->is_check_cmtnd            = $param->is_check_cmtnd ?? null;
        }
        // tìm kiếm sđt có cài app hay chưa
        $apartmentMapResidentUserSearch = ApartmentMapResidentUser::findOne(['resident_user_phone' => $resident_user_phone,'install_app' => ApartmentMapResidentUser::INSTALL_APP ,'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);

        if(!empty($apartmentMapResidentUserSearch)){
            $apartmentMapResidentUser->install_app =  ApartmentMapResidentUser::INSTALL_APP; 
        }
        if(isset($param->type)){
            $apartmentMapResidentUser->type = $param->type;
        }
        if(isset($param->type_relationship) && $param->type_relationship !== null){
            $apartmentMapResidentUser->type_relationship = $param->type_relationship;
        }
        if (!$apartmentMapResidentUser->save()) {
            Yii::error($apartmentMapResidentUser->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $apartmentMapResidentUser->getErrors()
            ];
        }
        //sys data to department
        $apartmentMapResidentUser->syncDataToApartmentMap();
        if($is_new == true){
            $apartmentMapResidentUser->addHistory();
        }
        //update tags user
        if(!empty($apartmentMapResidentUser->resident_user_id)){
            $resident = ResidentUser::findOne(['phone' => $apartmentMapResidentUser->resident_user_phone]);
            $resident->updateNotifyTags();
        }

        return [
            'success' => true,
            'apartmentMapResidentUser' => $apartmentMapResidentUser,
            'is_new' => $is_new
        ];
    }

        /**
     * @param $apartment Apartment
     * @param $residentUser ResidentUser
     * @param $param
     * @return array
     */
    public static function addGetOrCreate($apartment, $residentUser, $param)
    {
        if(empty($residentUser)){
            $resident_user_phone = $param->resident_phone ?? null;
        }else{
            $resident_user_phone = $residentUser->phone;
        }
        if(10 == strlen($resident_user_phone))
        {
            $resident_user_phone =  '84'.substr($resident_user_phone, 1);
        }
        $fakeSdtValidate = preg_replace("/^84/", '0', $resident_user_phone);
        // return [
        //     'success' => false,
        //     'message' => Yii::t('common', "Invalid data"),
        //     'data' => $resident_user_phone 
        // ];
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $resident_user_phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        $apartmentMapResidentUserNew = new ApartmentMapResidentUser();
        $apartmentMapResidentUserNew->apartment_id         = $apartment->id;
        $apartmentMapResidentUserNew->apartment_name       = $apartment->name;
        $apartmentMapResidentUserNew->apartment_capacity   = (int)$apartment->capacity;
        $apartmentMapResidentUserNew->apartment_code       = $apartment->code;
        $apartmentMapResidentUserNew->apartment_parent_path= $apartment->parent_path;
        $apartmentMapResidentUserNew->building_cluster_id  = $apartment->building_cluster_id;
        $apartmentMapResidentUserNew->building_area_id     = $apartment->building_area_id;
        $is_new = true;
        $apartmentMapResidentUserNew->resident_user_phone       = $resident_user_phone ?? null;
        $apartmentMapResidentUserNew->resident_user_email       = $apartmentMapResidentUser->resident_email ?? null;
        $apartmentMapResidentUserNew->resident_user_first_name  = $apartmentMapResidentUser->resident_name ?? null;
        $apartmentMapResidentUserNew->resident_user_nationality = $apartmentMapResidentUser->nationality ?? null;
        $apartmentMapResidentUserNew->resident_user_gender      = $apartmentMapResidentUser->gender ?? null;
        $apartmentMapResidentUserNew->resident_user_birthday    = $apartmentMapResidentUser->birthday ?? null;
        $apartmentMapResidentUserNew->ngay_cap_cmtnd            = $apartmentMapResidentUser->ngay_cap_cmtnd ?? null;
        $apartmentMapResidentUserNew->ngay_dang_ky_nhap_khau    = $apartmentMapResidentUser->ngay_dang_ky_nhap_khau ?? null;
        $apartmentMapResidentUserNew->ngay_dang_ky_tam_chu      = $apartmentMapResidentUser->ngay_dang_ky_tam_chu ?? null;
        $apartmentMapResidentUserNew->ngay_het_han_thi_thuc     = $apartmentMapResidentUser->ngay_het_han_thi_thuc ?? null;
        $apartmentMapResidentUserNew->cmtnd                     = $apartmentMapResidentUser->cmtnd ?? null;
        $apartmentMapResidentUserNew->noi_cap_cmtnd             = $apartmentMapResidentUser->noi_cap_cmtnd ?? null;
        $apartmentMapResidentUserNew->work                      = $apartmentMapResidentUser->work ?? null;
        $apartmentMapResidentUserNew->so_thi_thuc               = $apartmentMapResidentUser->so_thi_thuc ?? null;
    
        // tìm kiếm sđt có cài app hay chưa
        $apartmentMapResidentUserSearch = ApartmentMapResidentUser::findOne(['resident_user_phone' => $resident_user_phone,'install_app' => ApartmentMapResidentUser::INSTALL_APP ,'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);

        if(!empty($apartmentMapResidentUserSearch)){
            $apartmentMapResidentUserNew->install_app =  ApartmentMapResidentUser::INSTALL_APP; 
        }
        if(isset($param->type)){
            $apartmentMapResidentUserNew->type = $param->type;
        }
        if(isset($param->type_relationship) && $param->type_relationship !== null){
            $apartmentMapResidentUserNew->type_relationship = $param->type_relationship;
        }
        if (!$apartmentMapResidentUserNew->save()) {
            Yii::error($apartmentMapResidentUser->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $apartmentMapResidentUser->getErrors()
            ];
        }
        //sys data to department
        $apartmentMapResidentUser->syncDataToApartmentMap();
        if($is_new == true){
            $apartmentMapResidentUser->addHistory();
        }
        //update tags user
        if(!empty($apartmentMapResidentUser->resident_user_id)){
            $resident = ResidentUser::findOne(['id' => $apartmentMapResidentUser->resident_user_id]);
            $resident->updateNotifyTags();
        }

        return [
            'success' => true,
            'apartmentMapResidentUser' => $apartmentMapResidentUser,
            'is_new' => $is_new
        ];
    }

        /**
     * @param $apartment Apartment
     * @param $residentUser ResidentUser
     * @param $param
     * @return array
     */
    public static function getOrUpdate($apartment, $residentUser, $param)
    {
        if(empty($residentUser)){
            $resident_user_phone = $param->resident_phone ?? null;
        }else{
            $resident_user_phone = $residentUser->phone;
        }
        if(10 == strlen($resident_user_phone))
        {
            $resident_user_phone =  '84'.substr($resident_user_phone, 1);
        }
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment->id, 'resident_user_phone' => $resident_user_phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if (!empty($apartmentMapResidentUser)) {
            $resident_email = $apartmentMapResidentUser->resident_user_email;
            $resident_name = $apartmentMapResidentUser->resident_user_first_name;
            $nationality = $apartmentMapResidentUser->resident_user_nationality;
            $gender = $apartmentMapResidentUser->resident_user_gender;
            $birthday = $apartmentMapResidentUser->resident_user_birthday;
            $ngay_cap_cmtnd = $apartmentMapResidentUser->ngay_cap_cmtnd;
            $ngay_dang_ky_nhap_khau = $apartmentMapResidentUser->ngay_dang_ky_nhap_khau;
            $ngay_dang_ky_tam_chu = $apartmentMapResidentUser->ngay_dang_ky_tam_chu;
            $ngay_het_han_thi_thuc = $apartmentMapResidentUser->ngay_het_han_thi_thuc;
            $cmtnd = $apartmentMapResidentUser->cmtnd;
            $noi_cap_cmtnd = $apartmentMapResidentUser->noi_cap_cmtnd;
            $work = $apartmentMapResidentUser->work;
            $so_thi_thuc = $apartmentMapResidentUser->so_thi_thuc;
            $apartmentMapResidentUser->apartment_id         = $apartment->id;
            $apartmentMapResidentUser->apartment_name       = $apartment->name;
            $apartmentMapResidentUser->apartment_capacity   = (int)$apartment->capacity;
            $apartmentMapResidentUser->apartment_code       = $apartment->code;
            $apartmentMapResidentUser->apartment_parent_path= $apartment->parent_path;
            $apartmentMapResidentUser->building_cluster_id  = $apartment->building_cluster_id;
            $apartmentMapResidentUser->building_area_id     = $apartment->building_area_id;
            $is_new = true;
            $apartmentMapResidentUser->resident_user_phone       = $resident_user_phone ?? null;
            $apartmentMapResidentUser->resident_user_email       = $resident_email ?? null;
            $apartmentMapResidentUser->resident_user_first_name  = $resident_name ?? null;
            $apartmentMapResidentUser->resident_user_nationality = $nationality ?? null;
            $apartmentMapResidentUser->resident_user_gender      = $gender ?? null;
            $apartmentMapResidentUser->resident_user_birthday    = $birthday ?? null;
            $apartmentMapResidentUser->ngay_cap_cmtnd            = $ngay_cap_cmtnd ?? null;
            $apartmentMapResidentUser->ngay_dang_ky_nhap_khau    = $ngay_dang_ky_nhap_khau ?? null;
            $apartmentMapResidentUser->ngay_dang_ky_tam_chu      = $ngay_dang_ky_tam_chu ?? null;
            $apartmentMapResidentUser->ngay_het_han_thi_thuc     = $ngay_het_han_thi_thuc ?? null;
            $apartmentMapResidentUser->cmtnd                     = $cmtnd ?? null;
            $apartmentMapResidentUser->noi_cap_cmtnd             = $noi_cap_cmtnd ?? null;
            $apartmentMapResidentUser->work                      = $work ?? null;
            $apartmentMapResidentUser->so_thi_thuc               = $so_thi_thuc ?? null;
        }
        
    
        // tìm kiếm sđt có cài app hay chưa
        $apartmentMapResidentUserSearch = ApartmentMapResidentUser::findOne(['resident_user_phone' => $resident_user_phone,'install_app' => ApartmentMapResidentUser::INSTALL_APP ,'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);

        if(!empty($apartmentMapResidentUserSearch)){
            $apartmentMapResidentUser->install_app =  ApartmentMapResidentUser::INSTALL_APP; 
        }
        if(isset($param->type)){
            $apartmentMapResidentUser->type = $param->type;
        }
        if(isset($param->type_relationship) && $param->type_relationship !== null){
            $apartmentMapResidentUser->type_relationship = $param->type_relationship;
        }
        if (!$apartmentMapResidentUser->save()) {
            Yii::error($apartmentMapResidentUser->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $apartmentMapResidentUser->getErrors()
            ];
        }
        //sys data to department
        $apartmentMapResidentUser->syncDataToApartmentMap();
        if($is_new == true){
            $apartmentMapResidentUser->addHistory();
        }
        //update tags user
        if(!empty($apartmentMapResidentUser->resident_user_id)){
            $resident = ResidentUser::findOne(['id' => $apartmentMapResidentUser->resident_user_id]);
            $resident->updateNotifyTags();
        }

        return [
            'success' => true,
            'apartmentMapResidentUser' => $apartmentMapResidentUser,
            'is_new' => $is_new
        ];
    }

    /**
     * @param $resident ResidentUser
     */
    public function updateResident($resident){
        $this->resident_user_id = $resident->id;
        $this->resident_user_phone = $resident->phone;
        $this->resident_user_email = $resident->email;
        $this->resident_user_first_name = $resident->first_name;
        $this->resident_user_last_name = $resident->last_name;
        $this->resident_user_avatar = $resident->avatar;
        $this->resident_user_nationality = $resident->nationality;
        $this->resident_user_gender = $resident->gender;
        $this->resident_user_birthday = $resident->birthday;
        $this->resident_user_is_send_email = $resident->is_send_email;
        $this->resident_user_is_send_notify = $resident->is_send_notify;
        if($this->update() ===  false){
            Yii::error($this->getErrors());
            return false;
        }

        return true;
    }

    public function sendEparking($is_delete = false){
        $payload = [
            'type' => 'resident',
            'data' => [
                'customer_id' => $this->resident_user_id,
                'phone' => $this->resident_user_phone,
                'email' => $this->resident_user_email,
                'name' => $this->resident_user_first_name,
                'address' => "",
                'identity' => null,
                'status' => $this->status,
                'room' => $this->apartment_name,
                'building' => trim($this->apartment_parent_path, '/')
            ]
        ];
        QueueLib::channelEparking(json_encode($payload), $this->building_cluster_id);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if(empty($this->resident_user_nationality)){
            $this->resident_user_nationality = 'vi';
        }
        $name_search = CVietnameseTools::removeSigns2($this->resident_user_first_name);
        $this->resident_name_search = CVietnameseTools::toLower($name_search);
        $this->resident_user_last_name = null;
        return true;
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $apartment = Apartment::findOne(['id' => $this->apartment_id]);
        //tìm lại chủ hộ
        $need_update = false;
        $apartmentMap = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'type' => self::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(!empty($apartmentMap)){
//            if($apartment->resident_user_id != $apartmentMap->resident_user_id){
                $apartment->resident_user_id = $apartmentMap->resident_user_id;
                $apartment->resident_user_name = $apartmentMap->resident_user_first_name;
                $apartment->status = Apartment::STATUS_LIVE;
                $apartment->date_received = time(); 
                $need_update = true;
//            }
        }else{
            if(!empty($apartment->resident_user_id)){
                $apartment->resident_user_id = null;
                $apartment->resident_user_name = null;
                $apartment->status = Apartment::STATUS_EMPTY;
                $need_update = true;
            }
        }
        if($need_update){
            if (!$apartment->save()) {
                Yii::error($apartment->getErrors());
            }
        }

//        $this->sendEparking();

//        $need_update = false;
//        /**
//         * Cap nhat lai trang thai can ho khi co thay doi map resident voi can ho
//         */
//        if($apartment->resident_user_id != $this->resident_user_id) {
//            /**
//             * Neu them nguoi moi thi chi update khi la chu nha
//             */
//            if($this->type == self::TYPE_HEAD_OF_HOUSEHOLD){
//                $apartment->resident_user_id = $this->resident_user_id;
//                $apartment->resident_user_name = $this->resident_user_first_name;
//                $apartment->status = Apartment::STATUS_LIVE;
//                $need_update = true;
//            }
//        }else{
//            /**
//             * Neu chu ho set ve thanh vien thi can ho se ko con chu ho
//             */
//            if($this->type != self::TYPE_HEAD_OF_HOUSEHOLD){
//                $apartment->resident_user_id = null;
//                $apartment->resident_user_name = null;
//                $need_update = true;
//            }
//        }
//        if($need_update){
//            if (!$apartment->save()) {
//                Yii::error($apartment->getErrors());
//            }
//        }
    }

    function afterDelete()
    {
        parent::afterDelete();
        $resident = ResidentUser::findOne(['id' => $this->resident_user_id]);
        if(!empty($resident)){
            $resident->updateNotifyTags();
        }
    }

    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }

    public function getResident()
    {
        return $this->hasOne(ResidentUser::className(), ['phone' => 'resident_user_phone']);
    }

    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }

    public function getBuildingArea()
    {
        return $this->hasOne(BuildingArea::className(), ['id' => 'building_area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResidentUserDeviceTokens()
    {
        return ResidentUserDeviceToken::find()->where('resident_user_id in (select id from resident_user where phone = ' . $this->resident_user_phone)->all();
//        return $this->hasMany(ResidentUserDeviceToken::className(), ['resident_user_id' => 'resident_user_id']);
    }


    public function checkNotifyReceiveConfig($channel, $type, $action = []){
        //kiểm tra xem user này có cấu hình nhận thông báo tạo phí hay không không
        $query = [
            'building_cluster_id' => $this->building_cluster_id,
            'resident_user_id' => $this->resident_user_id,
            'channel' => $channel,
            'type' => $type,
        ];
        $query = array_merge($query, $action);
        return ResidentNotifyReceiveConfig::findOne($query);
    }

    public function syncDataToApartmentMap(){
        $apartmentMapUsers = ApartmentMapResidentUser::find()
            ->where(['building_cluster_id' => $this->building_cluster_id, 'resident_user_phone' => $this->resident_user_phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])
            ->andWhere(['<>', 'id', $this->id])
            ->all();
        foreach ($apartmentMapUsers as $apartmentMapResidentUser){
            $apartmentMapResidentUser->resident_user_gender = $this->resident_user_gender;
            $apartmentMapResidentUser->resident_user_birthday = $this->resident_user_birthday;
            $apartmentMapResidentUser->resident_user_email = $this->resident_user_email;
            $apartmentMapResidentUser->resident_user_first_name = $this->resident_user_first_name;
            $apartmentMapResidentUser->resident_user_last_name = $this->resident_user_last_name;
            $apartmentMapResidentUser->resident_user_avatar = $this->resident_user_avatar;
            $apartmentMapResidentUser->resident_user_phone = $this->resident_user_phone;
            $apartmentMapResidentUser->resident_user_nationality = $this->resident_user_nationality;
            $apartmentMapResidentUser->resident_name_search = $this->resident_name_search;
            $apartmentMapResidentUser->ngay_cap_cmtnd = $this->ngay_cap_cmtnd;
            $apartmentMapResidentUser->ngay_dang_ky_nhap_khau = $this->ngay_dang_ky_nhap_khau;
            $apartmentMapResidentUser->ngay_dang_ky_tam_chu = $this->ngay_dang_ky_tam_chu;
            $apartmentMapResidentUser->ngay_het_han_thi_thuc = $this->ngay_het_han_thi_thuc;
            $apartmentMapResidentUser->cmtnd = $this->cmtnd;
            $apartmentMapResidentUser->noi_cap_cmtnd = $this->noi_cap_cmtnd;
            $apartmentMapResidentUser->work = $this->work;
            $apartmentMapResidentUser->so_thi_thuc = $this->so_thi_thuc;
            if(!$apartmentMapResidentUser->save()){
                Yii::error($apartmentMapResidentUser->errors);
            }
        }
    }

    public function addHistory($is_add = HistoryResidentMapApartment::IS_ADD_APARTMENT){
        if($is_add == HistoryResidentMapApartment::IS_ADD_APARTMENT){
            //Them vao lich su da o cua cu dan
            $historyMap = new HistoryResidentMapApartment();
            $historyMap->apartment_id = $this->apartment_id;
            $historyMap->apartment_name = $this->apartment->name;
            $historyMap->apartment_parent_path = $this->apartment->parent_path;
            $historyMap->resident_user_id = $this->resident_user_id;
            $historyMap->resident_user_phone = $this->resident_user_phone;
            $historyMap->building_cluster_id = $this->building_cluster_id;
            $historyMap->time_in = $this->created_at;
            $historyMap->type = $this->type;
            if(!$historyMap->save()){
                Yii::error($historyMap->errors);
            }
        }else{
            //Update remove vao lich su da o cua cu dan
            $historyMap = HistoryResidentMapApartment::find()
                ->where(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $this->resident_user_phone])
                ->orderBy(['id' => SORT_DESC])
                ->one();
            if(!empty($historyMap)){
                $historyMap->time_out = time();
                if(!$historyMap->save()){
                    Yii::error($historyMap->errors);
                }
            }
        }
    }

    public function getFullName()
    {
        return trim($this->resident_user_first_name);
    }

    public function getTotalApartment($building_cluster_id = null, $resident_user_phone = null)
    {
        return ApartmentMapResidentUser::find()->where([
            'building_cluster_id' => is_null($building_cluster_id) ? $this->building_cluster_id : $building_cluster_id,
            'resident_user_phone' => is_null($resident_user_phone) ? $this->resident_user_phone : $resident_user_phone,
            'is_deleted' => ApartmentMapResidentUser::NOT_DELETED
        ])->count();
    }
}
