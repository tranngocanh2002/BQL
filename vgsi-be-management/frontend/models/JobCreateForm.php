<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Job;
use common\models\Request;
use common\models\RequestAnswer;
use common\models\RequestMapAuthGroup;
use rmrevin\yii\fontawesome\AssetBundle;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="JobCreateForm")
 * )
 */
class JobCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="title: tiêu đề")
     * @var string
     */
    public $title;

    /**
     * @SWG\Property(description="description: nội dung")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(property="performer", type="array", description="những người xử lý",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $performer;

    /**
     * @SWG\Property(property="people_involved", type="array", description="những người liên quan",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $people_involved;

    /**
     * @SWG\Property(description="prioritize")
     * @var integer
     */
    public $prioritize;

    /**
     * @SWG\Property(description="Thời gian bắt đầu")
     * @var integer
     */
    public $time_start;

    /**
     * @SWG\Property(description="Thời gian kết thúc")
     * @var integer
     */
    public $time_end;

    /**
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $medias;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update']],
            [['title'], 'required'],
            [['title', 'description'], 'string'],
            [['id', 'time_start', 'time_end', 'prioritize'], 'integer'],
            [['performer', 'people_involved', 'medias'], 'safe'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = new Job();
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->performer) && is_array($this->performer)) {
                $item->performer = ','.implode(",",$this->performer).',';
            }
            if (isset($this->people_involved) && is_array($this->people_involved)) {
                $item->people_involved = ','.implode(",",$this->people_involved).',';
            }
            if (isset($this->medias) && is_array($this->medias)) {
                $item->medias = json_encode($this->medias);
            }
            $item->building_cluster_id = $user->building_cluster_id;
            $item->flag_check_send_reminder = 0;
            if (!$item->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $transaction->commit();
            $arrPerformer = explode(',', $item->performer);
            $item->sendNotifyToPerformer(Job::CREATE, $arrPerformer);
            $arrPeopleInvolved = explode(',', $item->people_involved);
            $item->sendNotifyToPeopleInvolved(Job::CREATE, $arrPeopleInvolved);
            $item->addCommentAuto(Job::CREATE, []);
            return JobResponse::findOne(['id' => $item->id]);
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

    public function update()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = Job::findOne(['id' => (int)$this->id, 'created_by' => $user->id]);
            if ($item) {
                $dataOld = clone $item;
                $arrPerformerOld = explode(',', $item->performer);
                $arrPeopleInvolvedOld = explode(',', $item->people_involved);
                $item->load(CUtils::arrLoad($this->attributes), '');
                $item->updated_at_by_user = time();
                if (isset($this->performer) && is_array($this->performer)) {
                    $item->performer = ','.implode(",",$this->performer).',';
                }
                if (isset($this->people_involved) && is_array($this->people_involved)) {
                    $item->people_involved = ','.implode(",",$this->people_involved).',';
                }
                if (isset($this->medias) && is_array($this->medias)) {
                    $item->medias = json_encode($this->medias);
                }
                if (!$item->save()) {
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
                $transaction->commit();
                $arrPerformerNew = explode(',', $item->performer);
                $arrPerformerSend = array_diff($arrPerformerNew, $arrPerformerOld);
                $item->sendNotifyToPerformer(Job::UPDATE, $arrPerformerSend);
                $arrPeopleInvolvedNew = explode(',', $item->people_involved);
                $arrPeopleInvolvedSend = array_diff($arrPeopleInvolvedNew, $arrPeopleInvolvedOld);
                $item->sendNotifyToPeopleInvolved(Job::UPDATE, $arrPeopleInvolvedSend);
                $item->addCommentAuto(Job::UPDATE, $dataOld);
                return JobResponse::findOne(['id' => $item->id]);
            } else {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
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
