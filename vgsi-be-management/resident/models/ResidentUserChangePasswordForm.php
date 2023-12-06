<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserChangePasswordForm")
 * )
 */
class ResidentUserChangePasswordForm extends Model
{
    /**
     * @SWG\Property(description="old password")
     * @var string
     */
    public $old_password;

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
            [['old_password'], 'required', 'message' => Yii::t('resident', 'invalid information, password has more than 8 characters and include a-z; A-Z; 0-9 or special character "!@#$%^&*()')],
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

    public function changePassword() {
        $user = Yii::$app->user->getIdentity();
        $item = ResidentUserResponse::findOne(['id' => $user->id]);
        if($item){
            if(!$item->validatePassword($this->old_password)){
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Old password Invalid"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $item->setPassword($this->password);
            if($item->save(false)){
                return [
                    'success' => true,
                    'message' => Yii::t('resident', "Success"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        }
        return [
            'success' => true,
            'message' => Yii::t('resident', "Invalid data"),
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
        ];
    }
}
