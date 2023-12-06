<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceBill;
use common\models\ServiceBillChagneStatusItem;
use common\models\ServiceMapManagement;
use common\models\ServiceBillChagneStatus;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBillChangeStatusForm")
 * )
 */
class ServiceBillChangeStatusForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="apartment Id", default=1, type="integer")
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
            [['id', 'apartment_id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceBill::className(), 'targetAttribute' => ['id' => 'id']],
            [['apartment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_id' => 'id']],
        ];
    }

    public function changeStatus()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $apartment = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartment)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $ServiceBill = ServiceBillResponse::findOne(['id' => (int)$this->id, 'building_cluster_id' => $apartment->building_cluster_id]);
            if ($ServiceBill) {
                $ServiceBill->load(CUtils::arrLoad($this->attributes), '');
                $ServiceBill->status = ServiceBill::STATUS_UNPAID;
                if (!$ServiceBill->save()) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $ServiceBill->getErrors()
                    ];
                }
                $transaction->commit();
                return [
                    'success' => true,
                    'message' => Yii::t('resident', "Change Status Success"),
                ];
            } else {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
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
