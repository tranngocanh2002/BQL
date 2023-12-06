<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ServiceWaterFee;
use common\models\ServiceWaterInfo;
use common\models\ServiceParkingLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceWaterInfoForm")
 * )
 */
class ServiceWaterInfoForm extends Model
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

        $ServiceWaterInfo = ServiceWaterInfo::findOne(['apartment_id' => $this->apartment_id]);
        if(!empty($ServiceWaterInfo)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "ServiceWaterInfo Exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        if(empty($this->start_date)){
            $this->start_date = time();
        }

        $ServiceWaterInfo = new ServiceWaterInfo();
        $ServiceWaterInfo->load(CUtils::arrLoad($this->attributes), '');
        $ServiceWaterInfo->building_cluster_id = $buildingCluster->id;
        $ServiceWaterInfo->building_area_id = $apartment->building_area_id;
        if(empty($ServiceWaterInfo->end_date)){
            $ServiceWaterInfo->end_date = $ServiceWaterInfo->start_date;
        }

        if($ServiceWaterInfo->start_date > $ServiceWaterInfo->end_date){
            $ServiceWaterInfo->start_date = $ServiceWaterInfo->end_date;
        }

        $ServiceWaterInfo->tmp_end_date = $ServiceWaterInfo->end_date;
        if (!$ServiceWaterInfo->save()) {
            Yii::error($ServiceWaterInfo->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceWaterInfo->getErrors()
            ];
        } else {
            return ServiceWaterInfoResponse::findOne(['id' => $ServiceWaterInfo->id]);
        }
    }

    public function update()
    {
        $ServiceWaterInfo = ServiceWaterInfoResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceWaterInfo) {
            $check = ServiceWaterInfo::find()
                ->where(['apartment_id' => $this->apartment_id])
                ->andWhere(['<>', 'id', $this->id])->one();
            if(!empty($check)){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "ServiceWaterInfo Exist"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            $end_date_old = $ServiceWaterInfo->end_date;
            $end_index_old = $ServiceWaterInfo->end_index;
            $ServiceWaterInfo->load(CUtils::arrLoad($this->attributes), '');
            $end_date_new = $ServiceWaterInfo->end_date;
            $end_index_new = $ServiceWaterInfo->end_index;
//            if($end_date_old !== $end_date_new || $end_index_old !== $end_index_new){
            if($end_date_old !== $end_date_new){
                //check map thi ko được update end_date
                $serviceWaterFee = ServiceWaterFee::findOne(['apartment_id' => $ServiceWaterInfo->apartment_id]);
                if(!empty($serviceWaterFee)){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Service Water Info Using, Not Update End Date"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
                $ServiceWaterInfo->tmp_end_date = $ServiceWaterInfo->end_date;
            }
            //chỉ số thay đổi -> xóa phí chưa duyệt
            if($end_index_old !== $end_index_new){
                $ServiceWaterFees = ServiceWaterFee::find()->where(['is_created_fee' => ServiceWaterFee::IS_UNCREATED_FEE])->orderBy(['lock_time' => SORT_DESC])->all();
                foreach ($ServiceWaterFees as $ServiceWaterFee){
                    $r = $ServiceWaterFee->resetInfo();
                    if(!$r){
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Delete Error")
                        ];
                    }
                }
            }
            if($ServiceWaterInfo->start_date > $ServiceWaterInfo->end_date){
                $ServiceWaterInfo->start_date = $ServiceWaterInfo->end_date;
            }
            if (!$ServiceWaterInfo->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceWaterInfo->getErrors()
                ];
            } else {
                return $ServiceWaterInfo;
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
        $ServiceWaterInfo = ServiceWaterInfo::findOne($this->id);

        //check map thi ko được xóa
        $serviceWaterFee = ServiceWaterFee::findOne(['apartment_id' => $ServiceWaterInfo->apartment_id]);
        if(!empty($serviceWaterFee)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Service Water Info Using, Not Delete"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        if(!empty($ServiceWaterInfo) && $ServiceWaterInfo->delete()){
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
