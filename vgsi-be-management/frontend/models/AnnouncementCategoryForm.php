<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementCategory;
use common\models\ManagementUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementCategoryForm")
 * )
 */
class AnnouncementCategoryForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Type", default="", type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="", default="Thông báo chung", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="", default="Thông báo chung", type="string")
     * @var string
     */
    public $name_en;

    /**
     * @SWG\Property(description="Color code format hex", default="", type="string")
     * @var string
     */
    public $label_color;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update']],
            [['name', 'label_color'], 'required'],
            [['id', 'type'], 'integer'],
            [['name', 'name_en', 'label_color'], 'string'],
        ];
    }

    public function create()
    {
        $user = Yii::$app->user->getIdentity();
        /**
         * @var $user ManagementUser
         */
        $announce_category = new AnnouncementCategory();
        $announce_category->load(CUtils::arrLoad($this->attributes), '');
        $announce_category->building_cluster_id = $user->building_cluster_id;
        if (!$announce_category->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $announce_category->getErrors()
            ];
        } else {
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Create Category Success"),
            ];
        }
    }

    public function update()
    {
        $announce_category = AnnouncementCategoryResponse::findOne(['id' => (int)$this->id]);
        if ($announce_category) {
            $announce_category->load(CUtils::arrLoad($this->attributes), '');
            if (!$announce_category->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $announce_category->getErrors()
                ];
            } else {
                return $announce_category;
            }
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function delete()
    {
        if(!$this->id){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $announceCampaign = AnnouncementCampaign::findOne(['announcement_category_id' => $this->id]);
        if(!empty($announceCampaign)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Category using, not delete"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $announce_category = AnnouncementCategory::findOne($this->id);
        if($announce_category){
            if($announce_category->delete()){
                return [
                    'success' => true,
                    'message' => Yii::t('frontend', "Delete Success")
                ];
            }else{
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "The system is busy, please try again later"),
                    'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR
                ];
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
