<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use common\models\VerifyCode;
use common\helpers\CgvVoiceOtp;
use common\helpers\QueueLib;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserUpdateForm")
 * )
 */
class ApartmentMapResidentUserUpdateForm extends Model
{
    /**
     * @SWG\Property(description="apartment map resident user id", default=1, type="integer")
     * @var integer
     */
    public $apartment_map_resident_user_id;

    /**
     * @SWG\Property(description="type relationship", default=7, type="integer")
     * @var integer
     */
    public $type_relationship;

    /**
     * @SWG\Property(description="Resident phone")
     * @var string
     */
    public $resident_phone;

    /**
     * @SWG\Property(description="Resident name")
     * @var string
     */
    public $resident_name;

    /**
     * @SWG\Property(description="Resident email")
     * @var string
     */
    public $resident_email;

    /**
     * @SWG\Property(description="Resident cmtnd")
     * @var string
     */
    public $cmtnd;
    /**
     * @SWG\Property(description="Resident ngày cấp cmtnd")
     * @var integer
     */
    public $ngay_cap_cmtnd;
    /**
     * @SWG\Property(description="is_check_cmtnd check hiển thị thị thực và ...")
     * @var integer
     */
    public $is_check_cmtnd;

    /**
     * @SWG\Property(description="birthday")
     * @var integer
     */
    public $birthday;

    /**
     * @SWG\Property(description="gender")
     * @var integer
     */
    public $gender;

    /**
     * @SWG\Property(description="Resident noi cap cmtnd")
     * @var string
     */
    public $noi_cap_cmtnd;

    /**
     * @SWG\Property(description="Resident nationality")
     * @var string
     */
    public $nationality;

    /**
     * @SWG\Property(description="Resident work")
     * @var string
     */
    public $work;

    /**
     * @SWG\Property(description="Resident so_thi_thuc")
     * @var string
     */
    public $so_thi_thuc;

    /**
     * @SWG\Property(description="Resident ngay_het_han_thi_thuc")
     * @var integer
     */
    public $ngay_het_han_thi_thuc;

    /**
     * @SWG\Property(description="Resident ngay_dang_ky_tam_chu")
     * @var integer
     */
    public $ngay_dang_ky_tam_chu;

    /**
     * @SWG\Property(description="Resident ngay_dang_ky_nhap_khau")
     * @var integer
     */
    public $ngay_dang_ky_nhap_khau;

    /**
     * @SWG\Property(description="Resident phone")
     * @var integer
     */
    public $phone;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_phone', 'apartment_map_resident_user_id'], 'required'],
            [['resident_name', 'resident_phone', 'resident_email', 'cmtnd', 'noi_cap_cmtnd', 'nationality', 'work', 'so_thi_thuc'], 'string'],
            [['birthday', 'gender', 'apartment_map_resident_user_id', 'type_relationship', 'ngay_cap_cmtnd', 'ngay_dang_ky_nhap_khau', 'ngay_dang_ky_tam_chu', 'ngay_het_han_thi_thuc','is_check_cmtnd'], 'integer'],
            [['resident_email'], 'email', 'message' => Yii::t('frontend', 'Invalid email')],
            [['resident_phone'], 'validateMobile'],
            [['apartment_map_resident_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => ApartmentMapResidentUser::className(), 'targetAttribute' => ['apartment_map_resident_user_id' => 'id']],
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'invalid phone number'));
        }
    }

    public function update()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $apartmentMapResidentUserOld = ApartmentMapResidentUser::findOne(['id' => $this->apartment_map_resident_user_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if (!empty($this->resident_phone) && $apartmentMapResidentUserOld) {
                $apartment = Apartment::findOne(['id' => $apartmentMapResidentUserOld->apartment_id]);
                $apartmentMapResidentUser = ApartmentMapResidentUser::getOrCreate($apartment, null, $this);
                if(!$apartmentMapResidentUser['success']){
                    $transaction->rollBack();
                    return $apartmentMapResidentUser;
                }
            }
            // if(!empty($this->resident_phone))
            // {   
            //     $residentUser = ResidentUser::find()->where(['phone'=>$this->resident_phone])->one();
            //     $residentUser->birthday = $this->birthday ?? "";
            //     $residentUser->cmtnd = $this->cmtnd ?? 11111111111;
            //     $residentUser->gender = $this->gender ?? 0;
            //     // $residentUser->is_check_cmtnd = $this->is_check_cmtnd ?? 1;
            //     $residentUser->nationality = $this->nationality ?? "";
            //     $residentUser->ngay_cap_cmtnd = $this->ngay_cap_cmtnd;
            //     $residentUser->ngay_dang_ky_nhap_khau = $this->ngay_dang_ky_nhap_khau ?? 123455666;
            //     $residentUser->ngay_dang_ky_tam_chu = $this->ngay_dang_ky_tam_chu ?? 123455666;
            //     $residentUser->noi_cap_cmtnd = $this->noi_cap_cmtnd ?? "";
            //     $residentUser->email = $this->resident_email ?? "hoanbgs@gmail.com";
            //     $residentUser->first_name = $this->resident_name ?? "test";
            //     $residentUser->phone = $this->resident_phone ?? "84516323111";
            //     if(!$residentUser->save())
            //     {
            //         return [
            //             'success' => false,
            //             'message' => Yii::t('frontend', "Update false"),
            //         ];
            //     }
            // }else{
            else{
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Residents were in the apartment"),
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update success"),
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

}
