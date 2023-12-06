<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityBookingChangeStatusForm")
 * )
 */
class ServiceUtilityBookingChangeStatusForm extends Model
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
     * @SWG\Property(description="title", default="", type="string")
     * @var string
     */
    public $title;


    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['service_map_management_id','title'], 'required'],
            [['service_map_management_id', 'is_active_all', 'is_unactive_all'], 'integer'],
            [['is_active_array', 'is_unactive_array'], 'safe'],
        ];
    }

    public function changeStatus($title = "")
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $buildingCluster = Yii::$app->building->BuildingCluster;

            $serviceMapManagement = ServiceMapManagement::findOne(['id' => $this->service_map_management_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
            if (empty($serviceMapManagement)) {
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
                $ServiceUtilityBookings = ServiceUtilityBooking::find()->where(
                    [
                        'building_cluster_id' => $buildingCluster->id,
                        'service_map_management_id' => $serviceMapManagement->id,
                        'status' => ServiceUtilityBooking::STATUS_CREATE,
//                        'is_created_fee' => ServiceUtilityBooking::IS_UNCREATED_FEE,
                    ]
                )->orderBy(['id' => SORT_ASC])->all();

            } else {
                if (!empty($this->is_active_array) && is_array($this->is_active_array)) {
                    //lấy ra danh sách cần tạo payment fee
                    $ServiceUtilityBookings = ServiceUtilityBooking::find()->where(
                        [
                            'id' => $this->is_active_array,
                            'building_cluster_id' => $buildingCluster->id,
                            'service_map_management_id' => $serviceMapManagement->id,
                            'status' => ServiceUtilityBooking::STATUS_CREATE,
//                            'is_created_fee' => ServiceUtilityBooking::IS_UNCREATED_FEE,
                        ]
                    )->orderBy(['id' => SORT_ASC])->all();

                }
            }
            $total_change = 0;
            $total_all = 0;
            //tạo payment fee
            if (!empty($ServiceUtilityBookings)) {
                Yii::info('In create payment fee');
                $fee_of_month = time();
                foreach ($ServiceUtilityBookings as $ServiceUtilityBooking) {
                    $total_all++;
                    $feeIds = [];
                    if ($ServiceUtilityBooking->price > 0) { // nếu book có giá lớn hơn 0 mới tạo phí vào công nợ
                        $feeIds = [$ServiceUtilityBooking->service_payment_fee_id];
                    }
                    if (!empty($ServiceUtilityBooking->service_payment_fee_deposit_ids)) {
                        $feeDepositIds = Json::decode($ServiceUtilityBooking->service_payment_fee_deposit_ids, true);
                        $feeIds = array_merge($feeIds, $feeDepositIds);
                    }
                    if (!empty($feeIds)) {
                        $ServicePaymentFees = ServicePaymentFee::find()->where(['id' => $feeIds])->all();
                        $is_send = 0;
                        foreach ($ServicePaymentFees as $ServicePaymentFee) {
                            $ServicePaymentFee->is_debt = ServicePaymentFee::IS_DEBT;
                            $ServicePaymentFee->fee_of_month = $fee_of_month;
                            $ServicePaymentFee->approved_by_id = $user->id;
                            if (!$ServicePaymentFee->save()) {
                                Yii::error($ServicePaymentFee->errors);
                                $transaction->rollBack();
                                return [
                                    'success' => false,
                                    'message' => Yii::t('frontend', "System busy"),
                                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                                ];
                            }

                            $ServiceUtilityBooking->fee_of_month = $fee_of_month;
                            if (!$ServicePaymentFee->apartment->updateCurrentDebt()) {
                                $transaction->rollBack();
                                return [
                                    'success' => false,
                                    'message' => Yii::t('frontend', "System busy"),
                                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                                ];
                            };

                            //gửi thông báo phí tới cư dân
                            // if ($is_send == 0) {
                            //     $ServicePaymentFee->sendNotifyToResidentUser($this->title);
                            //     $is_send = 1;
                            // }
                        }
                    }

                    $ServiceUtilityBooking->status = ServiceUtilityBooking::STATUS_ACTIVE;
                    if (!$ServiceUtilityBooking->save()) {
                        $transaction->rollBack();
                        Yii::error($ServiceUtilityBooking->errors);
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "System busy"),
                        ];
                    }
                    
                    if($ServiceUtilityBooking->status == ServiceUtilityBooking::STATUS_ACTIVE)
                    {
                        $ServiceUtilityBookingStatusChanges = ServiceUtilityBooking::find()->where(
                            [
                                'building_cluster_id' => $buildingCluster->id,
                                'service_map_management_id' => $serviceMapManagement->id,
                                'status' => ServiceUtilityBooking::STATUS_CREATE,
                                'service_utility_free_id' => $ServiceUtilityBooking->service_utility_free_id,
                                'service_utility_config_id' => $ServiceUtilityBooking->service_utility_config_id,
                                'start_time' => $ServiceUtilityBooking->start_time,
                                'end_time' => $ServiceUtilityBooking->end_time,
                            ]
                        )->orderBy(['id' => SORT_ASC])->all();
    
                        foreach($ServiceUtilityBookingStatusChanges as $ServiceUtilityBookingStatusChange)
                        {
                            if (!empty($ServiceUtilityBookingStatusChange)){
                                $ServiceUtilityBookingStatusChange->reason = ServiceUtilityBooking::REASON_CANCEL_SYSTEM ; 
                                $ServiceUtilityBookingStatusChange->cancelBook(ServiceUtilityBooking::STATUS_CANCEL_SYSTEM, ServiceUtilityBooking::REASON_CANCEL_SYSTEM);
                                if (!$ServiceUtilityBookingStatusChange->save()) {
                                    $transaction->rollBack();
                                    Yii::error($ServiceUtilityBooking->errors);
                                    return [
                                        'success' => false,
                                        'message' => Yii::t('frontend', "System busy"),
                                    ];
                                }
                                 $ServiceUtilityBookingStatusChange->sendNotifyToResidentUser(null, null, ServiceUtilityBooking::UPDATE_STATUS);
                            }
                        }
                    }
                    // if (!empty($ServiceUtilityBooking->apartment) && !empty($ServiceUtilityBooking->apartment->residentUser)) {
                    //     $ServiceUtilityBooking->sendNotifyToResidentUser(null, null, ServiceUtilityBooking::UPDATE_STATUS);
                    // }
                    if (!empty($ServiceUtilityBooking->apartment)) {
                        $ServiceUtilityBooking->sendNotifyToResidentUser(null, null, ServiceUtilityBooking::UPDATE_STATUS);
                    }
                    $total_change++;
                }
            }

            if ($this->is_unactive_all == self::IS_UNACTIVE_ALL) {
                ServiceUtilityBooking::updateAll(
                    ['status' => ServiceUtilityBooking::STATUS_CANCEL],
                    [
                        'building_cluster_id' => $buildingCluster->id,
                        'service_map_management_id' => $serviceMapManagement->id,
                        'status' => ServiceUtilityBooking::STATUS_ACTIVE,
//                        'is_created_fee' => ServiceUtilityBooking::IS_UNCREATED_FEE
                    ]
                );
            } else {
                if (!empty($this->is_unactive_array) && is_array($this->is_unactive_array)) {
                    ServiceUtilityBooking::updateAll(
                        ['status' => ServiceUtilityBooking::STATUS_CANCEL],
                        [
                            'id' => $this->is_unactive_array,
                            'building_cluster_id' => $buildingCluster->id,
                            'service_map_management_id' => $serviceMapManagement->id,
                            'status' => ServiceUtilityBooking::STATUS_ACTIVE,
//                            'is_created_fee' => ServiceUtilityBooking::IS_UNCREATED_FEE
                        ]
                    );
                }
            }
            $transaction->commit();
            Yii::info("===========");
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
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}