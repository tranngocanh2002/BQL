<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\AnnouncementItemSend;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ReportAnnouncementResponse")
 * )
 */
class ReportAnnouncementResponse extends AnnouncementCampaign
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string"),
     * @SWG\Property(property="announcement_category_id", type="integer"),
     * @SWG\Property(property="announcement_category_name", type="integer"),
     * @SWG\Property(property="announcement_category_label_color", type="integer"),
     * @SWG\Property(property="total_apartment", type="integer", description="tổng căn hộ"),
     * @SWG\Property(property="total_apartment_send", type="integer", description="tổng gửi"),
     * @SWG\Property(property="total_apartment_open", type="integer", description="tổng xem"),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'announcement_category_id',
            'announcement_category_name' => function($model){
                if(!empty($model->announcementCategory)){
                    return $model->announcementCategory->name;
                }
                return '';
            },
            'announcement_category_label_color' => function($model){
                if(!empty($model->announcementCategory)){
                    return $model->announcementCategory->label_color;
                }
                return '';
            },
            'total_apartment' => function($model){
                return (int)AnnouncementItem::find()->where(['announcement_campaign_id' => $model->id])->count();
            },
            'total_apartment_send',
            'total_apartment_open'
        ];
    }
}
