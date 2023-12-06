<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use common\models\ServiceBuildingConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBuildingConfigForm")
 * )
 */
class ServiceBuildingConfigForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="auto_create_fee", default=1, type="integer", description="0 - không tạo phí tự động, 1 -  tạo phí tự dộng")
     * @var integer
     */
    public $auto_create_fee;

    /**
     * @SWG\Property(description="price", default=1, type="integer")
     * @var integer
     */
    public $price;

    /**
     * @SWG\Property(description="percent", default=1, type="double")
     * @var double
     */
    public $percent;

    /**
     * @SWG\Property(description="vat_percent: % val thuế", default=0, type="double")
     * @var double
     */
    public $vat_percent;

    /**
     * @SWG\Property(description="environ_percent: phí bảo vệ môi trường", default=0, type="double")
     * @var double
     */
    public $environ_percent;

    /**
     * @SWG\Property(description="is_vat: 0 - chưa bao gồm vat, 1 - đã bao gồm vat", default=0, type="integer")
     * @var integer
     */
    public $is_vat;

    /**
     * @SWG\Property(description="unit: 0 - m2, 1 - căn hộ", default=0, type="integer")
     * @var integer
     */
    public $unit;

    /**
     * @SWG\Property(description="day", default=1, type="integer")
     * @var integer
     */
    public $day;

    /**
     * @SWG\Property(description="month_cycle", default=1, type="integer")
     * @var integer
     */
    public $month_cycle;

    /**
     * @SWG\Property(description="offset_day", default=1, type="integer")
     * @var integer
     */
    public $offset_day;

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
            [['price', 'service_map_management_id', 'unit'], 'required'],
            [['percent', 'vat_percent', 'environ_percent'], 'number'],
            [['is_vat', 'auto_create_fee', 'id', 'service_map_management_id', 'price', 'unit', 'day', 'month_cycle', 'offset_day'], 'integer'],
            [['service_map_management_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceMapManagement::className(), 'targetAttribute' => ['service_map_management_id' => 'id']],
        ];
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $serviceMapManagement = ServiceMapManagement::findOne(['id' => $this->service_map_management_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
        if (empty($serviceMapManagement)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServiceBuildingConfig = new ServiceBuildingConfig();
        $ServiceBuildingConfig->load(CUtils::arrLoad($this->attributes), '');
        $ServiceBuildingConfig->building_cluster_id = $buildingCluster->id;
        $ServiceBuildingConfig->building_area_id = $serviceMapManagement->building_area_id;
        $ServiceBuildingConfig->service_id = $serviceMapManagement->service_id;
//        if($ServiceBuildingConfig->is_vat == ServiceBuildingConfig::IS_VAT){
//            $ServiceBuildingConfig->vat_percent = 10;
//        }
        //từ đầu vào day, month cycle tính ra cr_x
        $ServiceBuildingConfig->createCrField();
        //end tính
        if (!$ServiceBuildingConfig->save()) {
            Yii::error($ServiceBuildingConfig->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceBuildingConfig->getErrors()
            ];
        } else {
            return ServiceBuildingConfigResponse::findOne(['id' => $ServiceBuildingConfig->id]);
        }
    }

    public function update()
    {
        $ServiceBuildingConfig = ServiceBuildingConfigResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceBuildingConfig) {
            $ServiceBuildingConfig->load(CUtils::arrLoad($this->attributes), '');
//            if($ServiceBuildingConfig->is_vat == ServiceBuildingConfig::IS_VAT){
//                $ServiceBuildingConfig->vat_percent = 10;
//            }else{
//                $ServiceBuildingConfig->vat_percent = 0;
//            }
            //từ đầu vào day, month cycle tính ra cr_x
            $ServiceBuildingConfig->createCrField();
            //end tính
            if (!$ServiceBuildingConfig->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceBuildingConfig->getErrors()
                ];
            } else {
                return $ServiceBuildingConfig;
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
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $ServiceBuildingConfig = ServiceBuildingConfig::findOne($this->id);
        if ($ServiceBuildingConfig->delete()) {
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success")
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
