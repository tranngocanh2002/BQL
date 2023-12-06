<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserDeviceToken;
use common\models\rbac\AuthGroup;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserCreateForm")
 * )
 */
class ManagementUserCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Email")
     * @var string
     */
    public $email;

    /**
     * @SWG\Property(description="Phone")
     * @var string
     */
    public $phone;

    /**
     * @SWG\Property(description="First Name")
     * @var string
     */
    public $first_name;

    /**
     * @SWG\Property(description="Mã nhân sự")
     * @var string
     */
    public $code_management_user;

    /**
     * @SWG\Property(description="Last Name")
     * @var string
     */
    public $last_name;

    /**
     * @SWG\Property(description="Avatar")
     * @var string
     */
    public $avatar;

    /**
     * @SWG\Property(description="Status", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="Gender", default=1, type="integer")
     * @var integer
     */
    public $gender;

    /**
     * @SWG\Property(description="Birthday", default=1, type="integer")
     * @var integer
     */
    public $birthday;

    /**
     * @SWG\Property(description="Auth Group Id", default=1, type="integer")
     * @var integer
     */
    public $auth_group_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email', 'phone', 'first_name', 'last_name', 'avatar','code_management_user'], 'string'],
            [['gender', 'status', 'birthday', 'auth_group_id'], 'integer'],
            [['auth_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuthGroup::className(), 'targetAttribute' => ['auth_group_id' => 'id']],
            [['id'], 'required', "on" => ['update']],
            [['id'], 'integer', "on" => ['update']],
            [['phone'], 'validateMobile']
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'invalid phone number'));
        }
    }

    public function create(){
        $user = Yii::$app->user->getIdentity();
        $managementUserCheckPhone = ManagementUser::findOne(['code_management_user' => $this->code_management_user, 'is_deleted' => ManagementUser::NOT_DELETED, 'building_cluster_id' => $user->building_cluster_id]);
        if ($managementUserCheckPhone) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Code management user exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $managementUserCheckEmail = ManagementUser::findOne(['email' => $this->email, 'is_deleted' => ManagementUser::NOT_DELETED, 'building_cluster_id' => $user->building_cluster_id]);
        if($managementUserCheckEmail){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Email exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $managementUserCheckPhone = ManagementUser::findOne(['phone' => $this->phone, 'is_deleted' => ManagementUser::NOT_DELETED, 'building_cluster_id' => $user->building_cluster_id]);
        if ($managementUserCheckPhone) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Phone exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $item = new ManagementUser();
        $item->load(CUtils::arrLoad($this->attributes),'');
        $password_new = CUtils::randomString(10);
        $item->setPassword($password_new);
        $item->status = ManagementUser::STATUS_ACTIVE;
        $item->building_cluster_id = $user->building_cluster_id;
        if(!$item->save()){
            Yii::error($item->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $item->getErrors()
            ];
        }else{
            return $item->sendEmailCreatePassword($password_new,$this->email);
        }
    }

    public function update(){
        $item = ManagementUserResponse::findOne(['id' => (int)$this->id, 'role_type' => ManagementUser::DEFAULT_ADMIN]);

        if($item){
            $email_old = $item->email;
            $auth_group_old = $item->auth_group_id;
            $item->load(CUtils::arrLoad($this->attributes), '');
            $email_new = $item->email;
            $auth_group_new = $item->auth_group_id;
            if(!$item->save()){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }else{
                if($email_old !== $email_new || $auth_group_old !== $auth_group_new){
                    ManagementUserDeviceToken::deleteAll(['management_user_id' => $item->id]);
                    ManagementUserAccessToken::deleteAll(['management_user_id' => $item->id]);
                }
                return $item;
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
