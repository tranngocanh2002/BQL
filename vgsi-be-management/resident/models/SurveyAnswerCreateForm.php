<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\AnnouncementSurvey;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="SurveyAnswerCreateForm")
 * )
 */
class SurveyAnswerCreateForm extends Model
{
    /**
     * @SWG\Property(description="apartment_id: id căn hộ nhận thông báo khảo sát", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="announcement_campaign_id: id thông báo khảo sát", default=1, type="integer")
     * @var integer
     */
    public $announcement_campaign_id;

    /**
     * @SWG\Property(description="Status: 1 đồng ý, 2 không đồng ý")
     * @var integer
     */
    public $status;

    public $apartment;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id', 'announcement_campaign_id', 'status'], 'required'],
            [['apartment_id', 'announcement_campaign_id', 'status'], 'integer'],
            [['apartment_id'], 'validateApartment'],
            [['announcement_campaign_id'], 'validateCampaign']
        ];
    }

    public function validateApartment($attribute, $params, $validator)
    {
        $this->apartment = Apartment::findOne($this->apartment_id);
        if (empty($this->apartment)) {
            $this->addError($attribute, Yii::t('resident', 'Căn hộ không tồn tại'));
        }
    }

    public function validateCampaign($attribute, $params, $validator)
    {
        $announcementCampaign = AnnouncementCampaign::find()->where([ 'id' => $this->announcement_campaign_id, 'is_survey' => AnnouncementCampaign::IS_SURVEY])->one();
        if (empty($announcementCampaign)) {
            $this->addError($attribute, Yii::t('resident', 'Không có thông báo phù hợp'));
        }
    }
    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            //check quyen tra loi
            $announcementSurvey = AnnouncementSurvey::findOne(['announcement_campaign_id' => $this->announcement_campaign_id, 'apartment_id' => $this->apartment_id, 'resident_user_id' => $user->id]);
            if(empty($announcementSurvey)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Không có quyền thực hiện khảo sát"),
                ];
            }
            if($announcementSurvey->status !== AnnouncementSurvey::STATUS_DEFAULT){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Trạng thái không phù hợp"),
                ];
            }
            $announcementSurvey->announcement_campaign_id = $this->announcement_campaign_id;
            $announcementSurvey->apartment_capacity = $this->apartment->capacity;
            $announcementSurvey->status = $this->status;
            if (!$announcementSurvey->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $announcementSurvey->getErrors()
                ];
            }
            $transaction->commit();
            $sums = AnnouncementSurvey::find()->select('status, count(*) as total_answer, sum(apartment_capacity) as apartment_capacity')->where(['announcement_campaign_id' => 1])->groupBy(['status'])->all();
            $res = [];
            foreach ($sums as $sum) {
                $res[] = [
                    'status' => $sum->status,
                    'total_apartment_capacity' => $sum->apartment_capacity,
                    'total_answer' => $sum->total_answer,
                ];
            }
            return $res;
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
