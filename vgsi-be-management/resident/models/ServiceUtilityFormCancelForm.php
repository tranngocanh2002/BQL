<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\Job;
use common\models\ServiceUtilityForm;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityFormCancelForm")
 * )
 */
class ServiceUtilityFormCancelForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Apartment Id")
     * @var integer
     */
    public $apartment_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'apartment_id'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceUtilityForm::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function cancel()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUser)){
                Yii::error('apartmentMapResidentUser empty');
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $item = ServiceUtilityForm::findOne(['id' => $this->id, 'status' => [Job::STATUS_NEW, Job::STATUS_DOING], 'created_by' => $user->id, 'apartment_id' => $this->apartment_id]);
            if(!$item){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data")
                ];
            }
            $item->status = ServiceUtilityForm::STATUS_CANCEL;
            if(!$item->save()){
                Yii::error($item->errors);
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "System busy")
                ];
            }
            $transaction->commit();
            $item->sendNotifyToManagementUser(null, $user, ServiceUtilityForm::CANCEL);
            return [
                'success' => true,
                'message' => Yii::t('resident', "Delete success"),
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('resident', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
    }
}
