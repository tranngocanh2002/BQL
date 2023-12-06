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
 *   @SWG\Xml(name="ApartmentMapResidentTypeUpdateForm")
 * )
 */
class ApartmentMapResidentTypeUpdateForm extends Model
{
    /**
     * @SWG\Property(description="ApartmentMapResident id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Type - vai trò : 0 - Gia đình chủ hộ, 1 - chủ hộ, 2 - khách thuê, 3 - Gia đình khách thuê", default=0, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="type relationship - Quan hệ với chủ hộ", default=0, type="integer")
     * @var integer
     */
    public $type_relationship;

    /**
     * @SWG\Property(description="resident_user_phone: số điện thoại chủ hộ hoặc thành viên", default=0, type="string")
     * @var string
     */
    public $resident_user_phone;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'required'],
            [['id', 'type', 'type_relationship'], 'integer'],
            [['resident_user_phone'], 'safe'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => ApartmentMapResidentUser::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }


    /**
     * @return array
     */
    public function process()
    {
        /**
         * @var $map ApartmentMapResidentUser
         */
        $map = ApartmentMapResidentUser::findOne(['id' => $this->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
//        //check chu ho
//        $apartmentMapResidentUserCheck = ApartmentMapResidentUser::findOne(['apartment_id' => $map->apartment_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD]);
//        if($apartmentMapResidentUserCheck){
//            if(ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD == $this->type){
//                //update chủ hộ cũ về thành viên
//                $apartmentMapResidentUserCheck->type = ApartmentMapResidentUser::TYPE_MEMBER;
//                if(!$apartmentMapResidentUserCheck->save()){
//                    return [
//                        'success' => false,
//                        'message' => Yii::t('frontend', "The apartment has a head of household"),
//                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                    ];
//                }
//            }else if($this->type !== ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD && $this->id == $apartmentMapResidentUserCheck->id){
//                return [
//                    'success' => false,
//                    'message' => Yii::t('frontend', "No head of household"),
//                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                ];
//            }
//        }else{
////            chưa có chủ hộ
//            if(ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD !== $this->type) {
//                return [
//                    'success' => false,
//                    'message' => Yii::t('frontend', "No head of household"),
//                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                ];
//            }
//        }
        $phone = $map->resident_user_phone;
        if(10 == strlen($phone))
        {
            $phone = '84'.substr($phone, 1);
        }
        $map->type = $this->type;
        if(isset($this->type_relationship) && ($this->type_relationship >= ApartmentMapResidentUser::TYPE_MEMBER && $this->type_relationship <= ApartmentMapResidentUser::TYPE_RELATIONSHIP_OTHER)){
            $map->type_relationship = $this->type_relationship;
        }
        $map->resident_user_phone = $phone;
        if(isset($this->resident_user_phone)){
            $map->resident_user_phone = $this->resident_user_phone;
        }
        //check còn chủ hộ hay ko
        if($map->type !== ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD){
            $apartmentMapResidentUserCheck = ApartmentMapResidentUser::findOne(['apartment_id' => $map->apartment_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if($apartmentMapResidentUserCheck->id == $map->id){
                 return [
                    'success' => false,
                    'message' => Yii::t('frontend', "No head of household"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
        }
        if($map->save()){
            //loại chủ hộ khác
            if($map->type == ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD){
                $apartmentMapResidentUsers = ApartmentMapResidentUser::find()
                    ->where(['type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_id' => $map->apartment_id])
                    ->andWhere(['<>', 'id', $map->id])
                    ->all();
                foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
                    $apartmentMapResidentUser->type = ApartmentMapResidentUser::TYPE_MEMBER;
                    if(!$apartmentMapResidentUser->save()){
                        Yii::error($apartmentMapResidentUser->errors);
                    }
                }
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Add success"),
            ];
        }else{
            Yii::error($map->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "System busy"),
                'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR
            ];
        }
    }

}
