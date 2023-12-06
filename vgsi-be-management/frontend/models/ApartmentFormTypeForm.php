<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\Post;
use common\models\ApartmentFormType;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentFormTypeForm")
 * )
 */
class ApartmentFormTypeForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Name")
     * @var string
     */
    public $name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
            [['id'], 'required', "on" => ['update', 'delete']],
            [['id'], 'integer'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $item = new ApartmentFormType();
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->building_cluster_id = $buildingCluster->id;
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
            return ApartmentFormTypeResponse::findOne(['id' => $item->id]);
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
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $item = ApartmentFormTypeResponse::findOne(['id' => (int)$this->id, 'building_cluster_id' => $buildingCluster->id]);
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
        $post = Apartment::findOne(['form_type' => $this->id, 'is_deleted' => Post::NOT_DELETED]);
        if(!empty($post)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Type contains Apartment, not deleted"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item = ApartmentFormType::findOne($this->id);
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
