<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use common\models\ServiceParkingFee;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceParkingFeeChangeStatusForm")
 * )
 */
class ServiceParkingFeeChangeStatusForm extends Model
{

    const IS_ACTIVE_ALL = 1;
    const IS_UNACTIVE_ALL = 1;
    /**
     * @SWG\Property(description="service map management id")
     * @var integer
     */
    public $service_map_management_id;

    /**
     * @SWG\Property(description="is_active_all = 1 duyệt tất cả, is_active_all = 0 thì sẽ check theo mảng is_active_array")
     * @var integer
     */
    public $is_active_all;

    /**
     * @SWG\Property(description="is_active_array : mảng các id duyệt tất cả", type="array",
     *      @SWG\Items(type="integer", default=0),
     * )
     * @var array
     */
    public $is_active_array;

    /**
     * @SWG\Property(description="is_unactive_all = 1 là đánh dấu hủy duyệt tất cả, is_unactive_all = 0 thì sẽ check theo mảng is_unactive_array")
     * @var integer
     */
    public $is_unactive_all;

    /**
     * @SWG\Property(description="is_unactive_array : mảng các id đánh dấu là hủy duyệt", type="array",
     *      @SWG\Items(type="integer", default=0),
     * )
     * @var array
     */
    public $is_unactive_array;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_map_management_id'], 'required'],
            [['service_map_management_id', 'is_active_all', 'is_unactive_all'], 'integer'],
            [['is_active_array', 'is_unactive_array'], 'safe'],
        ];
    }

    public function changeStatus()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $buildingCluster = Yii::$app->building->BuildingCluster;

            $serviceMapManagement = ServiceMapManagement::findOne(['id' => $this->service_map_management_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
            if(empty($serviceMapManagement)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if ($this->is_active_all == self::IS_ACTIVE_ALL && $this->is_unactive_all == self::IS_UNACTIVE_ALL) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if (
                $this->is_active_all != self::IS_ACTIVE_ALL
                && $this->is_unactive_all != self::IS_UNACTIVE_ALL
                && (empty($this->is_active_array) || !is_array($this->is_active_array))
                && (empty($this->is_unactive_array) || !is_array($this->is_unactive_array))
            ) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if ($this->is_active_all == self::IS_ACTIVE_ALL) {
                //lấy ra danh sách cần tạo payment fee
                $ServiceParkingFees = ServiceParkingFee::find()->where(
                    [
                        'building_cluster_id' => $buildingCluster->id,
                        'service_map_management_id' => $serviceMapManagement->id,
                        'status' => ServiceParkingFee::STATUS_UNACTIVE,
                        'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE
                    ]
                )->orderBy(['end_time' => SORT_ASC])->all();

//                ServiceParkingFee::updateAll(
//                    ['status' => ServiceParkingFee::STATUS_ACTIVE],
//                    [
//                        'building_cluster_id' => $buildingCluster->id,
//                        'service_map_management_id' => $serviceMapManagement->id,
//                        'status' => ServiceParkingFee::STATUS_UNACTIVE,
//                        'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE
//                    ]
//                );

            } else {
                if (!empty($this->is_active_array) && is_array($this->is_active_array)) {
                    //lấy ra danh sách cần tạo payment fee
                    $ServiceParkingFees = ServiceParkingFee::find()->where(
                        [
                            'id' => $this->is_active_array,
                            'building_cluster_id' => $buildingCluster->id,
                            'service_map_management_id' => $serviceMapManagement->id,
                            'status' => ServiceParkingFee::STATUS_UNACTIVE,
                            'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE
                        ]
                    )->orderBy(['end_time' => SORT_ASC])->all();

//                    ServiceParkingFee::updateAll(
//                        ['status' => ServiceParkingFee::STATUS_ACTIVE],
//                        [
//                            'id' => $this->is_active_array,
//                            'building_cluster_id' => $buildingCluster->id,
//                            'service_map_management_id' => $serviceMapManagement->id,
//                            'status' => ServiceParkingFee::STATUS_UNACTIVE,
//                            'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE
//                        ]
//                    );
                }
            }
            $total_change = 0;
            $total_all = 0;
            //tạo payment fee
            if(!empty($ServiceParkingFees)){
                Yii::info('In create payment fee');

               foreach ($ServiceParkingFees as $ServiceParkingFee){
                   $total_all++;
                   //update thời gian xử dụng info
                   $serviceManagementVehicle = ServiceManagementVehicle::findOne(['building_cluster_id' => $ServiceParkingFee->building_cluster_id, 'apartment_id' => $ServiceParkingFee->apartment_id, 'id' => $ServiceParkingFee->service_management_vehicle_id]);

//                   if($serviceManagementVehicle->end_date != $ServiceParkingFee->start_time){
//                       Yii::error("Không trùng time tmp");
//                       Yii::error($ServiceParkingFee->id);
//                       continue;
//                   }

                   if(!empty($serviceManagementVehicle) && ($serviceManagementVehicle->end_date < $ServiceParkingFee->end_time)){
                       $serviceManagementVehicle->end_date = $ServiceParkingFee->end_time;
                       if (!$serviceManagementVehicle->save()) {
                           $transaction->rollBack();
                           Yii::error($serviceManagementVehicle->errors);
                           return [
                               'success' => false,
                               'message' => Yii::t('frontend', "System busy"),
                           ];
                       }
                   }

                   $ServicePaymentFee = new ServicePaymentFee();
                   $ServicePaymentFee->service_map_management_id = $ServiceParkingFee->service_map_management_id;
                   $ServicePaymentFee->building_cluster_id = $ServiceParkingFee->building_cluster_id;
                   $ServicePaymentFee->building_area_id = $ServiceParkingFee->building_area_id;
                   $ServicePaymentFee->apartment_id = $ServiceParkingFee->apartment_id;
                   $ServicePaymentFee->description = $ServiceParkingFee->description;
                   $ServicePaymentFee->description_en = $ServiceParkingFee->description_en;
                   $ServicePaymentFee->json_desc = $ServiceParkingFee->json_desc;
                   $ServicePaymentFee->price = $ServiceParkingFee->total_money;
                   $ServicePaymentFee->is_draft = ServicePaymentFee::IS_NOT_DRAFT;
                   $ServicePaymentFee->approved_by_id = $user->id;
                   $ServicePaymentFee->fee_of_month = $ServiceParkingFee->fee_of_month;
                   $ServicePaymentFee->day_expired = $ServiceParkingFee->end_time;
                   $ServicePaymentFee->type = ServicePaymentFee::TYPE_SERVICE_PARKING_FEE;
                   $ServicePaymentFee->start_time = $ServiceParkingFee->start_time;
                   $ServicePaymentFee->end_time = $ServiceParkingFee->end_time;
                   if (!$ServicePaymentFee->save()) {
                       $transaction->rollBack();
                       Yii::error($ServicePaymentFee->errors);
                       return [
                           'success' => false,
                           'message' => Yii::t('frontend', "System busy"),
                       ];
                   }

                   $ServiceParkingFee->status = ServiceParkingFee::STATUS_ACTIVE;
                   $ServiceParkingFee->is_created_fee = ServiceParkingFee::IS_CREATED_FEE;
                   $ServiceParkingFee->service_payment_fee_id = $ServicePaymentFee->id;
                   if (!$ServiceParkingFee->save()) {
                       $transaction->rollBack();
                       Yii::error($ServiceParkingFee->errors);
                       return [
                           'success' => false,
                           'message' => Yii::t('frontend', "System busy"),
                       ];
                   }

                   if(!$ServicePaymentFee->apartment->updateCurrentDebt()){
                       $transaction->rollBack();
                       return [
                           'success' => false,
                           'message' => Yii::t('frontend', "System busy"),
                           'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                       ];
                   };

                   //gửi thông báo phí tới cư dân
                   $ServicePaymentFee->sendNotifyToResidentUser(ServicePaymentFee::$typeList[ServicePaymentFee::TYPE_SERVICE_PARKING_FEE],ServicePaymentFee::$typeList_en[ServicePaymentFee::TYPE_SERVICE_PARKING_FEE]);

                   $total_change++;
               }
            }

            if ($this->is_unactive_all == self::IS_UNACTIVE_ALL) {
                ServiceParkingFee::updateAll(
                    ['status' => ServiceParkingFee::STATUS_UNACTIVE],
                    [
                        'building_cluster_id' => $buildingCluster->id,
                        'service_map_management_id' => $serviceMapManagement->id,
                        'status' => ServiceParkingFee::STATUS_ACTIVE,
                        'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE
                    ]
                );
            } else {
                if (!empty($this->is_unactive_array) && is_array($this->is_unactive_array)) {
                    ServiceParkingFee::updateAll(
                        ['status' => ServiceParkingFee::STATUS_UNACTIVE],
                        [
                            'id' => $this->is_unactive_array,
                            'building_cluster_id' => $buildingCluster->id,
                            'service_map_management_id' => $serviceMapManagement->id,
                            'status' => ServiceParkingFee::STATUS_ACTIVE,
                            'is_created_fee' => ServiceParkingFee::IS_UNCREATED_FEE
                        ]
                    );
                }
            }
            $transaction->commit();
            Yii::info("===========");
            Yii::info($total_change);
            Yii::info($total_all);
            if($total_change >= 1){
                return [
                    'success' => true,
                    'message' => Yii::t('frontend', "Update success"),
                    'total_change' => $total_change,
                    'total_all' => $total_all,
                ];
            }else{
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Update Error"),
                    'total_change' => $total_change,
                    'total_all' => $total_all,
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
