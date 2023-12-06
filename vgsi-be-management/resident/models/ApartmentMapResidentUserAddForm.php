<?php

namespace resident\models;

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
class ApartmentMapResidentUserAddForm extends Model
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
     * @SWG\Property(description="Resident avatar")
     * @var string
     */
    public $resident_avatar;

    /**
     * @SWG\Property(description="Resident gender")
     * @var integer
     */
    public $resident_gender;

    /**
     * @SWG\Property(description="Resident birthday")
     * @var integer
     */
    public $resident_birthday;

    /**
     * @SWG\Property(description="Type - vai trò : 0 - Gia đình chủ hộ, 1 - chủ hộ, 2 - khách thuê, 3 - Gia đình khách thuê", default=0, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="Type Relationship - quan hệ với chủ hộ : 0 - Chủ hộ, 1 - Ông/Bà, 2 - Bố/Mẹ, 3 - Vợ/Chồng, 4 - Con, 5 - Anh/chị/em, 6 - Bạn, 7 - Khác", default=0, type="integer")
     * @var integer
     */
    public $type_relationship;

    public $cmtnd;

    public $ngay_cap_cmtnd;

    public $noi_cap_cmtnd;

    public $nationality;

    public $work;

    public $so_thi_thuc;

    public $ngay_het_han_thi_thuc;

    public $ngay_dang_ky_tam_chu;

    public $ngay_dang_ky_nhap_khau;

    public $gender;

    public $birthday;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_phone', 'apartment_id', 'type'], 'required'],
            [['resident_name', 'resident_phone', 'resident_avatar', 'cmtnd', 'noi_cap_cmtnd', 'nationality', 'work', 'so_thi_thuc'], 'string'],
            [['birthday', 'gender', 'apartment_id', 'type', 'type_relationship', 'resident_gender', 'resident_birthday', 'ngay_cap_cmtnd', 'ngay_dang_ky_nhap_khau', 'ngay_dang_ky_tam_chu', 'ngay_het_han_thi_thuc'], 'integer'],
            [['apartment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_id' => 'id']],
            [['resident_phone'], 'validateMobile']
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'invalid phone number'));
        }
    }

    public function add()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            //check resident đã được map vào căn hộ hay chưa
            $apartmentMapResidentUserCheckMap = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $this->resident_phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(!empty($apartmentMapResidentUserCheckMap)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Cư dân đã được thêm vào căn hộ rồi"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            //check chủ hộ
            $apartmentMapResidentUserCheck = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if (empty($apartmentMapResidentUserCheck)) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Not the head of the household"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if ($this->type == ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Cannot add household head"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $apartment = Apartment::findOne(['id' => $this->apartment_id]);
            if (!empty($this->resident_phone)) {
                //kiểm tra resident user nếu chưa tồn tại thì tạo mới theo số điện thoại
                $residentUser = ResidentUser::getOrCreate($this);
                if (!$residentUser['success']) {
                    $transaction->rollBack();
                    return $residentUser;
                }else{
                    $residentUser = $residentUser['residentUser'];
                }

                //map resident user vào căn hộ vừa tạo
                $apartmentMapResidentUser = ApartmentMapResidentUser::getOrCreate($apartment, $residentUser, $this);
                if(!$apartmentMapResidentUser['success']){
                    $transaction->rollBack();
                    return $apartmentMapResidentUser;
                }
                if(!$apartmentMapResidentUser['is_new']){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', "Residents were in the apartment"),
                    ];
                }
            }
            $transaction->commit();
            return ApartmentMapResidentUserResponse::findOne(['resident_user_id' => $residentUser->id, 'apartment_id' => $apartment->id]);
            /*return [
                'success' => true,
                'message' => Yii::t('resident', "Add success"),
            ];*/
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('resident', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                // 'errors' => $ex->getMessage()
            ];
        }
    }
}
