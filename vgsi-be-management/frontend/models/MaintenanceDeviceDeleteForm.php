<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\MaintenanceDevice;
use common\models\Job;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="MaintenanceDeviceDeleteForm")
 * )
 */
class MaintenanceDeviceDeleteForm extends Model
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => MaintenanceDevice::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $item = MaintenanceDevice::findOne(['id' => (int)$this->id, 'is_deleted' => MaintenanceDevice::NOT_DELETED]);
            if (!$item) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $item->is_deleted = MaintenanceDevice::DELETED;
            $item->save();
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
