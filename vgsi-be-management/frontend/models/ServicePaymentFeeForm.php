<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\PaymentGenCodeItem;
use common\models\ServiceDebt;
use common\models\ServiceMapManagement;
use common\models\ServiceParkingFee;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServicePaymentFeeForm")
 * )
 */
class ServicePaymentFeeForm extends Model
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
     * @SWG\Property(description="service map management id", default=1, type="integer")
     * @var integer
     */
    public $service_map_management_id;

    /**
     * @SWG\Property(description="apartment id", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="price", default=1, type="integer")
     * @var integer
     */
    public $price;

    /**
     * @SWG\Property(description="status - 0 : chưa thanh toán, 1 : đã thanh toán", default=0, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="fee of month", default=0, type="integer")
     * @var integer
     */
    public $fee_of_month;

    /**
     * @SWG\Property(description="day expired", default=0, type="integer")
     * @var integer
     */
    public $day_expired;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update']],
            [['ids'], 'safe', "on" => ['delete']],
            [['apartment_id', 'service_map_management_id', 'price', 'status'], 'required'],
            [['id','apartment_id', 'service_map_management_id', 'price', 'status', 'fee_of_month', 'day_expired'], 'integer'],
            [['description', 'description_en'], 'string'],
            [['service_map_management_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceMapManagement::className(), 'targetAttribute' => ['service_map_management_id' => 'id']],
            [['apartment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_id' => 'id']],
        ];
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $apartment = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'building_cluster_id' => $buildingCluster->id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartment)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "The apartment has no head of household"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $serviceMapManagement = ServiceMapManagement::findOne(['id' => $this->service_map_management_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
        if(empty($serviceMapManagement)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServicePaymentFee = new ServicePaymentFee();
        $ServicePaymentFee->load(CUtils::arrLoad($this->attributes), '');
        $ServicePaymentFee->building_cluster_id = $buildingCluster->id;
        $ServicePaymentFee->building_area_id = $apartment->building_area_id;
        if($ServicePaymentFee->type == ServicePaymentFee::TYPE_SERVICE_OLD_DEBIT_FEE){
            if($ServicePaymentFee->price == 0){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Phí thanh toán không hợp lệ"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
        }else{
            if($ServicePaymentFee->price <= 0){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Phí thanh toán không hợp lệ"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
        }
        if (!$ServicePaymentFee->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServicePaymentFee->getErrors()
            ];
        } else {
            return ServicePaymentFeeResponse::findOne(['id' => $ServicePaymentFee->id]);
        }
    }

    public function update()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $paymentGenCodeItem = PaymentGenCodeItem::findOne(['service_payment_fee_id' => (int)$this->id]);
            if(!empty($paymentGenCodeItem)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Phí đã được tạo yêu cầu thanh toán, không được sửa"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $ServicePaymentFee = ServicePaymentFeeResponse::findOne(['id' => (int)$this->id]);
            if ($ServicePaymentFee) {
                if($ServicePaymentFee->status == ServicePaymentFee::STATUS_PAID){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Service Status Paid, Not Update"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }

                if($ServicePaymentFee->money_collected !== 0){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Phí đã thanh toán một phần, không được sửa"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                $price_old = $ServicePaymentFee->price;
                $ServicePaymentFee->load(CUtils::arrLoad($this->attributes), '');
                $price_new = $ServicePaymentFee->price;
                if($ServicePaymentFee->type == ServicePaymentFee::TYPE_SERVICE_OLD_DEBIT_FEE){
                    if($ServicePaymentFee->price == 0){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Phí thanh toán không hợp lệ"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }
                }else{
                    if($ServicePaymentFee->price <= 0){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Phí thanh toán không hợp lệ"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }
                }
                if (!$ServicePaymentFee->save()) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $ServicePaymentFee->getErrors()
                    ];
                } else {
                    if(self::setDebit($ServicePaymentFee->fee_of_month, $ServicePaymentFee->apartment) == false){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "System busy"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                        ];
                    }
                    if($price_new !== $price_old){
                        if($ServicePaymentFee->updatePriceService($ServicePaymentFee->id) == false){
                            $transaction->rollBack();
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "System busy"),
                                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                            ];
                        };
                    }
                    $transaction->commit();
                    return $ServicePaymentFee;
                }
            } else {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    private function setDebit($fee_of_month, $apartment){
        if($fee_of_month < strtotime(date('Y-m-01 00:00:00', time()))){
            //Xóa all công nợ của tháng hiện tại
            ServiceDebt::deleteAll(['type' => ServiceDebt::TYPE_CURRENT_MONTH, 'apartment_id' => $apartment->id]);
            if(!$apartment->updateCurrentDebt($fee_of_month, ServiceDebt::TYPE_OLD_MONTH)){
                return false;
            };
        }else{
            //Xóa all công nợ của tháng hiện tại
            ServiceDebt::deleteAll(['type' => ServiceDebt::TYPE_CURRENT_MONTH, 'apartment_id' => $apartment->id]);
            if(!$apartment->updateCurrentDebt()){
                return false;
            };
        }
        return true;
    }

    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!$this->id && !$this->ids){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $ids = $this->ids;
            if(empty($ids)){
                $ids = [$this->id];
            }
            $paymentGenCodeItem = PaymentGenCodeItem::findOne(['service_payment_fee_id' => $ids]);
            if(!empty($paymentGenCodeItem)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Phí đã được tạo yêu cầu thanh toán, không được xóa"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $servicePaymentFees = ServicePaymentFee::find()->where(['id' => $ids, 'status' => ServicePaymentFee::STATUS_UNPAID])->orderBy(['end_time' => SORT_DESC])->all();
            $ids_del = [];
            $apartment_ids = [];
            $total_del = 0;
            $total_all = 0;
            foreach ($servicePaymentFees as $servicePaymentFee){
                if($servicePaymentFee->status == ServicePaymentFee::STATUS_PAID){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Service Status Paid, Not Update"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                if($servicePaymentFee->money_collected !== 0){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Phí đã được thu một phần, không được xóa"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                if($servicePaymentFee->type == ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Phí đặt dịch vụ tiện ích không được xóa"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                $total_all++;
//                if($servicePaymentFee->type !== ServicePaymentFee::TYPE_SERVICE_OLD_DEBIT_FEE){
                    $r = $servicePaymentFee->updateEndTime($servicePaymentFee->id);
                    if(!$r){
                        continue;
                    }
//                }
                $ids_del[] = $servicePaymentFee->id;
                $apartment_ids[] = [
                    'fee_of_month' => $servicePaymentFee->fee_of_month,
                    'apartment_id' => $servicePaymentFee->apartment_id
                ];
                $total_del++;
            }
            ServicePaymentFee::deleteAll(['id' => $ids_del, 'status' => ServicePaymentFee::STATUS_UNPAID]);
            Yii::info('================');
            Yii::info($total_del);
            Yii::info($total_all);
            //tính lại công nợ của các căn đã xóa phí
            if(!empty($apartment_ids)){
                foreach ($apartment_ids as $item){
                    $apartment = Apartment::findOne($item['apartment_id']);
                    self::setDebit($item['fee_of_month'], $apartment);
                }
            }
            if($total_all == 1 && $total_del == 0){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "The next month's fee has not been deleted"),
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success"),
                'total_del' => $total_del,
                'total_all' => $total_all,
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
