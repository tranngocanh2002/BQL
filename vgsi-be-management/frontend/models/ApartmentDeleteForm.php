<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentDeleteForm")
 * )
 */
class ApartmentDeleteForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if ($apartmentMapResidentUser) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Apartment contains residents, not deleted"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            $servicePaymentFee = ServicePaymentFee::findOne(['apartment_id' => $this->id]);
            if ($servicePaymentFee) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Apartment contains ServicePaymentFee, not deleted"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }


            $item = Apartment::findOne(['id' => $this->id]);
            $item->is_deleted = Apartment::DELETED;
            if(!$item->save()){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete success"),
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
