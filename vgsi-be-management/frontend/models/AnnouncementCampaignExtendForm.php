<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\helpers\ErrorCode;
use common\helpers\StringUtils;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementCategory;
use common\models\AnnouncementItem;
use common\models\AnnouncementItemSend;
use common\models\AnnouncementTemplate;
use common\models\Apartment;
use common\models\BuildingArea;
use common\models\ServiceDebt;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementCampaignExtendForm")
 * )
 */
class AnnouncementCampaignExtendForm extends Model
{
    /**
     * @SWG\Property(description="Id - announcement campaign", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="survey deadline - Thời điểm gia hạn mới", type="integer")
     * @var integer
     */
    public $survey_deadline;

    public $announcementCampaign;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'survey_deadline'], 'required'],
            [['id', 'survey_deadline'], 'integer'],
            [['id'], 'validateCampaign'],
            [['survey_deadline'], 'validateSurveyDeadline'],
        ];
    }

    public function validateCampaign($attribute, $params, $validator)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $this->announcementCampaign = AnnouncementCampaign::find()->where(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id, 'is_survey' => AnnouncementCampaign::IS_SURVEY])->one();
        if (empty($this->announcementCampaign)) {
            $this->addError($attribute, Yii::t('resident', 'Không có thông báo phù hợp'));
        }
    }

     public function validateSurveyDeadline($attribute, $params, $validator)
    {
        if (empty($this->survey_deadline) || ($this->survey_deadline <= time())) {
            $this->addError($attribute, Yii::t('resident', 'Thời gian gia hạn không hợp lệ'));
        }
    }

    public function extend()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $announcementCampaign = AnnouncementCampaign::findOne($this->id);
            $announcementCampaign->survey_deadline = $this->survey_deadline;
            if (!$announcementCampaign->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $announcementCampaign->getErrors()
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Gia hạn thành công"),
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
