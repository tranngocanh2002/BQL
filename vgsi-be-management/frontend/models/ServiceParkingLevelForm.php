<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServiceParkingFee;
use common\models\ServiceParkingLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceParkingLevelForm")
 * )
 */
class ServiceParkingLevelForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="name", default="Loại xe", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="name en", default="Loại xe", type="string")
     * @var string
     */
    public $name_en;

    /**
     * @SWG\Property(description="description", default="", type="string")
     * @var string
     */
    public $description;

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
            [['service_map_management_id', 'price'], 'required'],
            [['id', 'service_map_management_id', 'price'], 'integer'],
            [['name', 'name_en', 'description'], 'string'],
        ];
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $serviceMapManagement = ServiceMapManagement::findOne(['id' => $this->service_map_management_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
        if(empty($serviceMapManagement)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServiceParkingLevel = new ServiceParkingLevel();
        $ServiceParkingLevel->load(CUtils::arrLoad($this->attributes), '');
        $ServiceParkingLevel->generateCode();
        $ServiceParkingLevel->building_cluster_id = $buildingCluster->id;
        $ServiceParkingLevel->service_id = $serviceMapManagement->service_id;
        if (!$ServiceParkingLevel->save()) {
            Yii::error($ServiceParkingLevel->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceParkingLevel->getErrors()
            ];
        } else {
            return ServiceParkingLevelResponse::findOne(['id' => $ServiceParkingLevel->id]);
        }
    }

    public function update()
    {
        $ServiceParkingLevel = ServiceParkingLevelResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceParkingLevel) {
            $ServiceParkingLevel->load(CUtils::arrLoad($this->attributes), '');
            if (!$ServiceParkingLevel->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceParkingLevel->getErrors()
                ];
            } else {
                return $ServiceParkingLevel;
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

        //check map thi ko được xóa
        $serviceManagementVehicle = ServiceManagementVehicle::findOne(['service_parking_level_id' => $this->id]);
        $serviceParkingFee = ServiceParkingFee::findOne(['service_parking_level_id' => $this->id]);
        if(!empty($serviceManagementVehicle) || !empty($serviceParkingFee)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Service Parking Level Using, Not Delete"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        $ServiceParkingLevel = ServiceParkingLevel::findOne($this->id);
        if($ServiceParkingLevel->delete()){
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
