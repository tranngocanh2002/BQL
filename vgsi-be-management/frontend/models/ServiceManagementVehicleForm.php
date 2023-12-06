<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServiceParkingFee;
use common\models\ServiceParkingLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceManagementVehicleForm")
 * )
 */
class ServiceManagementVehicleForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="number - Biển số xe", default="", type="string")
     * @var string
     */
    public $number;

    /**
     * @SWG\Property(description="description", default="", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="apartment id", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="service parking level id", default=0, type="integer")
     * @var integer
     */
    public $service_parking_level_id;

    /**
     * @SWG\Property(description="start date : ngày bắt đầu gửi", default=0, type="integer")
     * @var integer
     */
    public $start_date;

    /**
     * @SWG\Property(description="end date : ngày hết hạn", default=0, type="integer")
     * @var integer
     */
    public $end_date;

    /**
     * @SWG\Property(description="cancel date : ngày hủy", default=0, type="integer")
     * @var integer
     */
    public $cancel_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['id', 'start_date'], 'required', "on" => ['active']],
            [['id', 'cancel_date'], 'required', "on" => ['cancel']],
            [['number', 'service_parking_level_id', 'apartment_id'], 'required', "on" => ['create', 'update']],
            [['id', 'service_parking_level_id', 'apartment_id', 'start_date', 'end_date', 'cancel_date'], 'integer'],
            [['number', 'description'], 'string'],
            [['number'], 'validateNumber'],
        ];
    }

    public function validateNumber($attribute, $params, $validator)
    {
        $this->$attribute = \common\helpers\CVietnameseTools::toUpper($this->$attribute);
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $serviceParkingLevel = ServiceParkingLevel::findOne(['id' => $this->service_parking_level_id, 'building_cluster_id' => $buildingCluster->id]);
        if (empty($serviceParkingLevel)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $apartment = Apartment::findOne(['id' => $this->apartment_id, 'building_cluster_id' => $buildingCluster->id]);
        if (empty($apartment)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $ServiceManagementVehicle = ServiceManagementVehicle::findOne(['apartment_id' => $this->apartment_id, 'number' => $this->number, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED]);
        if (!empty($ServiceManagementVehicle)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "ServiceManagementVehicle Exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $ServiceManagementVehicleExist = ServiceManagementVehicle::find()->where(['number' => $this->number, 'building_cluster_id' => $buildingCluster->id, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED])
            ->andWhere(['<>', 'status', ServiceManagementVehicle::STATUS_UNACTIVE])
            ->andWhere(['<>', 'apartment_id', $this->apartment_id])
            ->one();
        if (!empty($ServiceManagementVehicleExist)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Xe đã được kích hoạt trong căn hộ khác"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $current_date = strtotime(date('Y-m-d 00:00:00', time()));
        if (empty($this->start_date)) {
            $this->start_date = $current_date;
        }

        $ServiceManagementVehicle = new ServiceManagementVehicle();
        $ServiceManagementVehicle->load(CUtils::arrLoad($this->attributes), '');
        $ServiceManagementVehicle->building_cluster_id = $buildingCluster->id;
        $ServiceManagementVehicle->building_area_id = $apartment->building_area_id;
        if (empty($ServiceManagementVehicle->end_date)) {
            $ServiceManagementVehicle->end_date = $ServiceManagementVehicle->start_date;
        }
        $ServiceManagementVehicle->tmp_end_date = $ServiceManagementVehicle->end_date;
        if (!$ServiceManagementVehicle->save()) {
            Yii::error($ServiceManagementVehicle->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceManagementVehicle->getErrors()
            ];
        } else {
            return ServiceManagementVehicleResponse::findOne(['id' => $ServiceManagementVehicle->id]);
        }
    }

    public function update()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $ServiceManagementVehicle = ServiceManagementVehicleResponse::findOne(['id' => (int)$this->id, 'building_cluster_id' => $buildingCluster->id]);
        if ($ServiceManagementVehicle) {
            $check = ServiceManagementVehicle::find()
                ->where(['apartment_id' => $this->apartment_id, 'number' => $this->number, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED])
                ->andWhere(['<>', 'id', $this->id])->one();
            if (!empty($check)) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "ServiceManagementVehicle Exist"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $ServiceManagementVehicleExist = ServiceManagementVehicle::find()->where(['number' => $this->number, 'building_cluster_id' => $buildingCluster->id, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED])
                ->andWhere(['<>', 'status', ServiceManagementVehicle::STATUS_UNACTIVE])
                ->andWhere(['<>', 'apartment_id', $this->apartment_id])
                ->one();
            if (!empty($ServiceManagementVehicleExist)) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Xe đã được kích hoạt trong căn hộ khác"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $start_date_old = $ServiceManagementVehicle->start_date;
            $ServiceManagementVehicle->load(CUtils::arrLoad($this->attributes), '');
            $start_date_new = $ServiceManagementVehicle->start_date;
            if ($start_date_old !== $start_date_new) {
                //check map thi ko được update end_date
                $serviceParkingFee = ServiceParkingFee::findOne(['service_management_vehicle_id' => $this->id]);
                if (!empty($serviceParkingFee)) {
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Service Management Vehicle Using, Not Update End Date"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
                $ServiceManagementVehicle->end_date = $ServiceManagementVehicle->start_date;
                $ServiceManagementVehicle->tmp_end_date = $ServiceManagementVehicle->end_date;
            }
            if (!$ServiceManagementVehicle->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceManagementVehicle->getErrors()
                ];
            } else {
                return $ServiceManagementVehicle;
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
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        //check map thi ko được xóa
        $serviceParkingFee = ServiceParkingFee::findOne(['service_management_vehicle_id' => $this->id]);
        if (!empty($serviceParkingFee)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Service Management Vehicle Using, Not Delete"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $ServiceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id]);
        if (!empty($ServiceManagementVehicle) && $ServiceManagementVehicle->delete()) {
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success")
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function active()
    {
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $ServiceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $this->id, 'status' => ServiceManagementVehicle::STATUS_UNACTIVE, 'building_cluster_id' => $buildingCluster->id]);
            if ($ServiceManagementVehicle) {
                $ServiceManagementVehicleExist = ServiceManagementVehicle::find()->where(['number' => $ServiceManagementVehicle->number, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED])
                    ->andWhere(['<>', 'status', ServiceManagementVehicle::STATUS_UNACTIVE])
                    ->andWhere(['<>', 'apartment_id', $ServiceManagementVehicle->apartment_id])
                    ->one();
                if (!empty($ServiceManagementVehicleExist)) {
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Xe đã được kích hoạt trong căn hộ khác"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }

                $ServiceParkingFees = ServiceParkingFee::find()->where(['service_management_vehicle_id' => $ServiceManagementVehicle->id, 'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE])->orderBy(['end_time' => SORT_DESC])->all();
                foreach ($ServiceParkingFees as $ServiceParkingFee) {
                    $r = $ServiceParkingFee->resetInfo();
                    if (!$r) {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Không hủy được phí gửi xe")
                        ];
                    }
                }

                $serviceParkingFee = ServiceParkingFee::find()->where(['service_management_vehicle_id' => $ServiceManagementVehicle->id])
                    ->andWhere(['>', 'end_time', strtotime(date('Y-m-d 23:59:59', $this->start_date))])
                    ->one();
                if (!empty($serviceParkingFee)) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Tồn tại phí sau thời gian gửi mới")
                    ];
                }

                $ServiceManagementVehicle->tmp_end_date = $this->start_date;
                $ServiceManagementVehicle->end_date = $ServiceManagementVehicle->tmp_end_date;

                if($ServiceManagementVehicle->end_date < $ServiceManagementVehicle->start_date){
                    $ServiceManagementVehicle->start_date = $ServiceManagementVehicle->end_date;
                }
                if(!isset($this->cancel_date)){
                    $ServiceManagementVehicle->cancel_date = null;
                }else{
                    if($ServiceManagementVehicle->cancel_date <= $ServiceManagementVehicle->tmp_end_date){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Cancel Date Invalid")
                        ];
                    }
                    $ServiceManagementVehicle->cancel_date = $this->cancel_date;
                }
                $ServiceManagementVehicle->status = ServiceManagementVehicle::STATUS_ACTIVE;
                if (!$ServiceManagementVehicle->save()) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Cancel Error")
                    ];
                }

                $transaction->commit();
                return [
                    'success' => true,
                    'message' => Yii::t('frontend', "Active Success")
                ];
            } else {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function cancel()
    {
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $ServiceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id, 'status' => ServiceManagementVehicle::STATUS_ACTIVE]);
            if ($ServiceManagementVehicle) {
                $this->cancel_date = strtotime(date('Y-m-d 00:00:00', $this->cancel_date));
//                if($this->cancel_date < strtotime(date('Y-m-d 00:00:00', time()))){
//                    return [
//                        'success' => false,
//                        'message' => Yii::t('frontend', "Ngày hủy không được nhỏ hơn ngày hiện tại")
//                    ];
//                }
                if($this->cancel_date < strtotime(date('Y-m-d 00:00:00', $ServiceManagementVehicle->end_date))){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Ngày hủy không được nhỏ hơn ngày đã đóng phí")
                    ];
                }
                $serviceParkingFee = ServiceParkingFee::find()->where(['service_management_vehicle_id' => $ServiceManagementVehicle->id, 'is_created_fee' => ServiceParkingFee::IS_CREATED_FEE])
                    ->andWhere(['>', 'end_time', strtotime(date('Y-m-d 23:59:59', $this->cancel_date))])
                    ->one();
                if (!empty($serviceParkingFee)) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Tồn tại phí đã duyệt sau thời gian hủy")
                    ];
                }

                $ServiceParkingFees = ServiceParkingFee::find()->where(['service_management_vehicle_id' => $ServiceManagementVehicle->id, 'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE])->orderBy(['end_time' => SORT_DESC])->all();
                foreach ($ServiceParkingFees as $ServiceParkingFee) {
                    $r = $ServiceParkingFee->resetInfo();
                    if (!$r) {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Không hủy được phí gửi xe")
                        ];
                    }
                }

                $ServiceManagementVehicle->cancel_date = $this->cancel_date;
                if(strtotime(date('Y-m-d 00:00:00', $ServiceManagementVehicle->cancel_date)) <= strtotime(date('Y-m-d 00:00:00', time()))){
                    $ServiceManagementVehicle->status = ServiceManagementVehicle::STATUS_UNACTIVE;
                }
                if (!$ServiceManagementVehicle->save()) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Cancel Error")
                    ];
                }
                //Tạo phí cuối trước hủy
                if(strtotime(date('Y-m-d 00:00:00', $ServiceManagementVehicle->cancel_date)) > strtotime(date('Y-m-d 00:00:00', $ServiceManagementVehicle->end_date))){
                    //check tao phi
                    $current_time = strtotime(date('Y-m-01 00:00:00', time()));
                    $cancel_date_check = strtotime(date('Y-m-01 00:00:00', $ServiceManagementVehicle->cancel_date));
                    if($cancel_date_check > $current_time){
                        $cancel_date_check = $current_time;
                    }
                    $end_date_next = strtotime(date('Y-m-01 00:00:00', strtotime('+1 day', $ServiceManagementVehicle->end_date)));
                    $i = true;
                    while ($i){
                        if($end_date_next <= $cancel_date_check){
                            $ServiceParkingFee = new ServiceParkingFee();
                            $ServiceParkingFee->building_cluster_id = $buildingCluster->id;
                            $ServiceParkingFee->building_area_id = $ServiceManagementVehicle->building_area_id;
                            $ServiceParkingFee->service_management_vehicle_id = $ServiceManagementVehicle->id;
                            $ServiceParkingFee->service_parking_level_id = $ServiceManagementVehicle->service_parking_level_id;
                            $ServiceParkingFee->service_map_management_id = $ServiceManagementVehicle->serviceParkingLevel->service_map_management_id;
                            $ServiceParkingFee->apartment_id = $ServiceManagementVehicle->apartment_id;
                            $ServiceParkingFee->count_month = 1;
                            $ServiceParkingFee->setParams();
                            if ($ServiceParkingFee->total_money > 0) {
                                if (!$ServiceParkingFee->save()) {
                                    Yii::error($ServiceParkingFee->errors);
                                    $transaction->rollBack();
                                    return [
                                        'success' => false,
                                        'message' => Yii::t('frontend', "Tạo phí không thành công")
                                    ];
                                }
                                //update thời gian sử dụng vào quản lý fee
                                $ServiceParkingFee->updateEndTime();
                            }
                            $end_date_next = strtotime('+1 month', strtotime(date('Y-m-01', $end_date_next)));
                        }else{
                            $i = false;
                        }
                    }
                }
                $transaction->commit();
                if(($ServiceManagementVehicle->status != ServiceManagementVehicle::STATUS_UNACTIVE) && !empty($ServiceManagementVehicle->cancel_date)){
                    return [
                        'success' => true,
                        'message' => Yii::t('frontend', "Xe sẽ được hủy vào ngày: " . date('d/m/Y', $ServiceManagementVehicle->cancel_date))
                    ];
                }
                return [
                    'success' => true,
                    'message' => Yii::t('frontend', "Cancel Success")
                ];
            } else {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
