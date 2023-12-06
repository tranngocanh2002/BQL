<?php

namespace resident\models;

use common\helpers\AccountKit;
use common\helpers\CgvVoiceOtp;
use common\helpers\CmsVoiceOtp;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\QueueLib;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use common\models\ResidentUserAccessToken;
use common\models\ResidentUserDeviceToken;
use common\models\VerifyCode;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use Anhskohbo\AccountKit\Config;
use Anhskohbo\AccountKit\Client;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="LoginOtpForm")
 * )
 */
class LoginOtpForm extends Model
{
    /**
     * @SWG\Property(description="phone")
     * @var string
     */
    public $phone;

    /**
     * @SWG\Property(description="code : mã trả về từ api, dùng khi confirm otp")
     * @var string
     */
    public $code;

    /**
     * @SWG\Property(description="otp_code : mã code gửi về điện thoại")
     * @var string
     */
    public $otp_code;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['otp_code', 'code'], 'required', "on" => ['login']],
            [['phone', 'otp_code', 'code'], 'string'],
            ['phone', 'validateMobile'],
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('resident', 'Số điện thoại không hợp lệ'));
        }
    }

    public function checkPhone()
    {
        // //check số điện thoại đã được gán căn hộ thì mới gửi mã otp đăng nhập
        // $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $this->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE]);
        // if(empty($apartmentMapResidentUser)){
        //     return [
        //         'success' => false,
        //         'message' => Yii::t('resident', 'Số điện thoại chưa được khai báo căn hộ'),
        //     ];
        // }
        $resident = ResidentUser::findByPhoneIsDelete($this->phone);
        if (!empty($resident)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', 'Số điện thoại đã bị xóa'),
                'code'    => 202,
            ];
        }
        $resident = ResidentUser::findByPhone($this->phone);
        if (empty($resident)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', 'Số điện thoại chưa tồn tại'),
            ];
        }
        return [
            'success' => true,
            'message' => Yii::t('resident', 'Số điện thoại đã tồn tại'),
        ];
    }
    public function checkCurrentPhone()
    {
        // //check số điện thoại đã được gán căn hộ thì mới gửi mã otp đăng nhập
        // $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $this->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE]);
        // if(empty($apartmentMapResidentUser)){
        //     return [
        //         'success' => false,
        //         'message' => Yii::t('resident', 'Số điện thoại chưa được khai báo căn hộ'),
        //     ];
        // }
        $resident = ResidentUser::findByPhone($this->phone);
        if (empty($resident)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', 'Số điện thoại chưa tồn tại'),
            ];
        }
        $this->sendOtp();
    }

    public function sendOtp()
    {
        //kiểm tra thời gian gần nhất tạo mã
        //Tạm thời bỏ kiểm tra giới hạn 5p
//        $verifyCode = VerifyCode::find()
//            ->where(['type' => VerifyCode::TYPE_LOGIN_RESIDENT_USER])
//            ->andWhere(['like', 'payload', $this->phone])
//            ->andWhere(['>', 'created_at', time() - 1 * 60])
//            ->orderBy(['created_at' => SORT_DESC])
//            ->one();
//        if ($verifyCode) {
//            return [
//                'success' => false,
//                'message' => Yii::t('resident', 'Chờ sau 5 phút'),
//            ];
//        }
        $currentTime = time();
        $startTime = strtotime('today', $currentTime);
        $endTime = strtotime('tomorrow', $startTime) - 1;
        $countOtpByCurentDay = VerifyCode::find()
        ->andWhere(['like', 'payload', $this->phone])
        ->andWhere(['between', 'created_at', $startTime, $endTime])
        ->count();
        if($countOtpByCurentDay > 10)
        {
            return [
                'success' => false,
                'message' => Yii::t('resident', 'Bạn đã sử dụng hết số lượt xác thực OTP trong ngày')
            ];
        }
        //hủy mã xác thực cũ
        $verifyCodes = VerifyCode::find()
            ->where(['type' => VerifyCode::TYPE_LOGIN_RESIDENT_USER, 'status' => VerifyCode::STATUS_NOT_VERIFY])
            ->andWhere(['like', 'payload', $this->phone])
            ->all();
        foreach ($verifyCodes as $verifyCode) {
            $verifyCode->status = VerifyCode::STATUS_VERIFY_ERROR;
            if (!$verifyCode->save()) {
                Yii::error($verifyCode->errors);
            }
        }

        //Tạo mã xác thực mới
        $in_production = Yii::$app->params['in_production'];
        $PhoneWhiteListOtp = Yii::$app->params['PhoneWhiteListOtp'];
        if ($in_production == false || in_array($this->phone, $PhoneWhiteListOtp)) {
            $otp_code = '123456';
        } else {
            $otp_code = CUtils::generateRandomNumber(6);
        }

        $verifyCode = new VerifyCode();
        $verifyCode->code = VerifyCode::generateCode();
        $verifyCode->type = VerifyCode::TYPE_LOGIN_RESIDENT_USER;
        $verifyCode->expired_at = time() + 5 * 60;
        $verifyCode->payload = json_encode(['phone' => $this->phone, 'otp_code' => $otp_code]);
        if (!$verifyCode->save()) {
            Yii::error($verifyCode->errors);
            return [
                'success' => false,
                'message' => Yii::t('resident', 'Tạo mã OTP không thành công'),
            ];
        }
        //gửi tin nhắn nếu là product
        if ($in_production == true && !in_array($this->phone, $PhoneWhiteListOtp)) {
            $mode_otp = Yii::$app->params['mode_otp'];
            if($mode_otp == 'SMS'){
                self::otpCmc($otp_code);
            }else{
                self::voiceOtp($otp_code, $this->phone);
            }
        }

        return [
            'success' => true,
            'code' => $verifyCode->code,
            'message' => Yii::t('resident', 'Mã OTP đã được gửi vế số điện thoại, vui lòng nhập mã OTP để đăng nhập vào ứng dụng!'),
        ];
    }

    private function otpCmc($otp_code){
        $SmsCmc = Yii::$app->params['SmsCmc'];
//        $contentSms = "Ma OTP la " . $otp_code . ". Ma OTP chi dung mot lan de dang nhap  he thong!";
        $contentSms = "LUCI. Ma OTP la ".$otp_code.". Ma OTP chi dung mot lan de dang nhap he thong!";
        $payload = [
            'to' => $this->phone,
            'utf' => true,
            'content' => $contentSms,
        ];
        $payload = array_merge($payload, $SmsCmc);
        QueueLib::channelSms(json_encode($payload), true);
    }

    private function voiceOtp($code, $phone){
        // $voiceOtp = new CgvVoiceOtp();
        // $voiceOtp->sendOtpVoice($code, $phone);
        $voiceOtp = new CmsVoiceOtp();
        $voiceOtp->sendOtpVoice($code, $phone);
    }

    public function login()
    {
        $jwt = null;
        try {
            $access_token = null;
            //kiểm tra thời gian gần nhất tạo mã
            $verifyCode = VerifyCode::find()
                ->where(['type' => VerifyCode::TYPE_LOGIN_RESIDENT_USER, 'status' => VerifyCode::STATUS_NOT_VERIFY])
                ->andWhere(['like', 'payload', $this->phone])
                ->one();
            if (!$verifyCode) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', 'Mã OTP không hợp lệ'),
                ];
            }

            if ($verifyCode->expired_at < time()) {
                $verifyCode->status = VerifyCode::STATUS_VERIFY_ERROR;
                if (!$verifyCode->save()) {
                    Yii::error($verifyCode->errors);
                }
                return [
                    'success' => false,
                    'message' => Yii::t('resident', 'Mã OTP đã hết hạn'),
                ];
            }

            $payload = json_decode($verifyCode->payload, true);
            if (empty($payload['otp_code']) || $payload['otp_code'] !== $this->otp_code) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', 'Mã OTP không hợp lệ'),
                ];
            }

            $verifyCode->status = VerifyCode::STATUS_VERIFY;
            if (!$verifyCode->save()) {
                Yii::error($verifyCode->errors);
            }
            if (!empty($payload['phone'])) {
                $resident = ResidentUser::findByPhone($payload['phone']);
                // if (empty($resident)) {
                //     $resident = new ResidentUser();
                //     $resident->phone = $payload['phone'];
                //     $resident->status_verify_phone = ResidentUser::STATUS_VERIFY;
                //     $resident->status = ResidentUser::STATUS_ACTIVE;
                //     $resident->active_app = ResidentUser::ACTIVE_APP;
                //     $resident->setPassword(time());
                //     if (!$resident->save()) {
                //         return [
                //             'success' => false,
                //             'message' => Yii::t('resident', 'System busy'),
                //             'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                //             'error' => $resident->errors
                //         ];
                //     }
                // }
                if(!empty($resident))
                {
                    //đánh dấu cài app vào bảng apartmentMap
                    ApartmentMapResidentUser::updateAll(['install_app' => ApartmentMapResidentUser::INSTALL_APP], ['resident_user_phone' => $resident->phone]);

                    if ($resident->status != ResidentUser::STATUS_ACTIVE) {
                        throw new Exception(Yii::t('resident', "User not found or is activated"));
                    }
                    if ($resident->active_app !== ResidentUser::ACTIVE_APP) {
                        $resident->active_app = ResidentUser::ACTIVE_APP;
                        if (!$resident->save()) {
                            return [
                                'success' => false,
                                'message' => Yii::t('resident', 'System busy'),
                                'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                                'error' => $resident->errors
                            ];
                        }
                    }
                    $jwt_config = Yii::$app->params['jwt-config'];
                    $payload = array(
                        'iss' => $jwt_config['iss'],
                        'aud' => $jwt_config['aud'],
                        'exp' => $jwt_config['time'],
                        'jti' => $resident->id
                    );
                    $jwt = ResidentUser::generateApiToken($payload);

                    //sinh refresh token
                    $payloadRefresh = $payload;
                    $payloadRefresh['time_refresh'] = $jwt_config['time_refresh'];
                    $jwtRefresh = ResidentUser::generateApiToken($payloadRefresh);

                    //Hủy đang nhập trên các thiết bị khác
                    ResidentUserAccessToken::deleteAll(['resident_user_id' => $resident->id]);
                    ResidentUserDeviceToken::deleteAll(['resident_user_id' => $resident->id]);

                    //Tạo token login mới
                    $tokenLogin = new ResidentUserAccessToken();
                    $tokenLogin->resident_user_id = $resident->id;
                    $tokenLogin->token = $jwt;
                    $tokenLogin->setTokenHash();
                    $tokenLogin->expired_at = $jwt_config['time'];
                    if (!$tokenLogin->save()) {
                        return [
                            'success' => false,
                            'message' => Yii::t('resident', 'System busy'),
                            'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                            'error' => $tokenLogin->errors
                        ];
                    }

                    $tokenLoginRefresh = new ResidentUserAccessToken();
                    $tokenLoginRefresh->resident_user_id = $resident->id;
                    $tokenLoginRefresh->type = ResidentUserAccessToken::TYPE_REFRESH_TOKEN;
                    $tokenLoginRefresh->token = $jwtRefresh;
                    $tokenLoginRefresh->setTokenHash();
                    $tokenLoginRefresh->expired_at = $jwt_config['time_refresh'];
                    if (!$tokenLoginRefresh->save()) {
                        return [
                            'success' => false,
                            'message' => Yii::t('resident', 'System busy'),
                            'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                            'error' => $tokenLoginRefresh->errors
                        ];
                    }
                    return [
                        'success' => true,
                        'access_token' => $jwt,
                        'refresh_token' => $jwtRefresh,
                        'info_user' => ResidentUserResponse::findOne(['id' => $resident->id]),
                        'apartments' => ApartmentMapResidentUserResponse::find()->where(['resident_user_id' => $resident->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all()
                    ];
                }
                
            }
            return [
                'success' => true
            ];
            // return [
            //     'success' => false,
            //     'message' => Yii::t('resident', 'Phone empty'),
            // ];
        } catch (\Exception $e) {
            Yii::error($e, 'Errors login');
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
            ];
        }

    }
}
