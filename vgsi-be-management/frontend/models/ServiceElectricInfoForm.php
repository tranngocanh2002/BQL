<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ServiceElectricFee;
use common\models\ServiceElectricInfo;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceElectricInfoForm")
 * )
 */
class ServiceElectricInfoForm extends Model
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
     * @SWG\Property(description="service map management id", default=1, type="integer")
     * @var integer
     */
    public $service_map_management_id;

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
     * @SWG\Property(description="end_index : chỉ số chốt cuối", default=0, type="integer")
     * @var integer
     */
    public $end_index;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['apartment_id', 'service_map_management_id'], 'required'],
            [['id', 'apartment_id', 'service_map_management_id', 'start_date', 'end_date', 'end_index'], 'integer'],
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

        $ServiceElectricInfo = ServiceElectricInfo::findOne(['apartment_id' => $this->apartment_id]);
        if(!empty($ServiceElectricInfo)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "ServiceElectricInfo Exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        if(empty($this->start_date)){
            $this->start_date = time();
        }

        $ServiceElectricInfo = new ServiceElectricInfo();
        $ServiceElectricInfo->load(CUtils::arrLoad($this->attributes), '');
        $ServiceElectricInfo->building_cluster_id = $buildingCluster->id;
        $ServiceElectricInfo->building_area_id = $apartment->building_area_id;
        if(empty($ServiceElectricInfo->end_date)){
            $ServiceElectricInfo->end_date = $ServiceElectricInfo->start_date;
        }
        if($ServiceElectricInfo->start_date > $ServiceElectricInfo->end_date){
            $ServiceElectricInfo->start_date = $ServiceElectricInfo->end_date;
        }

        $ServiceElectricInfo->tmp_end_date = $ServiceElectricInfo->end_date;
        if (!$ServiceElectricInfo->save()) {
            Yii::error($ServiceElectricInfo->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceElectricInfo->getErrors()
            ];
        } else {
            return ServiceElectricInfoResponse::findOne(['id' => $ServiceElectricInfo->id]);
        }
    }

    public function update()
    {
        $ServiceElectricInfo = ServiceElectricInfoResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceElectricInfo) {
            $check = ServiceElectricInfo::find()
                ->where(['apartment_id' => $this->apartment_id])
                ->andWhere(['<>', 'id', $this->id])->one();
            if(!empty($check)){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "ServiceElectricInfo Exist"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            $end_date_old = $ServiceElectricInfo->end_date;
            $end_index_old = $ServiceElectricInfo->end_index;
            $ServiceElectricInfo->load(CUtils::arrLoad($this->attributes), '');
            $end_date_new = $ServiceElectricInfo->end_date;
            $end_index_new = $ServiceElectricInfo->end_index;
//            if($end_date_old !== $end_date_new || $end_index_old !== $end_index_new){
            if($end_date_old !== $end_date_new){
                //check map thi ko được update end_date
                $ServiceElectricFee = ServiceElectricFee::findOne(['apartment_id' => $ServiceElectricInfo->apartment_id]);
                if(!empty($ServiceElectricFee)){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Service Water Info Using, Not Update End Date"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
                $ServiceElectricInfo->tmp_end_date = $ServiceElectricInfo->end_date;
            }

            if($end_index_old !== $end_index_new){
                $ServiceElectricFees = ServiceElectricFee::find()->where(['is_created_fee' => ServiceElectricFee::IS_UNCREATED_FEE])->orderBy(['lock_time' => SORT_DESC])->all();
                foreach ($ServiceElectricFees as $ServiceElectricFee){
                    $r = $ServiceElectricFee->resetInfo();
                    if(!$r){
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Delete Error")
                        ];
                    }
                }
            }

            if($ServiceElectricInfo->start_date > $ServiceElectricInfo->end_date){
                $ServiceElectricInfo->start_date = $ServiceElectricInfo->end_date;
            }
            if (!$ServiceElectricInfo->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceElectricInfo->getErrors()
                ];
            } else {
                return $ServiceElectricInfo;
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
        $ServiceElectricInfo = ServiceElectricInfo::findOne($this->id);

        //check map thi ko được xóa
        $ServiceElectricFee = ServiceElectricFee::findOne(['apartment_id' => $ServiceElectricInfo->apartment_id]);
        if(!empty($ServiceElectricFee)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Service Water Info Using, Not Delete"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        if(!empty($ServiceElectricInfo) && $ServiceElectricInfo->delete()){
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
