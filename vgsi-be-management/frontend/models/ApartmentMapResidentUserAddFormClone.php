<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserAddForm")
 * )
 */
class ApartmentMapResidentUserAddFormClone extends Model
{
    /**
     * @SWG\Property(description="Apartment Id", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

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
     * @SWG\Property(description="Resident Email")
     * @var string
     */
    public $resident_email;

    /**
     * @SWG\Property(description="Type - vai trò : 0 - Gia đình chủ hộ, 1 - chủ hộ, 2 - khách thuê, 3 - Gia đình khách thuê", default=0, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="is_check_cmtnd 1 là được check, khác 1 là không check, default là 0", default=0, type="integer")
     * @var integer
     */
    public $is_check_cmtnd;

    /**
     * @SWG\Property(description="Type Relationship - Quan hệ với chủ hộ: 0 - Chủ hộ, 1 - Ông/Bà, 2 - Bố/Mẹ, 3 - Vợ/Chồng, 4 - Con, 5 - Anh/chị/em, 6 - Bạn, 7 - Khác", default=0, type="integer")
     * @var integer
     */
    public $type_relationship;

    /**
     * @SWG\Property(description="Resident cmtnd")
     * @var string
     */
    public $cmtnd;

    /**
     * @SWG\Property(description="Resident ngay cap cmtnd")
     * @var integer
     */
    public $ngay_cap_cmtnd;

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

    public $birthday;

    public $gender;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_phone', 'apartment_id', 'type'], 'required'],
            [['resident_name', 'resident_phone', 'resident_email', 'cmtnd', 'noi_cap_cmtnd', 'nationality', 'work', 'so_thi_thuc'], 'string'],
            [['birthday', 'gender', 'apartment_id', 'type', 'type_relationship','ngay_cap_cmtnd', 'ngay_dang_ky_nhap_khau', 'ngay_dang_ky_tam_chu', 'ngay_het_han_thi_thuc'], 'integer'],
            [['resident_email'], 'email', 'message' => Yii::t('frontend', 'Invalid email')],
            [['apartment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_id' => 'id']],
            [['resident_phone'], 'validateMobile'],
            // [['is_check_cmtnd'], 'validateIsCheckCmtnd'],
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'invalid phone number'));
        }
    }

    public function validateIsCheckCmtnd()
    {
        // if ( 1 != $this->is_check_cmtnd || 0 != $this->is_check_cmtnd) {
        //     $this->addError($this->is_check_cmtnd, Yii::t('frontend', 'invalid check cmtnd'));
        // }
        if(!empty($this->is_check_cmtnd) &&  (1 == $this->is_check_cmtnd))
        {
            if (empty($this->so_thi_thuc)) {
                $this->addError($this->so_thi_thuc, Yii::t('frontend', 'invalid so thi thuc'));
            }
            if (empty($this->work)) {
                $this->addError($this->work, Yii::t('frontend', 'invalid work'));
            }
        }

    }

    public function add()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $residentPhone = $this->resident_phone;
            if(10 == strlen($residentPhone))
            {
                $residentPhone = preg_replace("/^84/", '0', $this->resident_phone);
            }
            // $this->resident_phone = $residentPhone;
            //check resident đã được map vào căn hộ hay chưa
            $apartmentMapResidentUserCheckMap = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $residentPhone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            // return [
            //     'success' => false,
            //     'message' => Yii::t('frontend', "aaaaaaaaaaaa"),
            //     'data' => [
            //         'resident_phone' => $this->resident_phone,
            //         'residentPhone' => $residentPhone,
            //         'apartmentMapResidentUserCheckMap' => $apartmentMapResidentUserCheckMap
            //     ]
            // ];
            if(!empty($apartmentMapResidentUserCheckMap)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Cư dân đã được thêm vào căn hộ rồi"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
           
            //check chủ hộ
            $apartmentMapResidentUserCheck = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if($apartmentMapResidentUserCheck){
                //nếu có chủ hộ, mà muốn update chủ hộ mới
                if($this->type == ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD){
                    //update chủ hộ cũ về thành viên
                    $apartmentMapResidentUserCheck->type = ApartmentMapResidentUser::TYPE_MEMBER;
                    if(!$apartmentMapResidentUserCheck->save()){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "The apartment has a head of household"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }
                }else if($this->type !== ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD && $this->resident_phone == $apartmentMapResidentUserCheck->resident_user_phone){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "No head of household"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }else{
                //nếu chưa có chủ hộ, mà update mới ko phải chủ hộ
                if($this->type !== ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "No head of household"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }

            $apartment = Apartment::findOne(['id' => $this->apartment_id]);
            $residentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $this->resident_phone]);
            if (!empty($this->resident_phone)) {
                //map resident user vào căn hộ vừa tạo
                $apartmentMapResidentUser = ApartmentMapResidentUser::getOrCreate($apartment, $residentUser ?? null, $this);
                if(!$apartmentMapResidentUser['success']){
                    $transaction->rollBack();
                    return $apartmentMapResidentUser;
                }
                if(!$apartmentMapResidentUser['is_new']){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Residents were in the apartment"),
                    ];
                }
            }
            if(empty($apartment->date_received) && empty($apartment->date_delivery))
            {
                $apartment->date_received = time();
                $apartment->date_delivery = time();
                if(!$apartment->save()){
                    $transaction->rollBack();
                }
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Add success"),
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
