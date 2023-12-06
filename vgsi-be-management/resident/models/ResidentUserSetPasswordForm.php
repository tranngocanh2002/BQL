<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserSetPasswordForm")
 * )
 */
class ResidentUserSetPasswordForm extends Model
{
    /**
     * @SWG\Property(description="first_name")
     * @var string
     */
    public $first_name;

    /**
     * @SWG\Property(description="last_name")
     * @var string
     */
    public $last_name;

    /**
     * @SWG\Property(description="password")
     * @var string
     */
    public $password;

    /**
     * @SWG\Property(description="confirm password")
     * @var string
     */
    public $confirm_password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'string'],
            [['password'], 'required', 'message' => Yii::t('resident', 'invalid information, password has more than 8 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()')],
            [['password'], 'string', 'min' => Yii::$app->params['length_password_random'] ?? 6, 'message' => Yii::t('resident', 'invalid information, password has more than 8 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()')],
            [['confirm_password'], 'required', 'message' => Yii::t('resident', 'invalid information, password confirm has more than 8 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()')],
            [
                'confirm_password',
                'compare',
                'compareAttribute' => 'password',
                'message' => Yii::t('resident', 'invalid information, password confirm and password not match '),
            ],
        ];
    }

    public function setPassword() {
        $user = Yii::$app->user->getIdentity();
        $item = ResidentUserResponse::findOne(['id' => $user->id]);
        if($item){
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->setPassword($this->password);
            if($item->save(false)){
                return [
                    'success' => true,
                    'message' => Yii::t('resident', "Success")
                ];
            }
        }
        return [
            'success' => false,
            'message' => Yii::t('resident', "Invalid data"),
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
        ];
    }

    public function setPasswordByOtp() {
        $phone = Yii::$app->request->post('phone');
        $phone = substr($phone,1,9);
        $resident = ResidentUser::find()
        ->where(['LIKE', 'phone', $phone])
        ->one();
        if (empty($resident)) {
            $resident = new ResidentUser();
            $resident->phone = '84'.$phone;
            $resident->status_verify_phone = ResidentUser::STATUS_VERIFY;
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
        $item = ResidentUserResponse::find()
                ->where(['LIKE', 'phone', $phone])
                ->one();
        if($item){
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->setPassword($this->password);
            if($item->save(false)){
                return [
                    'success' => true,
                    'message' => Yii::t('resident', "Success")
                ];
            }
        }
        return [
            'success' => false,
            'message' => Yii::t('resident', "Invalid data"),
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
        ];
    }
}
