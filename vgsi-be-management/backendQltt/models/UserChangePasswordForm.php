<?php

namespace backendQltt\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\base\NotSupportedException;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="UserChangePasswordForm")
 * )
 */
class UserChangePasswordForm extends Model
{

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
    public function rules()
    {
        return [
            [['old_password', 'password', 'confirm_password'], 'required'],
            ['password', 'string', 'min' => 8, 'max' => 20],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('backendQltt', 'Mật khẩu không trùng khớp')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'old_password' => Yii::t('backend', 'Mật khẩu cũ'),
            'password' => Yii::t('backend', 'Mật khẩu mới'),
            'confirm_password' => Yii::t('backend', 'Nhập lại mật khẩu mới'),
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function changePassword()
    {
        $user = User::findOne(Yii::$app->user->getIdentity()->id);

        if (!$user->validatePassword($this->old_password)) {
            throw new NotSupportedException(Yii::t('backendQltt', 'Old password incorrect'));
        }

        if ($user->validatePassword($this->password)) {
            throw new NotSupportedException(Yii::t('backendQltt', 'Mật khẩu mới trùng với mật khẩu cũ'));
        }

        $user->setPassword($this->password);

        if ($user->birthday) {
            unset($user->birthday);
        }

        if (!$user->save(false)) {
            Yii::error($user->errors);
            throw new NotSupportedException(Yii::t('backendQltt', 'Change password error'));
        }
        ;

        return true;
    }

}