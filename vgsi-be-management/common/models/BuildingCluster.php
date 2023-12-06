<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\rbac\AuthGroup;
use backendQltt\models\LoggerUser;
use backendQltt\models\LogBehavior;

/**
 * This is the model class for table "building_cluster".
 *
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $link_dksd
 * @property string $email
 * @property string $hotline
 * @property string $avatar
 * @property string $address
 * @property string $bank_account
 * @property string $bank_name
 * @property string $bank_holders
 * @property string $description
 * @property string $medias
 * @property string $one_signal_app_id
 * @property string $one_signal_api_key
 * @property int $status  0 - chưa active, 1 - đã active
 * @property string $tax_code
 * @property string $tax_info
 * @property string $auth_item_tags
 * @property string $setting_group_receives_notices_financial
 * @property string $cash_instruction
 * @property string $alias
 * @property int $city_id id thành phố
 * @property int $is_deleted 0 : chưa xóa, 1 : đã xóa
 * @property int $limit_sms
 * @property int $limit_email
 * @property int $limit_notify
 * @property int $sms_price
 * @property int $security_mode
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property string $link_whether
 * @property string $email_account_push
 * @property string $email_password_push
 * @property string $sms_brandname_push
 * @property string $sms_account_push
 * @property string $sms_password_push
 * @property string $service_bill_template
 * @property string $service_bill_invoice_template
 * @property string $message_request_default
 *
 * @property ManagementUser[] $managementUsers
 * @property BuildingArea[] $buildingAreas
 */
class BuildingCluster extends \yii\db\ActiveRecord
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const NOT_DELETED = 0;
    const DELETED = 1;
    const SECURITY_MODE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'building_cluster';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'domain'], 'required'],
            // [['domain'], 'unique', 'message' => Yii::t('common', 'Domain đã tồn tại trong hệ thống')],
            [['alias', 'description', 'medias', 'one_signal_app_id', 'one_signal_api_key', 'hotline', 'auth_item_tags', 'setting_group_receives_notices_financial', 'cash_instruction', 'avatar'], 'string'],
            [['limit_sms', 'limit_email', 'limit_notify', 'sms_price', 'status', 'city_id', 'is_deleted', 'limit_sms', 'limit_email', 'limit_notify', 'created_at', 'updated_at', 'created_by', 'updated_by', 'security_mode'], 'integer'],
            [['domain',], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1000],
            [['email'], 'string', 'max' => 50],
            [['address', 'bank_account', 'bank_name', 'bank_holders',
                'link_whether',
                'email_account_push',
                'email_password_push',
                'sms_brandname_push',
                'sms_account_push',
                'sms_password_push',
                'link_dksd',
            ], 'string', 'max' => 255],
            [['tax_code'], 'string', 'max' => 45],
            [['tax_info'], 'string', 'max' => 512],
            [['service_bill_template', 'service_bill_invoice_template', 'message_request_default'], 'safe'],
            // [['address'], 'match', 'pattern' => '/^[\p{L},;\/\-\.]+$/u', 'message' => 'Only Vietnamese words and the characters ", / - ." are allowed.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Project Name'),
            'domain' => Yii::t('common', 'Domain'),
            'link_dksd' => Yii::t('common', 'Terms Of Use (Link)'),
            'email' => Yii::t('common', 'Email'),
            'hotline' => Yii::t('common', 'Hotline'),
            'address' => Yii::t('common', 'Address'),
            'one_signal_app_id' => Yii::t('common', 'One Signal App Id'),
            'one_signal_api_key' => Yii::t('common', 'One Signal Api Key'),
            'bank_account' => Yii::t('common', 'Bank Account'),
            'bank_name' => Yii::t('common', 'Bank Name'),
            'bank_holders' => Yii::t('common', 'Bank Holders'),
            'description' => Yii::t('common', 'Description'),
            'medias' => Yii::t('common', 'Medias'),
            'status' => Yii::t('common', 'Status'),
            'tax_code' => Yii::t('common', 'Tax Code'),
            'tax_info' => Yii::t('common', 'Tax Info'),
            'auth_item_tags' => Yii::t('common', 'Auth Item Tags'),
            'setting_group_receives_notices_financial' => Yii::t('common', 'Setting Group Receives Notices Financial'),
            'cash_instruction' => Yii::t('common', 'Cash Instruction'),
            'city_id' => Yii::t('common', 'City ID'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'limit_sms' => Yii::t('common', 'Limit Sms'),
            'limit_email' => Yii::t('common', 'Limit Email'),
            'limit_notify' => Yii::t('common', 'Limit Notify'),
            'sms_price' => Yii::t('common', 'Sms Price'),
            'security_mode' => Yii::t('common', 'Security Mode'),
            'link_whether' => Yii::t('common', 'Link Whether'),
            'email_account_push' => Yii::t('common', 'Email Account Push'),
            'email_password_push' => Yii::t('common', 'Email Password Push'),
            'sms_brandname_push' => Yii::t('common', 'Sms Brandname Push'),
            'sms_account_push' => Yii::t('common', 'Sms Account Push'),
            'sms_password_push' => Yii::t('common', 'Sms Password Push'),
            'service_bill_template' => Yii::t('common', 'Service Bill Template'),
            'service_bill_invoice_template' => Yii::t('common', 'Service Bill Invoice Template'),
            'message_request_default' => Yii::t('common', 'Message Request Default'),
            'avatar' => Yii::t('common', 'Avatar'),
            'alias' => Yii::t('common', 'Alias'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
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
            // 'log' => [
            //     'class' => LogBehavior::class,
            // ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagementUsers()
    {
        return $this->hasMany(ManagementUser::className(), ['building_cluster_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingAreas()
    {
        return $this->hasMany(BuildingArea::className(), ['building_cluster_id' => 'id']);
    }

    public function setDefaultData(){
        //tạo nhà cung cấp mặc định
        $providerCheck = \common\models\ServiceProvider::findOne(['building_cluster_id' => $this->id, 'is_deleted' => \common\models\ServiceProvider::NOT_DELETED]);
        if(empty($providerCheck)){
            $provider = new ServiceProvider();
            $provider->name = $this->name;
            $provider->address = $this->address;
            $provider->building_cluster_id = $this->id;
            $provider->status = 1;
            $provider->using_bank_cluster = 1;
            if(!$provider->save()){
                var_dump($provider->errors);
            }
        }

        //khởi tạo các nhóm quyền
        $authGroup = new \common\models\rbac\AuthGroup();
        $authGroup->name = 'Trưởng BQL';
        $authGroup->name_en = 'Management Board';
        $authGroup->description = 'Trưởng ban quản lý';
        $authGroup->data_role = '["ADMIN_PERMISSIONS_CREATE_UPDATE","ADMIN_PERMISSIONS_LIST","ANNOUNCE_CATEGORY_CREATE_UPDATE","ANNOUNCE_CATEGORY_LIST","ANNOUNCE_CREATE_UPDATE","ANNOUNCE_LIST","APARTMENT_CREATE_UPDATE","APARTMENT_LIST","BASIC","BOOKING_MANAGER","DASHBOARD_ANNOUNCEMENT","DASHBOARD_BOOKING","DASHBOARD_FINANCE","DASHBOARD_GENERAL","DASHBOARD_REQUEST","FINANCE_ANNOUNCE_FEE","FINANCE_CASH_BOOK","FINANCE_CREATE_BILL","FINANCE_DEBT","FINANCE_MANAGER","FINANCE_MANAGERMENT_BILL","FINANCE_SPECIAL_BILL","FINANCE_VIEW","FINANCE_VIEW_BILL","FullPermissionRole","MANAGEMENT-USER_CREATE_UPDATE","MANAGEMENT-USER_LIST","REQUEST_CATEGORY_CREATE_UPDATE","REQUEST_CATEGORY_LIST","REQUEST_CONTACT_RESIDENT","REQUEST_LIST","REQUEST_MANAGER_GROUPS_PROCESS_ADMIN","REQUEST_PROCESS","RESIDENT_CREATE_UPDATE","RESIDENT_LIST","SERVICE_CLOUD_ADD","SERVICE_CLOUD_VIEW","SERVICE_FEE_LIST","SERVICE_MANAGEMENT","SERVICE_MANAGERMENT_FEE","SERVICE_PROVIDER_DELETE","SERVICE_PROVIDER_EDIT","SERVICE_PROVIDER_VIEW","SERVICE_VIEW","SETTING_BUILDING_AREA_CREATE_UPDATE","SETTING_BUILDING_AREA_LIST","SETTING_CONFIG_RECEIVE_NOTIFY","SETTING_CONFIG_SEND_NOTIFY","SETTING_PLANE_UPDATE","SETTING_RECEIVE_NOTIFICATION","SETTING_RESIDENT_BOOK","SETTING_VIEW_PLANE","SETTING_VIEW_RESIDENT_BOOK"]';
        $authGroup->building_cluster_id = $this->id;
        $authGroup->type = 0;
        $authGroup->code = 'CODE1'.time();
        if(!$authGroup->save()){
            var_dump($authGroup->errors);
        }

        //Tạo tài khoản bql trên web
        $managementUser = new ManagementUser();
        $managementUser->email = 'bql.demo@luci.vn';
        $managementUser->setPassword('123456');
        $managementUser->phone = '84961196368';
        $managementUser->first_name = 'BQL Luci Building';
        $managementUser->status = 1;
        $managementUser->building_cluster_id = $this->id;
        $managementUser->auth_group_id = $authGroup->id;
        $managementUser->gender = 0;
        $managementUser->status_verify_email = 1;
        if(!$managementUser->save()){
            var_dump($managementUser->errors);
        }

        //các quyền khác
        $authGroup = new \common\models\rbac\AuthGroup();
        $authGroup->name = 'Lễ tân';
        $authGroup->name_en = 'Receptionist';
        $authGroup->description = 'Lễ tân';
        $authGroup->data_role = '["ADMIN_PERMISSIONS_CREATE_UPDATE","ADMIN_PERMISSIONS_LIST","ANNOUNCE_CATEGORY_CREATE_UPDATE","ANNOUNCE_CATEGORY_LIST","ANNOUNCE_CREATE_UPDATE","ANNOUNCE_LIST","APARTMENT_CREATE_UPDATE","APARTMENT_LIST","BASIC","DASHBOARD_ANNOUNCEMENT","DASHBOARD_FINANCE","DASHBOARD_GENERAL","DASHBOARD_REQUEST","FINANCE_MANAGER","FINANCE_VIEW","FullPermissionRole","MANAGEMENT-USER_CREATE_UPDATE","MANAGEMENT-USER_LIST","REQUEST_CATEGORY_CREATE_UPDATE","REQUEST_CATEGORY_LIST","REQUEST_CONTACT_RESIDENT","REQUEST_LIST","REQUEST_MANAGER_GROUPS_PROCESS_ADMIN","REQUEST_PROCESS","RESIDENT_CREATE_UPDATE","RESIDENT_LIST","SERVICE_CLOUD_ADD","SERVICE_CLOUD_VIEW","SERVICE_MANAGEMENT","SERVICE_PROVIDER_DELETE","SERVICE_PROVIDER_EDIT","SERVICE_PROVIDER_VIEW","SERVICE_VIEW","SETTING_BUILDING_AREA_CREATE_UPDATE","SETTING_BUILDING_AREA_LIST","SETTING_PLANE_UPDATE"]';
        $authGroup->building_cluster_id = $this->id;
        $authGroup->type = 0;
        $authGroup->code = 'CODE2'.time();
        if(!$authGroup->save()){
            var_dump($authGroup->errors);
        }

        //Tạo tài khoản bql trên web
        $managementUser = new ManagementUser();
        $managementUser->email = 'letan.demo@luci.vn';
        $managementUser->setPassword('123456');
        $managementUser->phone = '84961196369';
        $managementUser->first_name = 'BQL Luci Building';
        $managementUser->status = 1;
        $managementUser->building_cluster_id = $this->id;
        $managementUser->auth_group_id = $authGroup->id;
        $managementUser->gender = 0;
        $managementUser->status_verify_email = 1;
        if(!$managementUser->save()){
            var_dump($managementUser->errors);
        }

        $authGroup = new \common\models\rbac\AuthGroup();
        $authGroup->name = 'Kế toán';
        $authGroup->name_en = 'Accountant';
        $authGroup->description = 'Kế toán';
        $authGroup->data_role = '["BASIC","ADMIN_PERMISSIONS_CREATE_UPDATE","ADMIN_PERMISSIONS_LIST","ANNOUNCE_CATEGORY_CREATE_UPDATE","ANNOUNCE_CATEGORY_LIST","ANNOUNCE_CREATE_UPDATE","ANNOUNCE_LIST","APARTMENT_CREATE_UPDATE","APARTMENT_LIST","DASHBOARD_ANNOUNCEMENT","DASHBOARD_FINANCE","DASHBOARD_GENERAL","DASHBOARD_REQUEST","FINANCE_MANAGER","FINANCE_VIEW","FullPermissionRole","MANAGEMENT-USER_CREATE_UPDATE","MANAGEMENT-USER_LIST","REQUEST_CATEGORY_CREATE_UPDATE","REQUEST_CATEGORY_LIST","REQUEST_CONTACT_RESIDENT","REQUEST_LIST","REQUEST_MANAGER_GROUPS_PROCESS_ADMIN","REQUEST_PROCESS","RESIDENT_CREATE_UPDATE","RESIDENT_LIST","SERVICE_CLOUD_ADD","SERVICE_CLOUD_VIEW","SERVICE_MANAGEMENT","SERVICE_PROVIDER_DELETE","SERVICE_PROVIDER_EDIT","SERVICE_PROVIDER_VIEW","SERVICE_VIEW","SETTING_BUILDING_AREA_CREATE_UPDATE","SETTING_BUILDING_AREA_LIST","SETTING_PLANE_UPDATE"]';
        $authGroup->building_cluster_id = $this->id;
        $authGroup->type = 0;
        $authGroup->code = 'CODE3'.time();
        if(!$authGroup->save()){
            var_dump($authGroup->errors);
        }

        //Tạo tài khoản bql trên web
        $managementUser = new ManagementUser();
        $managementUser->email = 'ketoan.demo@luci.vn';
        $managementUser->setPassword('123456');
        $managementUser->phone = '84961196367';
        $managementUser->first_name = 'BQL Luci Building';
        $managementUser->status = 1;
        $managementUser->building_cluster_id = $this->id;
        $managementUser->auth_group_id = $authGroup->id;
        $managementUser->gender = 0;
        $managementUser->status_verify_email = 1;
        if(!$managementUser->save()){
            var_dump($managementUser->errors);
        }

        $authGroup = new \common\models\rbac\AuthGroup();
        $authGroup->name = 'Kỹ thuật';
        $authGroup->name_en = 'Technical Staff';
        $authGroup->description = 'Kỹ thuật';
        $authGroup->data_role = '["BASIC","ANNOUNCE_CATEGORY_CREATE_UPDATE","ANNOUNCE_CATEGORY_LIST","ANNOUNCE_CREATE_UPDATE","ANNOUNCE_LIST","REQUEST_CATEGORY_CREATE_UPDATE","REQUEST_CATEGORY_LIST","REQUEST_CONTACT_RESIDENT","REQUEST_LIST","REQUEST_MANAGER_GROUPS_PROCESS_ADMIN","REQUEST_PROCESS"]';
        $authGroup->building_cluster_id = $this->id;
        $authGroup->type = 0;
        $authGroup->code = 'CODE4'.time();
        if(!$authGroup->save()){
            var_dump($authGroup->errors);
        }

        //Tạo tài khoản bql trên web
        $managementUser = new ManagementUser();
        $managementUser->email = 'kythuat.demo@luci.vn';
        $managementUser->setPassword('123456');
        $managementUser->phone = '84961196366';
        $managementUser->first_name = 'kỹ thuật';
        $managementUser->status = 1;
        $managementUser->building_cluster_id = $this->id;
        $managementUser->auth_group_id = $authGroup->id;
        $managementUser->gender = 0;
        $managementUser->status_verify_email = 1;
        if(!$managementUser->save()){
            var_dump($managementUser->errors);
        }

        $authGroup = new \common\models\rbac\AuthGroup();
        $authGroup->name = 'Ban quản trị';
        $authGroup->name_en = 'Board of Directors';
        $authGroup->description = 'Ban quản trị';
        $authGroup->data_role = '["BASIC","ADMIN_PERMISSIONS_CREATE_UPDATE","ADMIN_PERMISSIONS_LIST","ANNOUNCE_CATEGORY_CREATE_UPDATE","ANNOUNCE_CATEGORY_LIST","ANNOUNCE_CREATE_UPDATE","ANNOUNCE_LIST","APARTMENT_CREATE_UPDATE","APARTMENT_LIST","DASHBOARD_ANNOUNCEMENT","DASHBOARD_FINANCE","DASHBOARD_GENERAL","DASHBOARD_REQUEST","FINANCE_MANAGER","FINANCE_VIEW","FullPermissionRole","MANAGEMENT-USER_CREATE_UPDATE","MANAGEMENT-USER_LIST","REQUEST_CATEGORY_CREATE_UPDATE","REQUEST_CATEGORY_LIST","REQUEST_CONTACT_RESIDENT","REQUEST_LIST","REQUEST_MANAGER_GROUPS_PROCESS_ADMIN","REQUEST_PROCESS","RESIDENT_CREATE_UPDATE","RESIDENT_LIST","SERVICE_CLOUD_ADD","SERVICE_CLOUD_VIEW","SERVICE_MANAGEMENT","SERVICE_PROVIDER_DELETE","SERVICE_PROVIDER_EDIT","SERVICE_PROVIDER_VIEW","SERVICE_VIEW","SETTING_BUILDING_AREA_CREATE_UPDATE","SETTING_BUILDING_AREA_LIST","SETTING_PLANE_UPDATE"]';
        $authGroup->building_cluster_id = $this->id;
        $authGroup->type = 0;
        $authGroup->code = 'CODE5'.time();
        if(!$authGroup->save()){
            var_dump($authGroup->errors);
        }

        //Tạo tài khoản bql trên web
        $managementUser = new ManagementUser();
        $managementUser->email = 'bqt.demo@luci.vn';
        $managementUser->setPassword('123456');
        $managementUser->phone = '84961196365';
        $managementUser->first_name = 'Ban quản trị';
        $managementUser->status = 1;
        $managementUser->building_cluster_id = $this->id;
        $managementUser->auth_group_id = $authGroup->id;
        $managementUser->gender = 0;
        $managementUser->status_verify_email = 1;
        if(!$managementUser->save()){
            var_dump($managementUser->errors);
        }

        $email_content = 'Kính gửi Anh/Chị {{RESIDENT_NAME}},<br>
  Ban Quản lý xin gửi thông báo phí tháng 07/2019 bao gồm phí dịch vụ và phí nước của căn hộ {{APARTMENT_NAME}} ( file đính kèm trong mail này).<br>
Thời gian thu phí (từ 08h00 đến 20h00):<br>
+ Từ ngày 01/8 đến 10/8/2019 <br>
Địa điểm thu phí:<br>
+ Tại Lễ tân '.$this->name.'<br>
 Phương thức thanh toán phí: Quý cư dân có thể<br>
+ Đóng phí bằng tiền mặt tại quầy lễ tân, hoặc<br>
+ Chuyển khoản theo tài khoản chỉ định trên thông báo phí.<br>
Mã thanh toán phí của căn hộ là: {{PAYMENT_CODE}}<br>
Mọi ý kiến  thắc mắc và đóng góp xin vui lòng liên hệ tại Quầy lễ tân Sảnh T2.<br>
Thank you and best regards!<br>
Ban Quản Lý Chung cư '.$this->name.'.<br>
Số điện thoại liên hệ (8h00 - 17h00): 024.33.11.33.14 <br>
Hotline Bảo Vệ (24/24): 024.66.575.675 <br>
Hotline Kỹ Thuật (24/24):  0868 081078';

        $sms_content = '['.$this->name.'] xin gửi thông báo phí tháng 08/2019 của căn hộ {{RESIDENT_NAME}}: {{TOTAL_FEE}}';
        //Tạo mẫu thông báo phí
        $templateFee = new \common\models\AnnouncementTemplate();
        $templateFee->name = 'Thông báo nhắc nợ lần 1';
        $templateFee->name_en = '1st debt reminder notification';
        $templateFee->building_cluster_id = $this->id;
        $templateFee->content_email = $email_content;
        $templateFee->content_sms = $sms_content;
        $templateFee->type = 1;
        if(!$templateFee->save()){
            var_dump($templateFee->errors);
        }

        $templateFee = new \common\models\AnnouncementTemplate();
        $templateFee->name = 'Thông báo nhắc nợ lần 2';
        $templateFee->name_en = '2st debt reminder notification';
        $templateFee->building_cluster_id = $this->id;
        $templateFee->content_email = $email_content;
        $templateFee->content_sms = $sms_content;
        $templateFee->type = 2;
        if(!$templateFee->save()){
            var_dump($templateFee->errors);
        }

        $templateFee = new \common\models\AnnouncementTemplate();
        $templateFee->name = 'Thông báo nhắc nợ lần 3';
        $templateFee->name_en = '3st debt reminder notification';
        $templateFee->building_cluster_id = $this->id;
        $templateFee->content_email = $email_content;
        $templateFee->content_sms = $sms_content;
        $templateFee->type = 3;
        if(!$templateFee->save()){
            var_dump($templateFee->errors);
        }

        //tạo danh mục thông báo phí mặc định
        $announcementCategory = new \common\models\AnnouncementCategory();
        $announcementCategory->name = 'Thông báo phí';
        $announcementCategory->name_en = 'Fee notification';
        $announcementCategory->label_color = '#eb144c';
        $announcementCategory->building_cluster_id = $this->id;
        $announcementCategory->type = 1;
        if(!$announcementCategory->save()){
            var_dump($announcementCategory->errors);
        }

        return true;
    }
    public function setDefaultAuthGroup(){
        //khởi tạo các nhóm quyền
        $authGroup = AuthGroup::findOne(['name' => 'Admin BQL', 'building_cluster_id' => $this->id]);
        if(!empty($authGroup)){
            return $authGroup;
        }
        $authGroup = new AuthGroup();
        $authGroup->name = 'Admin BQL';
        $authGroup->name_en = 'Management Board';
        $authGroup->description = 'Trưởng ban quản lý';
        $authGroup->data_role = '["ADMIN_PERMISSIONS_CREATE_UPDATE","ADMIN_PERMISSIONS_LIST","ANNOUNCE_CATEGORY_CREATE_UPDATE","ANNOUNCE_CATEGORY_LIST","ANNOUNCE_CREATE_UPDATE","ANNOUNCE_LIST","APARTMENT_CREATE_UPDATE","APARTMENT_LIST","BASIC","BOOKING_MANAGER","DASHBOARD_ANNOUNCEMENT","DASHBOARD_BOOKING","DASHBOARD_FINANCE","DASHBOARD_GENERAL","DASHBOARD_REQUEST","FINANCE_ANNOUNCE_FEE","FINANCE_CASH_BOOK","FINANCE_CREATE_BILL","FINANCE_DEBT","FINANCE_MANAGER","FINANCE_MANAGERMENT_BILL","FINANCE_SPECIAL_BILL","FINANCE_VIEW","FINANCE_VIEW_BILL","FullPermissionRole","MANAGEMENT-USER_CREATE_UPDATE","MANAGEMENT-USER_LIST","REQUEST_CATEGORY_CREATE_UPDATE","REQUEST_CATEGORY_LIST","REQUEST_CONTACT_RESIDENT","REQUEST_LIST","REQUEST_MANAGER_GROUPS_PROCESS_ADMIN","REQUEST_PROCESS","RESIDENT_CREATE_UPDATE","RESIDENT_LIST","SERVICE_CLOUD_ADD","SERVICE_CLOUD_VIEW","SERVICE_FEE_LIST","SERVICE_MANAGEMENT","SERVICE_MANAGERMENT_FEE","SERVICE_PROVIDER_DELETE","SERVICE_PROVIDER_EDIT","SERVICE_PROVIDER_VIEW","SERVICE_VIEW","SETTING_BUILDING_AREA_CREATE_UPDATE","SETTING_BUILDING_AREA_LIST","SETTING_CONFIG_RECEIVE_NOTIFY","SETTING_CONFIG_SEND_NOTIFY","SETTING_PLANE_UPDATE","SETTING_RECEIVE_NOTIFICATION","SETTING_RESIDENT_BOOK","SETTING_VIEW_PLANE","SETTING_VIEW_RESIDENT_BOOK"]';
        $authGroup->building_cluster_id = $this->id;
        $authGroup->type = 0;
        $authGroup->code = 'CODE1'.time();
        if(!$authGroup->save()){
            var_dump($authGroup->errors);
            return false;
        }
        return $authGroup;
    }

}
