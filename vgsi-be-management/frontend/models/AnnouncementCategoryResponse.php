<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementCategory;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementCategoryResponse")
 * )
 */
class AnnouncementCategoryResponse extends AnnouncementCategory
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="type", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="label_color", type="string"),
     * @SWG\Property(property="count_announcement", type="integer", description="Tổng thông báo của danh mục"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'type',
            'name',
            'name_en',
            'label_color',
            'count_announcement' => function($model){
                return (int)AnnouncementCampaign::find()->where(['announcement_category_id' => $model->id])->count();
            },
            'created_at',
            'updated_at',
        ];
    }
}
