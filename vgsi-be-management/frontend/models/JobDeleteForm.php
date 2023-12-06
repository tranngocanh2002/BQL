<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Job;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="JobDeleteForm")
 * )
 */
class JobDeleteForm extends Model
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Job::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = Job::findOne(['id' => (int)$this->id, 'created_by' => $user->id]);
            if (!$item) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            if($item->status !== Job::STATUS_NEW){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Trạng thái công việc không phù hợp, không được Xóa."),
                ];
            }
            if(!empty($item->performer)){
                $arrPerformer = explode(',', $item->performer);
                $item->sendNotifyToPerformer(Job::DELETE, $arrPerformer);
            }
            if(!empty($item->people_involved)){
                $arrPeopleInvolved = explode(',', $item->people_involved);
                $item->sendNotifyToPeopleInvolved(Job::DELETE, $arrPeopleInvolved);
            }
            $item->addCommentAuto(Job::DELETE);
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
