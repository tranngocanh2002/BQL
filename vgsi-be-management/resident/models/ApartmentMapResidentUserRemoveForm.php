<?php

namespace resident\models;

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
     * @SWG\Property(description="Resident Id", default=1, type="integer")
     * @var integer
     */
    public $resident_id;



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id', 'resident_id'], 'required'],
            [['apartment_id', 'resident_id'], 'integer'],
            [['apartment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_id' => 'id']],
            [['resident_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResidentUser::className(), 'targetAttribute' => ['resident_id' => 'id']],
        ];
    }

    public function remove()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            //kiểm tra chủ hộ, nếu là chủ hộ mới được bỏ thành viên ra khỏi căn hộ
            $apartmentMapResidentUserCheck = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUserCheck)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Not the head of the household"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_id' => $this->resident_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUser)){
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Residents are not in the apartment"),
                ];
            }

            //Them vao lich su da o cua cu dan
            $historyMap = new HistoryResidentMapApartment();
            $historyMap->apartment_id = $this->apartment_id;
            $historyMap->apartment_name = $apartmentMapResidentUser->apartment_name;
            $historyMap->apartment_parent_path = $apartmentMapResidentUser->apartment_parent_path;
            $historyMap->resident_user_id = $this->resident_id;
            $historyMap->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
            $historyMap->time_in = $apartmentMapResidentUser->created_at;
            $historyMap->type = $apartmentMapResidentUser->type;
            $historyMap->time_out = time();
            if(!$historyMap->save()){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "System busy"),
                    'errors' => $apartmentMapResidentUser->getErrors(),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }

            $apartmentMapResidentUser->is_deleted = ApartmentMapResidentUser::DELETED;
            $apartmentMapResidentUser->deleted_at = time();
            if(!$apartmentMapResidentUser->save()){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "System busy"),
                    'errors' => $apartmentMapResidentUser->getErrors(),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
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
            if($is_update === true){
                if (!$apartment->save()) {
                    $transaction->rollBack();
                    Yii::error($apartment->getErrors());
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', "System busy"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
            }

            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('resident', "Remove success"),
            ];
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
