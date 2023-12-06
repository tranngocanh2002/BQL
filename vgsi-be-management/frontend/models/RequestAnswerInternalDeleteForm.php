<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\RequestAnswerInternal;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestAnswerInternalDeleteForm")
 * )
 */
class RequestAnswerInternalDeleteForm extends Model
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => RequestAnswerInternal::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $item = RequestAnswerInternal::findOne(['id' => $this->id]);
            $item->is_deleted = RequestAnswerInternal::DELETED;
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
