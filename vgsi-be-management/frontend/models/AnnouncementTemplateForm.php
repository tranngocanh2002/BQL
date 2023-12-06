<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\AnnouncementTemplate;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementTemplateForm")
 * )
 */
class AnnouncementTemplateForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="name")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="name en")
     * @var string
     */
    public $name_en;

    /**
     * @SWG\Property(description="image")
     * @var string
     */
    public $image;

    /**
     * @SWG\Property(description="content_email")
     * @var string
     */
    public $content_email;

    /**
     * @SWG\Property(description="content_sms")
     * @var string
     */
    public $content_sms;

    /**
     * @SWG\Property(description="type")
     * @var integer
     */
    public $type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['type'], 'integer'],
            [['name', 'name_en', 'image', 'content_email', 'content_sms'], 'string'],
        ];
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $item = new AnnouncementTemplate();
        $item->load(CUtils::arrLoad($this->attributes), '');
        $item->building_cluster_id = $buildingCluster->id;
        if (!$item->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $item->getErrors()
            ];
        }
        return AnnouncementTemplateResponse::findOne(['id' =>$item->id]);
    }

    public function update()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $item = AnnouncementTemplateResponse::findOne(['id' => (int)$this->id, 'building_cluster_id' => $buildingCluster->id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            return $item;
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
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $item = AnnouncementTemplateResponse::findOne(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id]);
        if($item->delete()){
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success")
            ];
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
