<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use common\models\ServiceElectricConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceElectricConfigForm")
 * )
 */
class ServiceElectricConfigForm extends Model
{
    /**
     * @SWG\Property(description="type - 0 : thu phí theo căn hộ, 1 - thu phí theo đầu người / căn hộ", default=0, type="integer")
     * @var integer
     */
    public $type;


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
            [['type', 'service_map_management_id'], 'required'],
            [['is_vat', 'type', 'service_map_management_id'], 'integer'],
            [['percent', 'vat_percent', 'environ_percent'], 'number'],
        ];
    }

    public function update()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $ServiceElectricConfig = ServiceElectricConfigResponse::findOne(['building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => $this->service_map_management_id]);
        if (empty($ServiceElectricConfig)) {
            $ServiceElectricConfig = new ServiceElectricConfig();
            $ServiceElectricConfig->building_cluster_id = $buildingCluster->id;
        }
        $ServiceElectricConfig->load(CUtils::arrLoad($this->attributes), '');
//        if($ServiceElectricConfig->is_vat == ServiceElectricConfig::IS_VAT){
//            $ServiceElectricConfig->vat_percent = 10;
//        }else{
//            $ServiceElectricConfig->vat_percent = 0;
//        }
        if (!$ServiceElectricConfig->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceElectricConfig->getErrors()
            ];
        } else {
            return $ServiceElectricConfig;
        }
    }
}
