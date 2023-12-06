<?php
namespace backendQltt\models;

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
     * @SWG\Property(description="Text 1")
     * @var string
     */
    public $text_1;

    /**
     * @SWG\Property(description="Text 2")
     * @var string
     */
    public $text_2;

    /**
     * @SWG\Property(description="Text 3")
     * @var string
     */
    public $text_3;

    /**
     * @SWG\Property(description="Text 3")
     * @var string
     */
    public $text_4;

    /**
     * @SWG\Property(description="Text 3")
     * @var string
     */
    public $text_5;

    /**
     * @SWG\Property(description="Text 3")
     * @var string
     */
    public $text_6;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token'], 'required'],
            // [['text_1', 'text_2', 'text_3', 'text_4', 'text_5', 'text_6'], 'required'],
            ['token', 'validateCode'],
        ];
    }

    public function attributeLabels() {
        return [
            'text_1' => Yii::t('backendQltt', 'otp'),
            'text_2' => Yii::t('backendQltt', 'otp'),
            'text_3' => Yii::t('backendQltt', 'otp'),
            'text_4' => Yii::t('backendQltt', 'otp'),
            'text_5' => Yii::t('backendQltt', 'otp'),
            'text_6' => Yii::t('backendQltt', 'otp'),
        ];
    }

    /**
     * Validate code
     * @param $attribute
     * @return bool
     */
    public function validateCode($email)
    {
        $text1 = Yii::$app->request->post()['CheckOtpTokenForm']['text_1'];
        $text2 = Yii::$app->request->post()['CheckOtpTokenForm']['text_2'];
        $text3 = Yii::$app->request->post()['CheckOtpTokenForm']['text_3'];
        $text4 = Yii::$app->request->post()['CheckOtpTokenForm']['text_4'];
        $text5 = Yii::$app->request->post()['CheckOtpTokenForm']['text_5'];
        $text6 = Yii::$app->request->post()['CheckOtpTokenForm']['text_6'];
        $code = $text1.$text2.$text3.$text4.$text5.$text6;
        if (!$this->hasErrors()) {
            if (!empty($code) || 6 != strlen($code)) {
                $payload = [
                    'email' => $email,
                ];

                $verifyCode = VerifyCode::findOne([
                    'status' => VerifyCode::STATUS_NOT_VERIFY,
                    'code' => $code,
                    'type' => VerifyCode::TYPE_FORGOT_PASSWORD_ADMIN,
                    'payload' => json_encode($payload),
                ]);

                if(!empty($verifyCode)) {
                    
                    if($verifyCode->expired_at > time()){
                        return true;
                    }else{
                        Yii::$app->session->setFlash('error', 'Code Verify Expired.');
                    }
                }
                
                Yii::$app->session->setFlash('error', 'Code Verify Not Exist.');
            } 
            else {
                Yii::$app->session->setFlash('error', $code);
            }
        }

        return false;
    }
    /**
     * Resets password.
     *
     * @return array if password was reset.
     */
    public function check()
    {
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Success"),
        ];
    }
}