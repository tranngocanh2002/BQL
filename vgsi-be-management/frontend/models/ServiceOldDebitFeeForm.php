<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceMapManagement;
use common\models\Apartment;
use common\models\ServiceOldDebitFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceOldDebitFeeForm")
 * )
 */
class ServiceOldDebitFeeForm extends Model
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
     * @SWG\Property(description="total money", default=0, type="integer")
     * @var integer
     */
    public $total_money;

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
            [['service_map_management_id', 'apartment_id'], 'required'],
            [['id', 'service_map_management_id', 'apartment_id', 'fee_of_month', 'total_money'], 'integer'],
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

        if(empty($this->fee_of_month) || strtotime(date('Y-m-d 00:00:00', $this->fee_of_month)) > time()){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Phí của tháng không hợp lý"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        if ($this->total_money === 0) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid total money"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $ServiceOldDebitFee = new ServiceOldDebitFee();
        $ServiceOldDebitFee->load(CUtils::arrLoad($this->attributes), '');
        $ServiceOldDebitFee->building_cluster_id = $buildingCluster->id;
        $ServiceOldDebitFee->building_area_id = $apartment->building_area_id;
        $ServiceOldDebitFee->total_money = round($ServiceOldDebitFee->total_money);
        if (!$ServiceOldDebitFee->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceOldDebitFee->getErrors()
            ];
        } else {
            return ServiceOldDebitFeeResponse::findOne(['id' => $ServiceOldDebitFee->id]);
        }
    }

    public function update()
    {
        $ServiceOldDebitFee = ServiceOldDebitFeeResponse::findOne(['id' => (int)$this->id, 'is_created_fee' => ServiceOldDebitFee::IS_UNCREATED_FEE]);
        if ($ServiceOldDebitFee) {
            $ServiceOldDebitFee->load(CUtils::arrLoad($this->attributes), '');
            if(empty($this->fee_of_month) || strtotime(date('Y-m-d 00:00:00', $this->fee_of_month)) > time()){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Phí của tháng không hợp lý"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if ($this->total_money === 0) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid total money"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $ServiceOldDebitFee->total_money = round($ServiceOldDebitFee->total_money);

            if (!$ServiceOldDebitFee->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceOldDebitFee->getErrors()
                ];
            } else {
                return $ServiceOldDebitFee;
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
        $ServiceOldDebitFees = ServiceOldDebitFee::find()->where(['id' => $ids, 'is_created_fee' => ServiceOldDebitFee::IS_UNCREATED_FEE])->all();
        foreach ($ServiceOldDebitFees as $ServiceOldDebitFee){
            $r = $ServiceOldDebitFee->resetInfo();
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
}
