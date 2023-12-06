<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserDeviceToken;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use common\models\User;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserChangePasswordForm")
 * )
 */
class ManagementUserChangePasswordForm extends Model {

    /**
     * @SWG\Property(description="Old Password")
     * @var string
     */
    public $old_password;

    /**
     * @SWG\Property(description="Password")
     * @var string
     */
    public $password;

    /**
     * @SWG\Property(description="Confirm Password")
     * @var string
     */
    public $confirm_password;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['old_password', 'password', 'confirm_password'], 'required'],
            ['password', 'string', 'min' => 6],
            ['password', 'string', 'min' => 6],
            [
                'password',
                'compare',
                'compareAttribute' => 'confirm_password',
                'message' => Yii::t('frontend', 'invalid information, password confirm and password not match '),
            ],
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function changePassword() {
        $user = Yii::$app->user->getIdentity();
        if(!$user->validatePassword($this->old_password)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Old password incorrect"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $user->setPassword($this->password);
        if(!$user->save()){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Change password error"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $user->getErrors()
            ];
        };
        ManagementUserAccessToken::deleteAll(['management_user_id' => $user->id]);
        ManagementUserDeviceToken::deleteAll(['management_user_id' => $user->id]);
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Change Password Success"),
        ];
    }

}
