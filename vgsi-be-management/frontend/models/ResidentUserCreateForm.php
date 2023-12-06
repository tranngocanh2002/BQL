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
 *   @SWG\Xml(name="ResidentUserCreateForm")
 * )
 */
class ResidentUserCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - sử dụng khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Email")
     * @var string
     */
    public $email;

    /**
     * @SWG\Property(description="Phone - chỉ cần khi create")
     * @var string
     */
    public $phone;

    /**
     * @SWG\Property(description="First Name")
     * @var string
     */
    public $first_name;

    /**
     * @SWG\Property(description="Last Name")
     * @var string
     */
    public $last_name;

    /**
     * @SWG\Property(description="Avatar")
     * @var string
     */
    public $avatar;

    /**
     * @SWG\Property(description="Status", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="Gender", default=1, type="integer")
     * @var integer
     */
    public $gender;

    /**
     * @SWG\Property(description="Birthday", default=1, type="integer")
     * @var integer
     */
    public $birthday;

    /**
     * @SWG\Property(description="Type - nhóm cư dân : 0 - thành viên, 1 - chủ hộ", default=1, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="Apartment Id : id căn hộ", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="Type relationship : quan hệ với chủ hộ", default=1, type="integer")
     * @var integer
     */
    public $type_relationship;



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required', "on" => ['create']],
            [['email', 'phone', 'first_name', 'last_name', 'avatar'], 'string'],
            [['gender', 'status', 'birthday' , 'type', 'apartment_id', 'type_relationship'], 'integer'],
            [['apartment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_id' => 'id']],
            [['id'], 'required', "on" => ['update']],
            [['id'], 'integer', "on" => ['update']],
            [['phone'], 'validateMobile']
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'invalid phone number'));
        }
    }

    public function create(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $residentUser = ResidentUser::findOne(['phone' => $this->phone, 'is_deleted' => ResidentUser::NOT_DELETED]);
            if($residentUser){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Phone exist"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $residentUser = new ResidentUser();
            $residentUser->load(CUtils::arrLoad($this->attributes),'');
            $residentUser->setPassword(time());
            $residentUser->status = ResidentUser::STATUS_ACTIVE;
            if(!$residentUser->save()){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $residentUser->getErrors()
                ];
            }else{
                //nếu tồn tại thông tin để map user vào căn hộ
                if(!empty($this->type) && !empty($this->apartment_id)){
                    //check chủ hộ
                    if($this->type == ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD){
                        $apartmentMapResidentUserCheck = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                        if($apartmentMapResidentUserCheck){
                            $transaction->rollBack();
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "The apartment has a head of household"),
                                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            ];
                        }
                    }
                    //map resident user vào căn hộ vừa tạo
                    $apartment = Apartment::findOne(['id' => $this->apartment_id]);
                    $apartmentMapResidentUser = ApartmentMapResidentUser::getOrCreate($apartment, $residentUser, $this);
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
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Create success"),
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

    public function update(){
        $item = ResidentUserResponse::findOne(['id' => (int)$this->id]);
        if($item){
            $item->load(CUtils::arrLoad($this->attributes), '');
            if(!$item->save()){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }else{
                $item->updateApartmentMap();
                return $item;
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
