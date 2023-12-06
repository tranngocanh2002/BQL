<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use common\models\ServiceElectricFee;
use common\models\ServiceElectricInfo;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceElectricFeeChangeStatusForm")
 * )
 */
class ServiceElectricFeeChangeStatusForm extends Model
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
                $ServiceElectricFees = ServiceElectricFee::find()->where(
                    [
                        'building_cluster_id' => $buildingCluster->id,
                        'service_map_management_id' => $serviceMapManagement->id,
                        'status' => ServiceElectricFee::STATUS_UNACTIVE,
                        'is_created_fee' => ServiceElectricFee::IS_UNCREATED_FEE
                    ]
                )->orderBy(['lock_time' => SORT_ASC])->all();
            } else {
                if (!empty($this->is_active_array) && is_array($this->is_active_array)) {
                    //lấy ra danh sách cần tạo payment fee
                    $ServiceElectricFees = ServiceElectricFee::find()->where(
                        [
                            'id' => $this->is_active_array,
                            'building_cluster_id' => $buildingCluster->id,
                            'service_map_management_id' => $serviceMapManagement->id,
                            'status' => ServiceElectricFee::STATUS_UNACTIVE,
                            'is_created_fee' => ServiceElectricFee::IS_UNCREATED_FEE
                        ]
                    )->orderBy(['lock_time' => SORT_ASC])->all();
                }
            }
            $total_change = 0;
            $total_all = 0;
            //tạo payment fee
            if (!empty($ServiceElectricFees)) {
                Yii::info('In create payment fee');
                $arr_check = [];
                $ServicePaymentFee = null;
                foreach ($ServiceElectricFees as $ServiceElectricFee) {
                    $total_all++;
                    //update thời gian xử dụng info
                    $ServiceElectricInfo = ServiceElectricInfo::findOne(['building_cluster_id' => $ServiceElectricFee->building_cluster_id, 'apartment_id' => $ServiceElectricFee->apartment_id, 'service_map_management_id' => $ServiceElectricFee->service_map_management_id]);
                    if (empty($ServiceElectricInfo)) {
                        continue;
                    } else {
                        if($ServiceElectricFee->start_index >= 0 && $ServiceElectricFee->start_index < $ServiceElectricInfo->end_index){
                            $transaction->rollBack();
                            Yii::error('Chỉ số không đúng');
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "Chỉ số chốt không đúng"),
                            ];
                        }
                        $ServiceElectricInfo->end_index = $ServiceElectricFee->end_index;
                        $ServiceElectricInfo->end_date = $ServiceElectricFee->lock_time;
                        $ServiceElectricInfo->tmp_end_date = $ServiceElectricInfo->end_date;
                        if (!$ServiceElectricInfo->save()) {
                            $transaction->rollBack();
                            Yii::error($ServiceElectricInfo->errors);
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "System busy"),
                            ];
                        }
                    }

                    if(!isset($arr_check[$ServiceElectricInfo->apartment_id]) || $arr_check[$ServiceElectricInfo->apartment_id] == null){
                        $arr_check[$ServiceElectricInfo->apartment_id] = $ServiceElectricFee->end_index;
                    }else{
                        if($ServiceElectricFee->start_index >= 0 && $ServiceElectricFee->start_index <= $arr_check[$ServiceElectricInfo->apartment_id]){
                            $transaction->rollBack();
                            Yii::error($ServiceElectricInfo->errors);
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "Chỉ số chốt không đúng"),
                            ];
                        }else{
                            $arr_check[$ServiceElectricInfo->apartment_id] = $ServiceElectricFee->end_index;
                        }
                    }

                    $ServicePaymentFee = new ServicePaymentFee();
                    $ServicePaymentFee->service_map_management_id = $ServiceElectricFee->service_map_management_id;
                    $ServicePaymentFee->building_cluster_id = $ServiceElectricFee->building_cluster_id;
                    $ServicePaymentFee->building_area_id = $ServiceElectricFee->apartment->building_area_id;
                    $ServicePaymentFee->apartment_id = $ServiceElectricFee->apartment_id;
                    $ServicePaymentFee->description = $ServiceElectricFee->description;
                    $ServicePaymentFee->description_en = $ServiceElectricFee->description_en;
                    $ServicePaymentFee->json_desc = $ServiceElectricFee->json_desc;
                    $ServicePaymentFee->price = $ServiceElectricFee->total_money;
                    $ServicePaymentFee->is_draft = ServicePaymentFee::IS_NOT_DRAFT;
                    $ServicePaymentFee->approved_by_id = $user->id;
                    $ServicePaymentFee->fee_of_month = $ServiceElectricFee->fee_of_month;
                    $ServicePaymentFee->day_expired = $ServiceElectricFee->lock_time + 15 * 24 * 60; // 15 ngày sau ngày tạo phí
                    $ServicePaymentFee->type = ServicePaymentFee::TYPE_SERVICE_ELECTRIC_FEE;
                    $ServicePaymentFee->start_time = $ServiceElectricFee->start_time;
                    $ServicePaymentFee->end_time = $ServiceElectricFee->lock_time;
                    if (!$ServicePaymentFee->save()) {
                        $transaction->rollBack();
                        Yii::error($ServicePaymentFee->errors);
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "System busy"),
                        ];
                    }

                    $ServiceElectricFee->status = ServiceElectricFee::STATUS_ACTIVE;
                    $ServiceElectricFee->is_created_fee = ServiceElectricFee::IS_CREATED_FEE;
                    $ServiceElectricFee->service_payment_fee_id = $ServicePaymentFee->id;
                    if (!$ServiceElectricFee->save()) {
                        $transaction->rollBack();
                        Yii::error($ServiceElectricFee->errors);
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "System busy"),
                        ];
                    }

                    if (!$ServicePaymentFee->apartment->updateCurrentDebt()) {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "System busy"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                        ];
                    };

                }
                if(!empty($ServicePaymentFee)){
                    //gửi thông báo phí tới cư dân
                    $ServicePaymentFee->sendNotifyToResidentUser(ServicePaymentFee::$typeList[ServicePaymentFee::TYPE_SERVICE_ELECTRIC_FEE],ServicePaymentFee::$typeList_en[ServicePaymentFee::TYPE_SERVICE_ELECTRIC_FEE]);
                }
            }

            if ($this->is_unactive_all == self::IS_UNACTIVE_ALL) {
                ServiceElectricFee::updateAll(
                    ['status' => ServiceElectricFee::STATUS_UNACTIVE],
                    [
                        'building_cluster_id' => $buildingCluster->id,
                        'service_map_management_id' => $serviceMapManagement->id,
                        'status' => ServiceElectricFee::STATUS_ACTIVE,
                        'is_created_fee' => ServiceElectricFee::IS_UNCREATED_FEE
                    ]
                );
            } else {
                if (!empty($this->is_unactive_array) && is_array($this->is_unactive_array)) {
                    ServiceElectricFee::updateAll(
                        ['status' => ServiceElectricFee::STATUS_UNACTIVE],
                        [
                            'id' => $this->is_unactive_array,
                            'building_cluster_id' => $buildingCluster->id,
                            'service_map_management_id' => $serviceMapManagement->id,
                            'status' => ServiceElectricFee::STATUS_ACTIVE,
                            'is_created_fee' => ServiceElectricFee::IS_UNCREATED_FEE
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
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
