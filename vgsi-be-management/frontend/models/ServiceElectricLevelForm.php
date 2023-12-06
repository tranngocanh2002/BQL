<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use common\models\ServiceElectricLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceElectricLevelForm")
 * )
 */
class ServiceElectricLevelForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="name", default="Mức 1", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="name en", default="Mức 1", type="string")
     * @var string
     */
    public $name_en;

    /**
     * @SWG\Property(description="description", default="", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="from level", default=1, type="integer")
     * @var integer
     */
    public $from_level;

    /**
     * @SWG\Property(description="to level", default=1, type="integer")
     * @var integer
     */
    public $to_level;

    /**
     * @SWG\Property(description="price", default=1, type="integer")
     * @var integer
     */
    public $price;

    /**
     * @SWG\Property(description="service map management id", default=0, type="integer")
     * @var integer
     */
    public $service_map_management_id;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['name', 'service_map_management_id', 'from_level', 'to_level', 'price'], 'required'],
            [['id', 'service_map_management_id', 'from_level', 'to_level', 'price'], 'integer'],
            [['name', 'name_en', 'description'], 'string'],
            [['service_map_management_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceMapManagement::className(), 'targetAttribute' => ['service_map_management_id' => 'id']],
        ];
    }

    public function create()
    {
        $user = Yii::$app->user->getIdentity();
        $serviceMapManagement = ServiceMapManagement::findOne(['id' => $this->service_map_management_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED, 'building_cluster_id' => $user->building_cluster_id]);
        if(empty($serviceMapManagement)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServiceElectricLevel = new ServiceElectricLevel();
        $ServiceElectricLevel->load(CUtils::arrLoad($this->attributes), '');
        $ServiceElectricLevel->building_cluster_id = $user->building_cluster_id;
        $ServiceElectricLevel->service_id = $serviceMapManagement->service_id;
        if (!$ServiceElectricLevel->save()) {
            Yii::error($ServiceElectricLevel->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceElectricLevel->getErrors()
            ];
        } else {
            return ServiceElectricLevelResponse::findOne(['id' => $ServiceElectricLevel->id]);
        }
    }

    public function update()
    {
        if(!empty($this->service_id) && !empty($this->service_provider_id)){
            $ServiceElectricLevelCheck = ServiceElectricLevel::find()
                ->where(['service_id' => $this->service_id, 'service_provider_id' => $this->service_provider_id])
                ->andWhere(['<>', 'id', $this->id])->one();
            if(!empty($ServiceElectricLevelCheck)){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Service Exist"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
        }
        $ServiceElectricLevel = ServiceElectricLevelResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceElectricLevel) {
            $ServiceElectricLevel->load(CUtils::arrLoad($this->attributes), '');
            if (!$ServiceElectricLevel->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceElectricLevel->getErrors()
                ];
            } else {
                return $ServiceElectricLevel;
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
        $ServiceElectricLevel = ServiceElectricLevel::findOne($this->id);
        if($ServiceElectricLevel->delete()){
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
