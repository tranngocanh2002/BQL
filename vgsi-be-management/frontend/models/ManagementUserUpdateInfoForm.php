<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\rbac\AuthGroup;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserUpdateInfoForm")
 * )
 */
class ManagementUserUpdateInfoForm extends Model
{
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
     * @SWG\Property(description="Is Send Email", default=1, type="integer", description="0 - không nhận, 1 - có nhận")
     * @var integer
     */
    public $is_send_email;

    /**
     * @SWG\Property(description="Is Send Notify", default=1, type="integer", description="0 - không nhận, 1 - có nhận")
     * @var integer
     */
    public $is_send_notify;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'first_name', 'last_name', 'avatar'], 'string'],
            [['gender', 'birthday', 'is_send_email', 'is_send_notify'], 'integer'],
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

    public function update(){
        $user = Yii::$app->user->getIdentity();
        $item = ManagementUserResponse::findOne(['id' => $user->id, 'role_type' => ManagementUser::DEFAULT_ADMIN]);
        if($item){
            $item->load(CUtils::arrLoad($this->attributes), '');
            if(!$item->save()){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }else{
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
