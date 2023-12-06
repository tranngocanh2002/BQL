<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ServiceBuildingConfig;
use common\models\ServiceBuildingFee;
use common\models\ServiceBuildingInfo;
use common\models\ServiceParkingLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBuildingInfoForm")
 * )
 */
class ServiceBuildingInfoForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="apartment id", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="start date : ngày bắt", default=0, type="integer")
     * @var integer
     */
    public $start_date;

    /**
     * @SWG\Property(description="end_date : ngày kết hết hạn", default=0, type="integer")
     * @var integer
     */
    public $end_date;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['apartment_id'], 'required'],
            [['id', 'apartment_id', 'start_date', 'end_date'], 'integer'],
        ];
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $apartment = Apartment::findOne(['id' => $this->apartment_id, 'building_cluster_id' => $buildingCluster->id]);
        if(empty($apartment)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if(empty($this->start_date)){
            $this->start_date = time();
        }
        $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $apartment->building_cluster_id]);
        if(empty($serviceBuildingConfig)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServiceBuildingInfo = ServiceBuildingInfo::findOne(['apartment_id' => $this->apartment_id]);
        if(!empty($ServiceBuildingInfo)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "ServiceBuildingInfo Exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServiceBuildingInfo = new ServiceBuildingInfo();
        $ServiceBuildingInfo->load(CUtils::arrLoad($this->attributes), '');
        $ServiceBuildingInfo->building_cluster_id = $buildingCluster->id;
        $ServiceBuildingInfo->building_area_id = $apartment->building_area_id;
        $ServiceBuildingInfo->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
        if(empty($ServiceBuildingInfo->end_date)){
            $ServiceBuildingInfo->end_date = $ServiceBuildingInfo->start_date;
        }

        if($ServiceBuildingInfo->start_date > $ServiceBuildingInfo->end_date){
            $ServiceBuildingInfo->start_date = $ServiceBuildingInfo->end_date;
        }

        $ServiceBuildingInfo->tmp_end_date = $ServiceBuildingInfo->end_date;
        if (!$ServiceBuildingInfo->save()) {
            Yii::error($ServiceBuildingInfo->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceBuildingInfo->getErrors()
            ];
        } else {
            return ServiceBuildingInfoResponse::findOne(['id' => $ServiceBuildingInfo->id]);
        }
    }

    public function update()
    {
        $ServiceBuildingInfo = ServiceBuildingInfoResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceBuildingInfo) {

            $check = ServiceBuildingInfo::find()
                ->where(['apartment_id' => $this->apartment_id])
                ->andWhere(['<>', 'id', $this->id])->one();
            if(!empty($check)){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "ServiceBuildingInfo Exist"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            $end_date_old = $ServiceBuildingInfo->end_date;
            $ServiceBuildingInfo->load(CUtils::arrLoad($this->attributes), '');
            $end_date_new = $ServiceBuildingInfo->end_date;
            if($end_date_old !== $end_date_new){
                //check map thi ko được update end_date
                $serviceBuildingFee = ServiceBuildingFee::findOne(['apartment_id' => $ServiceBuildingInfo->apartment_id]);
                if(!empty($serviceBuildingFee)){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Service Building Info Using, Not Update End Date"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
                $ServiceBuildingInfo->tmp_end_date = $ServiceBuildingInfo->end_date;
            }

            if($ServiceBuildingInfo->start_date > $ServiceBuildingInfo->end_date){
                $ServiceBuildingInfo->start_date = $ServiceBuildingInfo->end_date;
            }

            if (!$ServiceBuildingInfo->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceBuildingInfo->getErrors()
                ];
            } else {
                return $ServiceBuildingInfo;
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
        $ServiceBuildingInfo = ServiceBuildingInfo::findOne($this->id);

        //check map thi ko được xóa
        $serviceBuildingFee = ServiceBuildingFee::findOne(['apartment_id' => $ServiceBuildingInfo->apartment_id]);
        if(!empty($serviceBuildingFee)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Service Building Info Using, Not Delete"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        if($ServiceBuildingInfo->delete()){
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
