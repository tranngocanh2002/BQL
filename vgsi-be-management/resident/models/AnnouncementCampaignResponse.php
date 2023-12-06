<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\AnnouncementSurvey;
use common\models\rbac\AuthGroup;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementItemResponse")
 * )
 */
class AnnouncementCampaignResponse extends AnnouncementCampaign
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string",
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="title_en", type="string"),
     * @SWG\Property(property="targets", type="array",
     *      @SWG\Items(type="string", default="string"),
     * ),
     * @SWG\Property(property="image", type="string"),
     * @SWG\Property(property="attach", type="string"),
     * @SWG\Property(property="type", type="integer"),
     * 
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        $user = Yii::$app->user->getIdentity();
        return [
            'id',
            'title',
            'description',
            'content',
            'status',
            'title_en',
            'targets' => function ($model) {
                return (!empty($model->targets)) ? json_decode($model->targets, true) : [];
            },
            'image',
            'attach',
            'created_at',
            'updated_at',
        ];
    }
}
