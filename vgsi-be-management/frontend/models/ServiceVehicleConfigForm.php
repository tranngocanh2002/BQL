<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use common\models\ServiceVehicleConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceVehicleConfigForm")
 * )
 */
class ServiceVehicleConfigForm extends Model
{
    /**
     * @SWG\Property(description="auto_create_fee - 0 : không tự động tạo phí, 1 - Tạo phí tự động", default=1, type="integer")
     * @var integer
     */
    public $auto_create_fee;


    /**
     * @SWG\Property(description="percent", default=1, type="double")
     * @var double
     */
    public $percent;

    /**
     * @SWG\Property(description="vat_percent: % vat thuế bql", default=0, type="double")
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
            [['auto_create_fee', 'service_map_management_id'], 'required'],
            [['is_vat', 'auto_create_fee', 'service_map_management_id'], 'integer'],
            [['percent', 'vat_percent', 'environ_percent'], 'number'],
        ];
    }

    public function update()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $ServiceVehicleConfig = ServiceVehicleConfigResponse::findOne(['building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => $this->service_map_management_id]);
        if (empty($ServiceVehicleConfig)) {
            $ServiceVehicleConfig = new ServiceVehicleConfig();
            $ServiceVehicleConfig->building_cluster_id = $buildingCluster->id;
        }
        $ServiceVehicleConfig->load(CUtils::arrLoad($this->attributes), '');
//        if($ServiceVehicleConfig->is_vat == ServiceVehicleConfig::IS_VAT){
//            $ServiceVehicleConfig->vat_percent = 10;
//        }else{
//            $ServiceVehicleConfig->vat_percent = 0;
//        }
        if (!$ServiceVehicleConfig->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceVehicleConfig->getErrors()
            ];
        } else {
            return $ServiceVehicleConfig;
        }
    }
}
