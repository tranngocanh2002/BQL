<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\BuildingArea;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="BuildingAreaDeleteForm")
 * )
 */
class BuildingAreaDeleteForm extends Model
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => BuildingArea::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingArea = BuildingArea::findOne(['parent_id' => (int)$this->id, 'is_deleted' => BuildingArea::NOT_DELETED]);
            $apartment = Apartment::findOne(['building_area_id' => $this->id, 'is_deleted' => Apartment::NOT_DELETED]);
            if (!empty($buildingArea)) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "The area has floors so it cannot be deleted"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if (!empty($apartment)) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "The floor is flat so it cannot be deleted"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $item = BuildingArea::findOne(['id' => $this->id]);
            $item->is_deleted = BuildingArea::DELETED;
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
