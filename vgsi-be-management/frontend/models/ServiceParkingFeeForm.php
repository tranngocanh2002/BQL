<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\Apartment;
use common\models\ServiceParkingFee;
use common\models\ServiceWaterLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceParkingFeeForm")
 * )
 */
class ServiceParkingFeeForm extends Model
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
     * @SWG\Property(description="service parking level id", default="", type="integer")
     * @var integer
     */
    public $service_parking_level_id;

    /**
     * @SWG\Property(description="service management vehicle id", default="", type="integer")
     * @var integer
     */
    public $service_management_vehicle_id;

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
            [['service_map_management_id', 'apartment_id', 'service_parking_level_id', 'service_management_vehicle_id', 'count_month'], 'required', "on" => ['create']],
            [['id', 'status', 'service_map_management_id', 'apartment_id', 'service_parking_level_id', 'service_management_vehicle_id', 'count_month'], 'integer'],
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

        $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $this->service_management_vehicle_id, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED]);
        if(empty($serviceManagementVehicle)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Xe không tồn tại hoặc chưa được kích hoạt"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $cancel_date = $serviceManagementVehicle->cancel_date;
        Yii::warning($this->count_month);
        if(!empty($serviceManagementVehicle->cancel_date)){
            //check tao phi
//            $current_time = strtotime(date('Y-m-01 00:00:00', time()));
//            $cancel_date_check = strtotime(date('Y-m-01 00:00:00', $serviceManagementVehicle->cancel_date));
//            if($cancel_date_check > $current_time){
//                $cancel_date_check = $current_time;
//            }
//            $end_date_next = strtotime(date('Y-m-01 00:00:00', strtotime('+1 day', $serviceManagementVehicle->end_date)));
//            $i = true;
//            $diff_month = 1;
//            while ($i){
//                if($end_date_next <= $cancel_date_check){
//                    $end_date_next = strtotime('+1 month', strtotime(date('Y-m-01', $end_date_next)));
//                    $diff_month++;
//                }else{
//                    $i = false;
//                }
//            }
            $start_year = date('Y', $serviceManagementVehicle->tmp_end_date);
            $start_month = date('m', $serviceManagementVehicle->tmp_end_date);
            $cancel_year = date('Y', $serviceManagementVehicle->cancel_date);
            $cancel_month = date('m', $serviceManagementVehicle->cancel_date);
            $diff_month = ($cancel_month - $start_month) + ($cancel_year - $start_year)*12;
            if((strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->cancel_date)) > strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->tmp_end_date))) && (strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->end_date)) == strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->tmp_end_date))) && (strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->tmp_end_date)) !== strtotime(date('Y-m-t 00:00:00', $serviceManagementVehicle->tmp_end_date)))){
                $diff_month++;
            }
            Yii::warning($diff_month);
            if($this->count_month > $diff_month){
                $this->count_month = $diff_month;
            }
            Yii::warning($this->count_month);
        }
        if($this->count_month == 0){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Xe không phát sinh phí trong tháng này, hoặc đã hủy xe"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $is_error = 0;
        $res_ids = [];
        for($i = 0;$i < $this->count_month; $i++){
            $ServiceParkingFee = new ServiceParkingFee();
            $ServiceParkingFee->load(CUtils::arrLoad($this->attributes), '');
            $ServiceParkingFee->building_cluster_id = $buildingCluster->id;
            $ServiceParkingFee->building_area_id = $apartment->building_area_id;
            $ServiceParkingFee->count_month = 1;
            $ServiceParkingFee->setParams();
            if($ServiceParkingFee->total_money <= 0){
                Yii::error($ServiceParkingFee->errors);
                $is_error = 1;
                break;
            }
            if (!$ServiceParkingFee->save()) {
                Yii::error($ServiceParkingFee->errors);
                $is_error = 1;
                break;
            }
            //update thời gian sử dụng vào quản lý fee
            $ServiceParkingFee->updateEndTime();
            $res_ids[] = $ServiceParkingFee->id;
            if(!empty($cancel_date) && (strtotime(date('Y-m-d 00:00:00', $cancel_date)) <= strtotime(date('Y-m-d 00:00:00', $ServiceParkingFee->end_time)))){
                break;
            }
        }

        if($is_error == 1){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceParkingFee->getErrors()
            ];
        }
        if(!empty($cancel_date) && (strtotime(date('Y-m-d 00:00:00', $cancel_date)) == strtotime(date('Y-m-d 00:00:00', time())))){
            $serviceManagementVehicle->status = ServiceManagementVehicle::STATUS_UNACTIVE;
            if($serviceManagementVehicle->save()){
                Yii::error($serviceManagementVehicle->errors);
            }
        }
        $r = ServiceParkingFeeResponse::findOne(['id' => $ServiceParkingFee->id]);
        if(!empty($r)){
            $r = $r->toArray();
            $r['res_ids'] = $res_ids;
        }
        return $r;
    }

    public function update()
    {
        $ServiceParkingFee = ServiceParkingFeeResponse::findOne(['id' => (int)$this->id, 'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE]);
        if ($ServiceParkingFee) {
            return $ServiceParkingFee;
            $ServiceParkingFee->load(CUtils::arrLoad($this->attributes), '');
            //trường hợp sửa chỉ chấp nhận count_month = 1
//            $ServiceParkingFee->count_month = 1;
//            $ServiceParkingFee->setParams(true);
            if (!$ServiceParkingFee->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceParkingFee->getErrors()
                ];
            }
            //update thời gian sử dụng vào quản lý fee
//            $ServiceParkingFee->updateEndTime();

            return $ServiceParkingFee;
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

        $ServiceParkingFees = ServiceParkingFee::find()->where(['id' => $ids, 'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE])->orderBy(['end_time' => SORT_DESC])->all();
        foreach ($ServiceParkingFees as $ServiceParkingFee){
            //check phi thang sau
            $ServiceParkingFeeCheck = ServiceParkingFee::find()->where(['>', 'end_time', $ServiceParkingFee->end_time])
                ->andWhere(['<>', 'id', $ServiceParkingFee->id])
                ->andWhere(['service_management_vehicle_id' => $ServiceParkingFee->service_management_vehicle_id])
                ->orderBy(['end_time' => SORT_ASC])->one();
            if(!empty($ServiceParkingFeeCheck)){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Có phí tháng sau chưa được xóa")
                ];
            }
            $r = $ServiceParkingFee->resetInfo();
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
        $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $this->service_management_vehicle_id]);
        if (empty($serviceManagementVehicle)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        //tìm ra phí mới nhất của xe hiện tại
        //nếu tồn tại id truyền lên thì tính vào trường hợp sửa
        $start_time = $serviceManagementVehicle->start_date;
        if(!empty($this->id)){
            $serviceParkingFee = ServiceParkingFee::find()->where(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id, 'apartment_id' => $serviceManagementVehicle->apartment_id, 'service_management_vehicle_id' => $this->service_management_vehicle_id])->orderBy(['end_time' => SORT_DESC])->one();
            if(!empty($serviceParkingFee)){
                $start_time = $serviceParkingFee->start_time;
            }
        }else{
            Yii::warning('in tem');
//            $serviceParkingFee = ServiceParkingFee::find()->where(['building_cluster_id' => $buildingCluster->id, 'apartment_id' => $serviceManagementVehicle->apartment_id, 'service_management_vehicle_id' => $this->service_management_vehicle_id])->orderBy(['end_time' => SORT_DESC])->one();
//            if(!empty($serviceParkingFee)){
//                $start_time = $serviceParkingFee->end_time;
//            }
            $start_time = $serviceManagementVehicle->tmp_end_date;
        }
        Yii::warning($this->count_month);
        if(!empty($serviceManagementVehicle->cancel_date)){
            //check tao phi
//            $current_time = strtotime(date('Y-m-01 00:00:00', time()));
//            $cancel_date_check = strtotime(date('Y-m-01 00:00:00', $serviceManagementVehicle->cancel_date));
//            if($cancel_date_check > $current_time){
//                $cancel_date_check = $current_time;
//            }
//            $end_date_next = strtotime(date('Y-m-01 00:00:00', strtotime('+1 day', $serviceManagementVehicle->end_date)));
//            $i = true;
//            $diff_month = 1;
//            while ($i){
//                if($end_date_next <= $cancel_date_check){
//                    $end_date_next = strtotime('+1 month', strtotime(date('Y-m-01', $end_date_next)));
//                    $diff_month++;
//                }else{
//                    $i = false;
//                }
//            }
            $start_year = date('Y', $serviceManagementVehicle->tmp_end_date);
            $start_month = date('m', $serviceManagementVehicle->tmp_end_date);
            $cancel_year = date('Y', $serviceManagementVehicle->cancel_date);
            $cancel_month = date('m', $serviceManagementVehicle->cancel_date);
            $diff_month = ($cancel_month - $start_month) + ($cancel_year - $start_year)*12;
            if((strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->cancel_date)) > strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->tmp_end_date))) && (strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->end_date)) == strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->tmp_end_date))) && (strtotime(date('Y-m-d 00:00:00', $serviceManagementVehicle->tmp_end_date)) !== strtotime(date('Y-m-t 00:00:00', $serviceManagementVehicle->tmp_end_date)))){
                $diff_month++;
            }
            Yii::warning($diff_month);
            if($this->count_month > $diff_month){
                $this->count_month = $diff_month;
            }
            Yii::warning($this->count_month);
        }
        if($this->count_month == 0){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Xe không phát sinh phí trong tháng này, hoặc đã hủy xe"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $start_time = strtotime('+1 day', $start_time);
        $dateRes = ServiceParkingFee::getCharge($start_time, $serviceManagementVehicle->service_parking_level_id, $this->count_month, $serviceManagementVehicle->cancel_date);
        return $dateRes;
    }
}
