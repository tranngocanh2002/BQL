<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Job;
use common\models\JobComment;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="JobCommentDeleteForm")
 * )
 */
class JobCommentDeleteForm extends Model
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => JobComment::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = JobComment::findOne(['id' => (int)$this->id, 'created_by' => $user->id, 'type' => JobComment::TYPE_COMMENT]);
            if (!$item) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $item->delete();
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
