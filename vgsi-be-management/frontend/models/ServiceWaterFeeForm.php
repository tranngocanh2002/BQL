<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceMapManagement;
use common\models\Apartment;
use common\models\ServiceWaterFee;
use common\models\ServiceWaterInfo;
use common\models\ServiceWaterLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceWaterFeeForm")
 * )
 */
class ServiceWaterFeeForm extends Model
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
     * @SWG\Property(description="start index", default=1, type="integer")
     * @var integer
     */
    public $start_index;

    /**
     * @SWG\Property(description="end index", default=1, type="integer")
     * @var integer
     */
    public $end_index;

    /**
     * @SWG\Property(description="total index", default=1, type="integer")
     * @var integer
     */
    public $total_index;

    /**
     * @SWG\Property(description="total money", default=0, type="integer")
     * @var integer
     */
    public $total_money;

    /**
     * @SWG\Property(description="lock_time", default=0, type="integer")
     * @var integer
     */
    public $lock_time;

    /**
     * @SWG\Property(description="fee_of_month", default=0, type="integer")
     * @var integer
     */
    public $fee_of_month;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update']],
            [['ids'], 'safe', "on" => ['delete']],
            [['service_map_management_id', 'apartment_id', 'end_index', 'lock_time'], 'required'],
            [['id', 'service_map_management_id', 'apartment_id', 'start_index', 'end_index', 'lock_time', 'fee_of_month'], 'integer'],
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
        $serviceWaterInfo = ServiceWaterInfo::findOne(['apartment_id' => $this->apartment_id, 'service_map_management_id' => $this->service_map_management_id]);
        if(empty($serviceWaterInfo)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "The apartment has not been configured fee"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if($this->lock_time < $serviceWaterInfo->tmp_end_date){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Lock time is incorrect"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        if($this->lock_time > time()){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Lock time is bigger current time"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $this->start_index = $serviceWaterInfo->end_index;
        $start_time = $serviceWaterInfo->end_date;
//        if(empty($this->start_index)){
//            $serviceWaterInfo = ServiceWaterInfo::findOne(['apartment_id' => $this->apartment_id, 'service_map_management_id' => $this->service_map_management_id]);
//            if(!empty($serviceWaterInfo)){
//                $this->start_index = $serviceWaterInfo->end_index;
//            }
//        }

        if ($this->end_index <= $this->start_index) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "end index does not match"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $fee_of_month_start = strtotime(date('Y-m-01', $this->fee_of_month));
//        $fee_of_month_end = strtotime(date('Y-m-t', $this->fee_of_month));
        $serviceWaterFeeOfMonth = ServiceWaterFee::find()->where(['building_cluster_id' => $buildingCluster->id, 'apartment_id' => $this->apartment_id])
            ->andWhere(['>=','fee_of_month', $fee_of_month_start])
//            ->andWhere(['<=','fee_of_month', $fee_of_month_end])
            ->one();
        if (!empty($serviceWaterFeeOfMonth)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Phí của tháng không phù hợp"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $ServiceWaterFee = new ServiceWaterFee();
        $ServiceWaterFee->load(CUtils::arrLoad($this->attributes), '');
        $ServiceWaterFee->building_cluster_id = $buildingCluster->id;
        $ServiceWaterFee->building_area_id = $apartment->building_area_id;
        $ServiceWaterFee->start_time = $start_time;
        $ServiceWaterFee->getTotalIndex();
        $ServiceWaterFee->getTotalMoney();
        if($ServiceWaterFee->total_money <= 0){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if (!$ServiceWaterFee->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Tổng tiền phí không phù hơp"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceWaterFee->getErrors()
            ];
        } else {
            return ServiceWaterFeeResponse::findOne(['id' => $ServiceWaterFee->id]);
        }
    }

    public function update()
    {
        $ServiceWaterFee = ServiceWaterFeeResponse::findOne(['id' => (int)$this->id, 'is_created_fee' => ServiceWaterFee::IS_UNCREATED_FEE]);
        if ($ServiceWaterFee) {
            $ServiceWaterFee->load(CUtils::arrLoad($this->attributes), '');
            $ServiceWaterFee->getTotalIndex();
            $ServiceWaterFee->getTotalMoney();
            if($ServiceWaterFee->total_money <= 0){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if ($ServiceWaterFee->end_index <= $ServiceWaterFee->start_index) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "end index does not match"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if($ServiceWaterFee->total_money <= 0){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Tổng phí không phù hợp"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $fee_of_month_start = strtotime(date('Y-m-01', $ServiceWaterFee->fee_of_month));
            $serviceWaterFeeOfMonth = ServiceWaterFee::find()->where(['building_cluster_id' => $ServiceWaterFee->building_cluster_id, 'apartment_id' => $ServiceWaterFee->apartment_id])
                ->andWhere(['>=','fee_of_month', $fee_of_month_start])
                ->andWhere(['<>','id', $ServiceWaterFee->id])
                ->one();
            if (!empty($serviceWaterFeeOfMonth)) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Phí của tháng không phù hợp"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if (!$ServiceWaterFee->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceWaterFee->getErrors()
                ];
            } else {
                return $ServiceWaterFee;
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
        $ServiceWaterFees = ServiceWaterFee::find()->where(['id' => $ids, 'is_created_fee' => ServiceWaterFee::IS_UNCREATED_FEE])->orderBy(['lock_time' => SORT_DESC])->all();
        foreach ($ServiceWaterFees as $ServiceWaterFee){
            $r = $ServiceWaterFee->resetInfo(true);
            if(!$r){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "The next month's fee has not been deleted")
                ];
            }
        }
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Delete Success")
        ];
//        $ServiceWaterFee = ServiceWaterFee::findOne(['id' => $ids, 'is_created_fee' => ServiceWaterFee::IS_UNCREATED_FEE]);
//        if($ServiceWaterFee->delete()){
//            return [
//                'success' => true,
//                'message' => Yii::t('frontend', "Delete Success")
//            ];
//        }else{
//            return [
//                'success' => false,
//                'message' => Yii::t('frontend', "Invalid data"),
//                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
//            ];
//        }
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

        $apartment = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'building_cluster_id' => $buildingCluster->id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if (empty($apartment)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $serviceWaterInfo = ServiceWaterInfo::findOne(['apartment_id' => $this->apartment_id, 'service_map_management_id' => $this->service_map_management_id]);
        if(empty($serviceWaterInfo)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "The apartment has not been configured fee"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if ($this->end_index < $this->start_index) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "end index does not match"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $dateRes = ServiceWaterFee::getCharge($buildingCluster->id, $this->service_map_management_id, $this->end_index, $this->start_index, $this->lock_time, $this->apartment_id);
        return $dateRes;
    }
}
