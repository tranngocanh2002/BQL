<?php
namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\BuildingCluster;
use common\models\ManagementUser;
use common\models\VerifyCode;
use riskivy\captcha\CaptchaHelper;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResetPasswordForm")
 * )
 */
class ResetPasswordForm extends Model
{
    /**
     * @SWG\Property(description="Token")
     * @var string
     */
    public $token;

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
     * @var Email
     */
    private $_email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'confirm_password', 'token'], 'required'],
            ['token', 'validateToken'],
            ['password', 'string', 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match" ],
        ];
    }

    public function validateToken($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!empty($this->token)) {
                $verifyCode = VerifyCode::findOne(['status' => VerifyCode::STATUS_NOT_VERIFY, 'code' => $this->token, 'type' => VerifyCode::TYPE_FORGOT_PASSWORD_MANAGEMENT_USER]);
                if(!empty($verifyCode)){
                    if($verifyCode->expired_at > time()){
                        $verifyCode->status = VerifyCode::STATUS_VERIFY;
                        $verifyCode->save();
                        $payload = (!empty($verifyCode->payload)) ? json_decode($verifyCode->payload,true) : [];
                        $this->_email = $payload['email'];
                        if (!$this->_email) {
                            throw new InvalidArgumentException('Wrong verify email token.');
                        }
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
    public function resetPassword()
    {
        $user = ManagementUser::findByEmail($this->_email);
        if(!empty($user)){
            $is_send_email = false;
            if(empty($user->status_verify_email)){
                $is_send_email = true;
            }
            $user->setPassword($this->password);
            $user->status_verify_email = ManagementUser::STATUS_VERIFY;
            if(!$user->save(false)){
                return [
                    'success' => false,
                    'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                    'message' => Yii::t('frontend', "Invalid data"),
                ];
            };
//            if($is_send_email === true){
//                $buildingCluster = BuildingCluster::findOne(['id' => $user->building_cluster_id]);
//                $domain = (!empty($buildingCluster)) ? $buildingCluster->domain : '';
//                return Yii::$app
//                    ->mailer
//                    ->compose(
//                        ['html' => 'welcome-html'],
//                        ['user' => $user, 'linkWeb' => $domain]
//                    )
//                    ->setFrom([Yii::$app->params['supportEmail'] => $buildingCluster->name])
//                    ->setTo($user->email)
//                    ->setSubject('Chào mừng gia nhập hệ thống quản lý '.$buildingCluster->name)
//                    ->send();
//            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Success"),
            ];
        }else{
            return [
                'success' => false,
                'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                'message' => Yii::t('frontend', "Invalid data"),
            ];
        }
    }
}
