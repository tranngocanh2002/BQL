<?php
namespace frontend\models;

use common\models\VerifyCode;
use Yii;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="CheckOtpTokenForm")
 * )
 */
class CheckOtpTokenForm extends Model
{
    /**
     * @SWG\Property(description="Token")
     * @var string
     */
    public $token;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token'], 'required'],
            ['token', 'validateToken'],
        ];
    }

    public function validateToken($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!empty($this->token)) {
                $verifyCode = VerifyCode::findOne(['status' => VerifyCode::STATUS_NOT_VERIFY, 'code' => $this->token, 'type' => VerifyCode::TYPE_FORGOT_PASSWORD_MANAGEMENT_USER]);
                if(!empty($verifyCode)){
                    if($verifyCode->expired_at > time()){
                        return true;
                    }else{
                        $this->addError($attribute, Yii::t('frontend','Token Verify Expired.'));
                    }
                }
                $this->addError($attribute, Yii::t('frontend','Mã OTP không đúng. Vui lòng nhập lại.'));
            }
        }
    }
    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function check()
    {
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Success"),
        ];
    }
}
