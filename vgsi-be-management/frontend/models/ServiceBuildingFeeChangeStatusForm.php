<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceBuildingInfo;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use common\models\ServiceBuildingFee;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBuildingFeeChangeStatusForm")
 * )
 */
class ServiceBuildingFeeChangeStatusForm extends Model
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
                $ServiceBuildingFees = ServiceBuildingFee::find()->where(
                    [
                        'building_cluster_id' => $buildingCluster->id,
                        'service_map_management_id' => $serviceMapManagement->id,
                        'status' => ServiceBuildingFee::STATUS_UNACTIVE,
                        'is_created_fee' => ServiceBuildingFee::IS_UNCREATED_FEE
                    ]
                )->orderBy(['end_time' => SORT_ASC])->all();

//                ServiceBuildingFee::updateAll(
//                    ['status' => ServiceBuildingFee::STATUS_ACTIVE],
//                    [
//                        'building_cluster_id' => $buildingCluster->id,
//                        'service_map_management_id' => $serviceMapManagement->id,
//                        'status' => ServiceBuildingFee::STATUS_UNACTIVE,
//                        'is_created_fee' => ServiceBuildingFee::IS_UNCREATED_FEE
//                    ]
//                );



            } else {
                if (!empty($this->is_active_array) && is_array($this->is_active_array)) {
                    //lấy ra danh sách cần tạo payment fee
                    $ServiceBuildingFees = ServiceBuildingFee::find()->where(
                        [
                            'id' => $this->is_active_array,
                            'building_cluster_id' => $buildingCluster->id,
                            'service_map_management_id' => $serviceMapManagement->id,
                            'status' => ServiceBuildingFee::STATUS_UNACTIVE,
                            'is_created_fee' => ServiceBuildingFee::IS_UNCREATED_FEE
                        ]
                    )->orderBy(['end_time' => SORT_ASC])->all();

//                    ServiceBuildingFee::updateAll(
//                        ['status' => ServiceBuildingFee::STATUS_ACTIVE],
//                        [
//                            'id' => $this->is_active_array,
//                            'building_cluster_id' => $buildingCluster->id,
//                            'service_map_management_id' => $serviceMapManagement->id,
//                            'status' => ServiceBuildingFee::STATUS_UNACTIVE,
//                            'is_created_fee' => ServiceBuildingFee::IS_UNCREATED_FEE
//                        ]
//                    );
                }
            }
            $total_change = 0;
            $total_all = 0;
            //tạo payment fee
            if(!empty($ServiceBuildingFees)){
                Yii::info('In create payment fee');
                $ServicePaymentFee = null;
               foreach ($ServiceBuildingFees as $ServiceBuildingFee){
                   $total_all++;
                   //update thời gian xử dụng info
                   $serviceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $ServiceBuildingFee->building_cluster_id, 'apartment_id' => $ServiceBuildingFee->apartment_id, 'service_map_management_id' => $ServiceBuildingFee->service_map_management_id]);
                   if(empty($serviceBuildingInfo)){
                       continue;

//                       //bỏ logic tạo info , bắt buộc phải có trước ở bước import
//                       $serviceBuildingInfo = new ServiceBuildingInfo();
//                       $serviceBuildingInfo->building_cluster_id = $ServiceBuildingFee->building_cluster_id;
//                       $serviceBuildingInfo->building_area_id = $ServiceBuildingFee->building_area_id;
//                       $serviceBuildingInfo->apartment_id = $ServiceBuildingFee->apartment_id;
//                       $serviceBuildingInfo->service_map_management_id = $ServiceBuildingFee->service_map_management_id;
//                       $serviceBuildingInfo->start_date = $ServiceBuildingFee->start_time;
//                       $serviceBuildingInfo->end_date = $ServiceBuildingFee->end_time;
//                       $serviceBuildingInfo->tmp_end_date = $ServiceBuildingFee->end_time;
//                       if (!$serviceBuildingInfo->save()) {
//                           $transaction->rollBack();
//                           Yii::error($serviceBuildingInfo->errors);
//                           return [
//                               'success' => false,
//                               'message' => Yii::t('frontend', "System busy"),
//                           ];
//                       }
                   }else{
//                       if($serviceBuildingInfo->end_date != $ServiceBuildingFee->start_time){
//                           Yii::error("Không trùng time tmp");
//                           Yii::error($ServiceBuildingFee->id);
//                           continue;
//                       }
//                       if($serviceBuildingInfo->end_date < $ServiceBuildingFee->end_time){
                           $serviceBuildingInfo->end_date = $ServiceBuildingFee->end_time;
                           if (!$serviceBuildingInfo->save()) {
                               $transaction->rollBack();
                               Yii::error($serviceBuildingInfo->errors);
                               return [
                                   'success' => false,
                                   'message' => Yii::t('frontend', "System busy"),
                               ];
                           }
//                       }
                   }

                   $ServicePaymentFee = new ServicePaymentFee();
                   $ServicePaymentFee->service_map_management_id = $ServiceBuildingFee->service_map_management_id;
                   $ServicePaymentFee->building_cluster_id = $ServiceBuildingFee->building_cluster_id;
                   $ServicePaymentFee->building_area_id = $ServiceBuildingFee->building_area_id;
                   $ServicePaymentFee->apartment_id = $ServiceBuildingFee->apartment_id;
                   $ServicePaymentFee->description = $ServiceBuildingFee->description;
                   $ServicePaymentFee->description_en = $ServiceBuildingFee->description_en;
                   $ServicePaymentFee->json_desc = $ServiceBuildingFee->json_desc;
                   $ServicePaymentFee->price = $ServiceBuildingFee->total_money;
                   $ServicePaymentFee->is_draft = ServicePaymentFee::IS_NOT_DRAFT;
                   $ServicePaymentFee->approved_by_id = $user->id;
                   $ServicePaymentFee->fee_of_month = $ServiceBuildingFee->fee_of_month;
                   $ServicePaymentFee->day_expired = $ServiceBuildingFee->end_time;
                   $ServicePaymentFee->type = ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE;
                   $ServicePaymentFee->start_time = $ServiceBuildingFee->start_time;
                   $ServicePaymentFee->end_time = $ServiceBuildingFee->end_time;
                   if (!$ServicePaymentFee->save()) {
                       $transaction->rollBack();
                       Yii::error($ServicePaymentFee->errors);
                       return [
                           'success' => false,
                           'message' => Yii::t('frontend', "System busy"),
                       ];
                   }

                   $ServiceBuildingFee->status = ServiceBuildingFee::STATUS_ACTIVE;
                   $ServiceBuildingFee->is_created_fee = ServiceBuildingFee::IS_CREATED_FEE;
                   $ServiceBuildingFee->service_payment_fee_id = $ServicePaymentFee->id;
                   if (!$ServiceBuildingFee->save()) {
                       $transaction->rollBack();
                       Yii::error($ServiceBuildingFee->errors);
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

                   $total_change++;
               }
               if(!empty($ServicePaymentFee)){
                   //gửi thông báo phí tới cư dân
                   $ServicePaymentFee->sendNotifyToResidentUser(ServicePaymentFee::$typeList[ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE],ServicePaymentFee::$typeList_en[ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE]);
               }
            }

            if ($this->is_unactive_all == self::IS_UNACTIVE_ALL) {
                ServiceBuildingFee::updateAll(
                    ['status' => ServiceBuildingFee::STATUS_UNACTIVE],
                    [
                        'building_cluster_id' => $buildingCluster->id,
                        'service_map_management_id' => $serviceMapManagement->id,
                        'status' => ServiceBuildingFee::STATUS_ACTIVE,
                        'is_created_fee' => ServiceBuildingFee::IS_UNCREATED_FEE
                    ]
                );
            } else {
                if (!empty($this->is_unactive_array) && is_array($this->is_unactive_array)) {
                    ServiceBuildingFee::updateAll(
                        ['status' => ServiceBuildingFee::STATUS_UNACTIVE],
                        [
                            'id' => $this->is_unactive_array,
                            'building_cluster_id' => $buildingCluster->id,
                            'service_map_management_id' => $serviceMapManagement->id,
                            'status' => ServiceBuildingFee::STATUS_ACTIVE,
                            'is_created_fee' => ServiceBuildingFee::IS_UNCREATED_FEE
                        ]
                    );
                }
            }
            $transaction->commit();
            Yii::info("===================");
            Yii::info($total_change);
            Yii::info($total_all);
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update success"),
                'total_change' => $total_change,
                'total_all' => $total_all,
            ];
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => Yii::t('frontend', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
