<?php

namespace frontend\models;

use Codeception\Module\SOAP;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceBuildingConfig;
use common\models\ServiceBuildingInfo;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServiceBuildingFee;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBuildingFeeForm")
 * )
 */
class ServiceBuildingFeeForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(property="ids", description="ids - mảng các phần từ cần xóa", type="array",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $ids;

    /**
     * @SWG\Property(description="apartment id", default="", type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="service map management id", default="", type="integer")
     * @var integer
     */
    public $service_map_management_id;

    /**
     * @SWG\Property(description="service building config id", default="", type="integer")
     * @var integer
     */
    public $service_building_config_id;

    /**
     * @SWG\Property(description="description", default="", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="description en", default="", type="string")
     * @var string
     */
    public $description_en;

    /**
     * @SWG\Property(description="status", default=0, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="count month", default=0, type="integer")
     * @var integer
     */
    public $count_month;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update']],
            [['ids'], 'safe', "on" => ['delete']],
            [['service_map_management_id', 'apartment_id', 'count_month'], 'required', "on" => ['create']],
            [['id', 'status', 'service_map_management_id', 'apartment_id', 'service_building_config_id', 'count_month'], 'integer'],
            [['description', 'description_en'], 'string'],
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
        if($serviceMapManagement->status == ServiceMapManagement::STATUS_INACTIVE){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Dịch vụ đang ngừng cung cấp")
            ];
        }
        $apartment = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'building_cluster_id' => $buildingCluster->id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if (empty($apartment)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $buildingCluster->id]);

        $ServiceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $buildingCluster->id, 'apartment_id' => $apartment->apartment_id, 'service_map_management_id' => $serviceMapManagement->id]);
        if(empty($ServiceBuildingInfo)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "The apartment has not been configured fee"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $is_error = 0;
        $res_ids = [];
        for($i = 0; $i < $this->count_month; $i++){
            $ServiceBuildingFee = new ServiceBuildingFee();
            $ServiceBuildingFee->load(CUtils::arrLoad($this->attributes), '');
            $ServiceBuildingFee->service_building_config_id = $serviceBuildingConfig->id;
            $ServiceBuildingFee->building_cluster_id = $buildingCluster->id;
            $ServiceBuildingFee->building_area_id = $apartment->building_area_id;
            $ServiceBuildingFee->count_month = 1;
            $ServiceBuildingFee->setParams();
            if($ServiceBuildingFee->total_money <= 0){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Tổng tiền phí không phù hơp"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if (!$ServiceBuildingFee->save()) {
                Yii::error($ServiceBuildingFee->errors);
                $is_error = 1;
            }
            //update thời gian sử dụng vào quản lý fee
            $ServiceBuildingFee->updateEndTime();
            $res_ids[] = $ServiceBuildingFee->id;
        }
        if($is_error == 1){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceBuildingFee->getErrors()
            ];
        }
        $r = ServiceBuildingFeeResponse::findOne(['id' => $ServiceBuildingFee->id]);
        if(!empty($r)){
            $r = $r->toArray();
            $r['res_ids'] = $res_ids;
        }
        return $r;
    }

    public function update()
    {
        $ServiceBuildingFee = ServiceBuildingFeeResponse::findOne(['id' => (int)$this->id, 'is_created_fee' => ServiceBuildingFee::IS_UNCREATED_FEE]);
        if ($ServiceBuildingFee) {
            $ServiceBuildingFee->load(CUtils::arrLoad($this->attributes), '');
//            $ServiceBuildingFee->setParams(true);
            if($ServiceBuildingFee->total_money <= 0){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Tổng tiền phí không phù hơp"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
//            return $ServiceBuildingFee;
            if (!$ServiceBuildingFee->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceBuildingFee->getErrors()
                ];
            }
            //update thời gian sử dụng vào quản lý fee
//            $ServiceBuildingFee->updateEndTime();

            return $ServiceBuildingFee;
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
        if (!$this->id && !$this->ids) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $ids = $this->ids;
        if (empty($ids)) {
            $ids = [$this->id];
        }
        $serviceBuildingFees = ServiceBuildingFee::find()->where(['id' => $ids, 'is_created_fee' => ServiceBuildingFee::IS_UNCREATED_FEE])->orderBy(['end_time' => SORT_DESC])->all();
        foreach ($serviceBuildingFees as $serviceBuildingFee){
            //check phi thang sau
            $ServiceBuildingFeeCheck = ServiceBuildingFee::find()->where(['>', 'end_time', $serviceBuildingFee->end_time])
                ->andWhere(['<>', 'id', $serviceBuildingFee->id])
                ->andWhere(['apartment_id' => $serviceBuildingFee->apartment_id])
                ->orderBy(['end_time' => SORT_ASC])->one();
            if(!empty($ServiceBuildingFeeCheck)){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Có phí tháng sau chưa được xóa")
                ];
            }

            $r = $serviceBuildingFee->resetInfo();
            if(!$r){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Delete Error")
                ];
            }
        }
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Delete Success")
        ];
    }

    public function getCharge()
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

        $apartment = Apartment::find()->where(['building_cluster_id' => $buildingCluster->id, 'id' => $this->apartment_id, 'is_deleted' => Apartment::NOT_DELETED])->one();

        $ServiceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $buildingCluster->id, 'apartment_id' => $this->apartment_id, 'service_map_management_id' => $serviceMapManagement->id]);
        if(empty($ServiceBuildingInfo)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "The apartment has not been configured fee"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $serviceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $buildingCluster->id, 'apartment_id' => $this->apartment_id]);
        if (!empty($serviceBuildingInfo)) {
            if(!empty($this->id)){
                $start_time = $serviceBuildingInfo->end_date;
            }else{
                $start_time = $serviceBuildingInfo->tmp_end_date;
            }
        } else {
            $start_time = $apartment->date_received;
        }
        $start_time = strtotime('+1 day', $start_time);
        $dateRes = ServiceBuildingFee::getCharge($start_time, $buildingCluster->id, $this->count_month, $apartment->capacity);
        return $dateRes;
    }
}
