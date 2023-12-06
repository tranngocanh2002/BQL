<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceMapManagement;
use common\models\Apartment;
use common\models\ServiceElectricFee;
use common\models\ServiceElectricInfo;
use common\models\ServiceElectricLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceElectricFeeForm")
 * )
 */
class ServiceElectricFeeForm extends Model
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
        $ServiceElectricInfo = ServiceElectricInfo::findOne(['apartment_id' => $this->apartment_id, 'service_map_management_id' => $this->service_map_management_id]);
        if(empty($ServiceElectricInfo)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "The apartment has not been configured fee"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if($this->lock_time < $ServiceElectricInfo->tmp_end_date){
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

        $this->start_index = $ServiceElectricInfo->end_index;
        $start_time = $ServiceElectricInfo->end_date;
//        if(empty($this->start_index)){
//            $ServiceElectricInfo = ServiceElectricInfo::findOne(['apartment_id' => $this->apartment_id, 'service_map_management_id' => $this->service_map_management_id]);
//            if(!empty($ServiceElectricInfo)){
//                $this->start_index = $ServiceElectricInfo->end_index;
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
        $ServiceElectricFeeOfMonth = ServiceElectricFee::find()->where(['building_cluster_id' => $buildingCluster->id, 'apartment_id' => $this->apartment_id])
            ->andWhere(['>=','fee_of_month', $fee_of_month_start])
//            ->andWhere(['<=','fee_of_month', $fee_of_month_end])
            ->one();
        if (!empty($ServiceElectricFeeOfMonth)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Phí của tháng không phù hợp"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $ServiceElectricFee = new ServiceElectricFee();
        $ServiceElectricFee->load(CUtils::arrLoad($this->attributes), '');
        $ServiceElectricFee->building_cluster_id = $buildingCluster->id;
        $ServiceElectricFee->building_area_id = $apartment->building_area_id;
        $ServiceElectricFee->start_time = $start_time;
        $ServiceElectricFee->getTotalIndex();
        $ServiceElectricFee->getTotalMoney();
        if($ServiceElectricFee->total_money <= 0){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Tổng tiền phí không phù hợp"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        if (!$ServiceElectricFee->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceElectricFee->getErrors()
            ];
        } else {
            return ServiceElectricFeeResponse::findOne(['id' => $ServiceElectricFee->id]);
        }
    }

    public function update()
    {
        $ServiceElectricFee = ServiceElectricFeeResponse::findOne(['id' => (int)$this->id, 'is_created_fee' => ServiceElectricFee::IS_UNCREATED_FEE]);
        if ($ServiceElectricFee) {
            $ServiceElectricFee->load(CUtils::arrLoad($this->attributes), '');
            $ServiceElectricFee->getTotalIndex();
            $ServiceElectricFee->getTotalMoney();
            if ($ServiceElectricFee->end_index <= $ServiceElectricFee->start_index) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "end index does not match"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if($ServiceElectricFee->total_money <= 0){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Tổng tiền phí không phù hợp"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $fee_of_month_start = strtotime(date('Y-m-01', $ServiceElectricFee->fee_of_month));
            $serviceElectricFeeOfMonth = ServiceElectricFee::find()->where(['building_cluster_id' => $ServiceElectricFee->building_cluster_id, 'apartment_id' => $ServiceElectricFee->apartment_id])
                ->andWhere(['>=','fee_of_month', $fee_of_month_start])
                ->andWhere(['<>','id', $ServiceElectricFee->id])
                ->one();
            if (!empty($serviceElectricFeeOfMonth)) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Phí của tháng không phù hợp"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if (!$ServiceElectricFee->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceElectricFee->getErrors()
                ];
            } else {
                return $ServiceElectricFee;
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
        $ServiceElectricFees = ServiceElectricFee::find()->where(['id' => $ids, 'is_created_fee' => ServiceElectricFee::IS_UNCREATED_FEE])->orderBy(['lock_time' => SORT_DESC])->all();
        foreach ($ServiceElectricFees as $ServiceElectricFee){
            $r = $ServiceElectricFee->resetInfo(true);
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
//        $ServiceElectricFee = ServiceElectricFee::findOne(['id' => $ids, 'is_created_fee' => ServiceElectricFee::IS_UNCREATED_FEE]);
//        if($ServiceElectricFee->delete()){
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
        $ServiceElectricInfo = ServiceElectricInfo::findOne(['apartment_id' => $this->apartment_id, 'service_map_management_id' => $this->service_map_management_id]);
        if(empty($ServiceElectricInfo)){
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
        $dateRes = ServiceElectricFee::getCharge($buildingCluster->id, $this->service_map_management_id, $this->end_index, $this->start_index, $this->lock_time, $this->apartment_id);
        return $dateRes;
    }
}
