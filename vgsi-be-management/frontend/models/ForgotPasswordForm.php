<?php
namespace frontend\models;

use common\models\BuildingCluster;
use common\models\VerifyCode;
use riskivy\captcha\CaptchaHelper;
use Yii;
use yii\base\Model;
use common\models\ManagementUser;
use yii\log\EmailTarget;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ForgotPasswordForm")
 * )
 */
class ForgotPasswordForm extends Model
{
    /**
     * @SWG\Property(description="Email")
     * @var string
     */
    public $email;

    /**
     * @SWG\Property(description="Captcha Code")
     * @var string
     */
    public $captcha_code;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // ['captcha_code', 'validateCaptcha'],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\ManagementUser',
                'filter' => ['status' => ManagementUser::STATUS_ACTIVE],
                'message' => Yii::t('frontend','Email không tồn tại trong hệ thống')
            ],
        ];
    }

    public function validateCaptcha($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!empty($this->captcha_code) && !(new CaptchaHelper())->verify($this->captcha_code)) {
                $this->addError($attribute, Yii::t('frontend','Incorrect captcha code.'));
            }
        }
    }
    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail($is_web = true)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        /* @var $user ManagementUser */
        $user = ManagementUser::findOne([
            'status' => ManagementUser::STATUS_ACTIVE,
            'email' => $this->email,
            'building_cluster_id' => $buildingCluster->id,
            'is_deleted' => ManagementUser::NOT_DELETED,
        ]);

        if (!$user) {
            return false;
        }
        return $user->resetPasswordSendEmail($is_web,$this->email);
    }
}
