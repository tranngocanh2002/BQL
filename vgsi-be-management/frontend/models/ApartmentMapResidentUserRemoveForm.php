<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\HistoryResidentMapApartment;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserRemoveForm")
 * )
 */
class ApartmentMapResidentUserRemoveForm extends Model
{
    /**
     * @SWG\Property(description="Apartment Id", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="Resident phone", default=1, type="string")
     * @var string
     */
    public $resident_phone;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id', 'resident_phone'], 'required'],
            [['apartment_id'], 'integer'],
            [['resident_phone'], 'string'],
            [['apartment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_id' => 'id']],
        ];
    }

    public function remove()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $this->resident_phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUser)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data")
                ];
            }
            $apartmentMapResidentUser->addHistory(HistoryResidentMapApartment::IS_REMOVE_APARTMENT);
            $apartmentMapResidentUser->is_deleted = ApartmentMapResidentUser::DELETED;
            $apartmentMapResidentUser->deleted_at = time();
            if(!$apartmentMapResidentUser->save()){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'errors' => $apartmentMapResidentUser->getErrors(),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            //check nếu không còn chủ hộ thì lấy lại chủ hộ
            $checkChuHo = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($checkChuHo)){
                //nếu là xóa chủ hộ thì update thành viên khác thành chủ hộ
                $apartmentMapResidentUserUpdate = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                if($apartmentMapResidentUserUpdate){
                    $apartmentMapResidentUserUpdate->type = ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD;
                    if(!$apartmentMapResidentUserUpdate->save()){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data")
                        ];
                    }
                }
            }
            //Lấy chủ hộ add vào căn hộ
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            $apartment = Apartment::findOne(['id' => $this->apartment_id]);
            $is_update = false;
            if ($apartmentMapResidentUser) {
                if($apartment->resident_user_id !== $apartmentMapResidentUser->resident_user_id){
                    $apartment->resident_user_id = $apartmentMapResidentUser->resident_user_id;
                    $apartment->resident_user_name = $apartmentMapResidentUser->resident_user_first_name;
                    $is_update = true;
                }
            } else {
                $apartment->resident_user_id = null;
                $apartment->resident_user_name = null;
                $is_update = true;
            }

            //nếu căn hộ không còn người ở thì chuyển về trạng thái trống
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUser)){
                $apartment->status = Apartment::STATUS_EMPTY;
                $is_update = true;
            }

            if($is_update === true){
                if (!$apartment->save()) {
                    $transaction->rollBack();
                    Yii::error($apartment->getErrors());
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "System busy"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Remove success"),
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
