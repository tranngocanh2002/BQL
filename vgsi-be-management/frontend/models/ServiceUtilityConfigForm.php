<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityFree;
use common\models\ServiceWaterFee;
use common\models\ServiceUtilityConfig;
use common\models\ServiceParkingLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityConfigForm")
 * )
 */
class ServiceUtilityConfigForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="name", default="", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="address", default="", type="string")
     * @var string
     */
    public $address;

    /**
     * @SWG\Property(description="name en", default="", type="string")
     * @var string
     */
    public $name_en;

    /**
     * @SWG\Property(description="address en", default="", type="string")
     * @var string
     */
    public $address_en;

    /**
     * @SWG\Property(description="service utility free id", default=1, type="integer")
     * @var integer
     */
    public $service_utility_free_id;

    /**
     * @SWG\Property(description="type: 0 - không thu phí, 1 - có thu phí", default=1, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="total_slot: Số lượng chỗ trống có", default=0, type="integer")
     * @var integer
     */
    public $total_slot;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['service_utility_free_id', 'type'], 'required'],
            [['id', 'service_utility_free_id', 'type', 'total_slot'], 'integer'],
            [['name', 'address', 'name_en', 'address_en'], 'string'],
        ];
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $ServiceUtilityFree = ServiceUtilityFree::findOne(['building_cluster_id' => $buildingCluster->id, 'id' => $this->service_utility_free_id]);
        if(empty($ServiceUtilityFree)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "ServiceUtilityFree Not Exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServiceUtilityConfig = ServiceUtilityConfig::findOne(['name' => $this->name, 'service_utility_free_id' => $this->service_utility_free_id]);
        if(!empty($ServiceUtilityConfig)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Tên chỗ đã tồn tại"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $ServiceUtilityConfig = new ServiceUtilityConfig();
        $ServiceUtilityConfig->load(CUtils::arrLoad($this->attributes), '');
        $ServiceUtilityConfig->building_cluster_id = $buildingCluster->id;
        $ServiceUtilityConfig->service_utility_free_id = $ServiceUtilityFree->id;
        $ServiceUtilityConfig->booking_type = $ServiceUtilityFree->booking_type;
        if (!$ServiceUtilityConfig->save()) {
            Yii::error($ServiceUtilityConfig->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceUtilityConfig->getErrors()
            ];
        } else {
            return ServiceUtilityConfigResponse::findOne(['id' => $ServiceUtilityConfig->id]);
        }
    }

    public function update()
    {
        $ServiceUtilityConfig = ServiceUtilityConfigResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceUtilityConfig) {
            $ServiceUtilityConfig->load(CUtils::arrLoad($this->attributes), '');
            if (!$ServiceUtilityConfig->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceUtilityConfig->getErrors()
                ];
            } else {
                return $ServiceUtilityConfig;
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
        //check booking, nếu có book sẽ không cho xóa
        $serviceUtilityBooking = ServiceUtilityBooking::findOne(['service_utility_config_id' => $this->id]);
        if(!empty($serviceUtilityBooking)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Service Utility Booking Exist"),
            ];
        }
        $ServiceUtilityConfig = ServiceUtilityConfig::findOne($this->id);
        if($ServiceUtilityConfig->delete()){
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
