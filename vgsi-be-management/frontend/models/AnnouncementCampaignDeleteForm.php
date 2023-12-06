<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementItem;
use common\models\AnnouncementItemSend;
use common\models\AnnouncementSurvey;
use common\models\AnnouncementCampaign;
use Yii;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementCampaignDeleteForm")
 * )
 */
class AnnouncementCampaignDeleteForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc", default=1, type="integer")
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
        ];
    }

    public function delete()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $item = AnnouncementCampaignResponse::findOne(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id]);
        if ($item) {
            AnnouncementItem::deleteAll(['announcement_campaign_id' => $item->id, 'building_cluster_id' => $buildingCluster->id]);
            AnnouncementItemSend::deleteAll(['announcement_campaign_id' => $item->id, 'building_cluster_id' => $buildingCluster->id]);
            AnnouncementSurvey::deleteAll(['announcement_campaign_id' => $item->id, 'building_cluster_id' => $buildingCluster->id]);
            AnnouncementCampaign::deleteAll(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id]);
            // return [
            //     'success' => false,
            //     'message' => Yii::t('frontend', $item->id ."/".$buildingCluster->id),
            //     'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            // ];
            // $item->delete();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Success"),
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
