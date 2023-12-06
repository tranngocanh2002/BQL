<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Service;
use common\models\ServiceBillItem;
use common\models\ServiceBuildingConfig;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use common\models\ServiceProvider;
use common\models\ServiceUtilityFree;
use common\models\ServiceWaterLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceMapManagementForm")
 * )
 */
class ServiceMapManagementForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="service id", default=1, type="integer")
     * @var integer
     */
    public $service_id;

    /**
     * @SWG\Property(description="service name", default="Điện", type="string")
     * @var string
     */
    public $service_name;

    /**
     * @SWG\Property(description="service name en", default="Điện", type="string")
     * @var string
     */
    public $service_name_en;

    /**
     * @SWG\Property(description="service icon name", default="Icon name", type="string")
     * @var string
     */
    public $service_icon_name;

    /**
     * @SWG\Property(description="color", default="", type="string")
     * @var string
     */
    public $color;

    /**
     * @SWG\Property(description="service description", default="", type="string")
     * @var string
     */
    public $service_description;

    /**
     * @SWG\Property(description="service provider id", default=0, type="integer")
     * @var integer
     */
    public $service_provider_id;

    /**
     * @SWG\Property(description="Status - 0 : ngừn cung cấp, 1 - đang cung cấp", default=0, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $medias;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['service_id', 'service_provider_id'], 'required'],
            [['id', 'status', 'service_id', 'service_provider_id'], 'integer'],
            [['service_name', 'service_name_en', 'service_description', 'service_icon_name', 'color'], 'string'],
            [['medias'], 'safe'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['service_provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceProvider::className(), 'targetAttribute' => ['service_provider_id' => 'id']],
        ];
    }

    public function create()
    {
        $user = Yii::$app->user->getIdentity();
        $service = Service::findOne(['id' => $this->service_id, 'status' => Service::STATUS_ACTIVE]);
        $serviceProvider = ServiceProvider::findOne(['id' => $this->service_provider_id, 'is_deleted' => ServiceProvider::NOT_DELETED, 'building_cluster_id' => $user->building_cluster_id]);
        if(empty($service) || empty($serviceProvider)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServiceMapManagementCheck = ServiceMapManagement::findOne(['service_id' => $this->service_id, 'service_provider_id' => $this->service_provider_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED]);
        if(!empty($ServiceMapManagementCheck)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Service Exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServiceMapManagement = new ServiceMapManagement();
        $ServiceMapManagement->load(CUtils::arrLoad($this->attributes), '');
        $ServiceMapManagement->building_cluster_id = $user->building_cluster_id;
        if(empty($this->color)){
            $ServiceMapManagement->color = $service->color;
        }
        if(empty($this->service_name)){
            $ServiceMapManagement->service_name = $service->name;
        }
        if(empty($this->service_icon_name)){
            $ServiceMapManagement->service_icon_name = $service->icon_name;
        }
        if(empty($this->service_description)){
            $ServiceMapManagement->service_description = $service->description;
        }
        if (isset($this->medias) && is_array($this->medias)) {
            $ServiceMapManagement->medias = !empty($this->medias) ? json_encode($this->medias) : null;
        }
        $ServiceMapManagement->service_base_url = $service->base_url;
        if (!$ServiceMapManagement->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceMapManagement->getErrors()
            ];
        } else {
            return ServiceMapManagementResponse::findOne(['id' => $ServiceMapManagement->id]);
        }
    }

    public function update()
    {
        if(!empty($this->service_id) && !empty($this->service_provider_id)){
            $ServiceMapManagementCheck = ServiceMapManagement::find()
                ->where(['service_id' => $this->service_id, 'service_provider_id' => $this->service_provider_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED])
                ->andWhere(['<>', 'id', $this->id])->one();
            if(!empty($ServiceMapManagementCheck)){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Service Exist"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
        }
        $ServiceMapManagement = ServiceMapManagementResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceMapManagement) {
            $ServiceMapManagement->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->medias) && is_array($this->medias)) {
                $ServiceMapManagement->medias = !empty($this->medias) ? json_encode($this->medias) : null;
            }
            if (!$ServiceMapManagement->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceMapManagement->getErrors()
                ];
            } else {
                return $ServiceMapManagement;
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
        //check các bảng map để không được xóa
        $serviceBillItem = ServiceBillItem::findOne(['service_map_management_id' => $this->id]);
        $serviceBuildingConfig = ServiceBuildingConfig::findOne(['service_map_management_id' => $this->id]);
        $servicePaymentFee = ServicePaymentFee::findOne(['service_map_management_id' => $this->id]);
        $serviceUtilityFree = ServiceUtilityFree::findOne(['service_map_management_id' => $this->id]);
        $serviceWaterLevel = ServiceWaterLevel::findOne(['service_map_management_id' => $this->id]);
        if(!empty($serviceBillItem) || !empty($serviceBuildingConfig) || !empty($servicePaymentFee) || !empty($serviceUtilityFree) || !empty($serviceWaterLevel)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Service is being used"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        //end check
        $ServiceMapManagement = ServiceMapManagement::findOne($this->id);
        $ServiceMapManagement->is_deleted = ServiceMapManagement::DELETED;
        if($ServiceMapManagement->save()){
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
