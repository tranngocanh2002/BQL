<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\Job;
use Yii;

use common\helpers\CUtils;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="JobChangeStatusForm")
 * )
 */
class JobChangeStatusForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Status", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'required'],
            [['id', 'status'], 'integer'],
            ['status', 'in', 'range' => [Job::STATUS_CANCEL, Job::STATUS_NEW, Job::STATUS_DOING, Job::STATUS_FINISH]],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Job::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function changeStatus()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = Job::findOne(['id' => $this->id, 'building_cluster_id' => $user->building_cluster_id]);
            if(!$item){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data")
                ];
            }
            $statusNew = $this->status ?? 0;
            $dataOld = clone $item;
            if($this->status == Job::STATUS_CANCEL && !in_array($item->status, [Job::STATUS_NEW, Job::STATUS_DOING])){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Trạng thái không phù hợp")
                ];
            }
            $item->status = $this->status;
            if(!$item->save()){
                Yii::error($item->errors);
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "System busy")
                ];
            }
          
            $item->addCommentAuto(Job::UPDATE_STATUS, $dataOld);
            $transaction->commit();
            $arrPerformer = explode(',', $item->performer);
            $arrPeopleInvolved = explode(',', $item->people_involved);
            $newArrayPerformer = [];
            $newArrayInvolved  = [];
            foreach($arrPerformer as $arrPerformerValue)
            {
                if("" == $arrPerformerValue || $user->id == $arrPerformerValue)
                {
                    continue;
                }
                $newArrayPerformer[] = $arrPerformerValue;
            }
            foreach($arrPeopleInvolved as $arrPeopleInvolvedValue)
            {
                if("" == $arrPeopleInvolvedValue || $user->id == $arrPeopleInvolvedValue)
                {
                    continue;
                }
                $newArrayInvolved[] = $arrPeopleInvolvedValue;
            }
            $aryNotify = array_merge($newArrayPerformer,$newArrayInvolved);
            $fullName = $dataOld->createdUser->fullName;
            $item->sendNotifyChangeStatus(Job::UPDATE_STATUS, $aryNotify,$dataOld,$fullName,$statusNew);
            return [
                'success' => true,
                'message' => Yii::t('resident', "Change status success"),
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
