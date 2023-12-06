<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use common\models\VerifyCode;
use common\models\ResidentUserAccessToken;
use common\models\ResidentUserDeviceToken;
use common\helpers\CgvVoiceOtp;
use common\helpers\QueueLib;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserUpdatePhone")
 * )
 */
class ApartmentMapResidentUserUpdatePhone extends Model
{
    
    /**
     * @SWG\Property(description="Resident phone")
     * @var string
     */
    public $phone;

    /**
     * @SWG\Property(description="Resident oldPhone")
     * @var string
     */

    public $oldPhone;
    /**
     * @SWG\Property(description="Resident newPhone")
     * @var string
     */
    public $newPhone;
    
    /**
     * @SWG\Property(description="otp")
     * @var string
     */
    public $otp;

    /**
     * @SWG\Property(description="type_auth")
     * @var integer
     */
    public $type_auth;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['otp', 'phone','newPhone','oldPhone'], 'string'],
            [['type_auth'], 'integer'],
            [['phone','newPhone','oldPhone'], 'validateMobile']
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'invalid phone number'));
        }
    }

    public function changePhone()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try 
        {
            // kiểm tra user theo phone
            $newPhone = $this->newPhone;
            $oldPhone = $this->oldPhone;
            $residentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $newPhone]);
            if (!empty($residentUser)) {
                if($newPhone != '84354613983')
                {
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', 'Số điện thoại đã tồn tại'),
                    ];
                }
                
            }
            // kiểm tra user map với bđs theo phone
            // $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $this->resident_phone]);
            // if(!empty($apartmentMapResidentUser))
            // {
            //     return [
            //         'success' => false,
            //         'message' => Yii::t('frontend', "Residents were in the apartment")
            //     ];
            // }
            // lấy phương thức xác thực
            if(!$this->type_auth)
            {   
                 //hủy mã xác thực cũ
                $verifyCodes = VerifyCode::find()
                    ->where(['type' => VerifyCode::TYPE_CHANGE_PHONE, 'status' => VerifyCode::STATUS_NOT_VERIFY])
                    ->andWhere(['like', 'payload', $newPhone])
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
                if ($in_production == false || in_array($newPhone, $PhoneWhiteListOtp)) {
                    $otp_code = '123456';
                } else {
                    $otp_code = CUtils::generateRandomNumber(6);
                }
                $verifyCode = new VerifyCode();
                $verifyCode->code = VerifyCode::generateCode();
                $verifyCode->type = VerifyCode::TYPE_CHANGE_PHONE;
                $verifyCode->status = VerifyCode::STATUS_NOT_VERIFY;
                $verifyCode->expired_at = time() + 5 * 60;
                $verifyCode->payload = json_encode(['phone' => $newPhone, 'otp_code' => $otp_code]);
                // return [
                //     'success' => false,
                //     'data'    => [
                //         'type_auth' => $this->type_auth,
                //         'phone' => $this->phone,
                //         'otp_code' => $otp_code,
                //         'verifyCode' => $verifyCode->save()
                //     ]
                // ];
                if (!$verifyCode->save()) {
                    Yii::error($verifyCode->errors);
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', 'Tạo mã OTP không thành công'),
                    ];
                }
                //gửi tin nhắn nếu là product
                if ($in_production == true && !in_array($newPhone, $PhoneWhiteListOtp)) {
                    $mode_otp = Yii::$app->params['mode_otp'];
                    if($mode_otp == 'SMS'){
                        self::otpCmc($otp_code);
                    }else{
                        self::voiceOtp($otp_code, $newPhone);
                    }
                }
                $transaction->commit();
                return [
                    'success' => true,
                    'code' => $verifyCode->code,
                    'message' => Yii::t('resident', 'Mã OTP đã được gửi vế số điện thoại, vui lòng nhập mã OTP để đăng nhập vào ứng dụng!'),
                ];
            }

            if(1 == $this->type_auth)
            {   
                $this->updatePhoneResident($oldPhone,$newPhone);
                $this->updatePhoneApartmentMapResident($oldPhone,$newPhone);
                $transaction->commit();
                return [
                    'success' => true,
                    'phone' => $this->phone,
                    'message' => Yii::t('resident', 'Thay đổi số điện thoại thành công'),
                ];
            }
            $transaction->rollBack();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Tạo mã OTP không thành công"),
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
    private function otpCmc($otp_code){
        $SmsCmc = Yii::$app->params['SmsCmc'];
        //$contentSms = "Ma OTP la " . $otp_code . ". Ma OTP chi dung mot lan de dang nhap  he thong!";
        $contentSms = "LUCI. Ma OTP la ".$otp_code.". Ma OTP chi dung mot lan de thay doi so dien thoai!";
        $payload = [
            'to' => $this->phone,
            'utf' => true,
            'content' => $contentSms,
        ];
        $payload = array_merge($payload, $SmsCmc);
        QueueLib::channelSms(json_encode($payload), true);
    }

    private function voiceOtp($code, $phone){
        $voiceOtp = new CgvVoiceOtp();
        $voiceOtp->sendOtpVoice($code, $phone);
    }

    public function VerifyOtpChangePhone()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $jwt = null;
        try 
        {
            $newPhone = $this->newPhone;
            $oldPhone = $this->oldPhone;
            $access_token = null;
            //kiểm tra thời gian gần nhất tạo mã
            // return [
            //     'success' => false,
            //     'this_phone' =>  $this->phone,
            //     'data' =>  $phone,
            // ];
            $verifyCode = VerifyCode::find()
                ->where(['type' => VerifyCode::TYPE_CHANGE_PHONE, 'status' => VerifyCode::STATUS_NOT_VERIFY])
                ->andWhere(['like', 'payload', $newPhone])
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
            if (empty($payload['otp_code']) || $payload['otp_code'] !== $this->otp) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', 'Mã OTP không hợp lệ'),
                ];
            }

            $verifyCode->status = VerifyCode::STATUS_VERIFY;
            if (!$verifyCode->save()) {
                Yii::error($verifyCode->errors);
            }
            if (!empty($payload['phone'])) 
            {
                $this->updatePhoneResident($oldPhone,$newPhone);
                $this->updatePhoneApartmentMapResident($oldPhone,$newPhone);
                
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', 'Thay doi so dien thoai thanh cong')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
            ];
        }
    }
    public function updatePhoneResident($oldPhone = "",$newPhone = ""){
        $residents = ResidentUser::find()->where(['phone'=>$oldPhone])->all();
        foreach($residents as $resident)
        {
            if(!empty($resident))
            {
                if(!empty($newPhone))
                {
                    $resident->phone = $newPhone;
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
            }
        }
        
    }

    public function updatePhoneApartmentMapResident($oldPhone = "",$newPhone = ""){
        $oldPhoneClone = '0'.substr($oldPhone,2);
        $apartmentMapResidentUsers= ApartmentMapResidentUser::find()->where(['resident_user_phone'=> $oldPhone])->all();
        $apartmentMapResidentUsersOldPhoneClones = ApartmentMapResidentUser::find()->where(['resident_user_phone'=> $oldPhoneClone])->all();
        
        foreach($apartmentMapResidentUsers as $apartmentMapResidentUser)
        {
            if(!empty($apartmentMapResidentUser))
            {
                if(!empty($newPhone))
                {
                    $apartmentMapResidentUser->resident_user_phone = $newPhone;
                    if (!$apartmentMapResidentUser->save()) {
                        return [
                            'success' => false,
                            'message' => Yii::t('resident', 'System busy'),
                            'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                            'error' => $apartmentMapResidentUser->errors
                        ];
                    }
                }
            }
        }
        foreach($apartmentMapResidentUsersOldPhoneClones as $apartmentMapResidentUsersOldPhoneClone)
        {
            if(!empty($apartmentMapResidentUsersOldPhoneClone))
            {
                if(!empty($newPhone))
                {
                    $apartmentMapResidentUsersOldPhoneClone->resident_user_phone = $newPhone;
                    if (!$apartmentMapResidentUsersOldPhoneClone->save()) {
                        return [
                            'success' => false,
                            'message' => Yii::t('resident', 'System busy'),
                            'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
                            'error' => $apartmentMapResidentUsersOldPhoneClone->errors
                        ];
                    }
                }
            }
        }
    }
}