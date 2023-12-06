<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\ServiceBillItem;
use common\models\ServiceMapManagement;
use common\models\ServiceBill;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBillForm")
 * )
 */
class ServiceBillForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="apartment id", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="management user name : người thu tiền", default="", type="string")
     * @var string
     */
    public $management_user_name;

    /**
     * @SWG\Property(description="payer name : người thanh toán", default="", type="string")
     * @var string
     */
    public $payer_name;

    /**
     * @SWG\Property(description="type payment name : Loại thanh toán (0: Tiền mặt, 1: chuyển khoản)", default=0, type="integer")
     * @var string
     */
    public $type_payment;

    /**
     * @SWG\Property(description="description : mô tả phiếu", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="service payment fee: mảng id và tiền thực thu thêm vào", type="array",
     *     @SWG\Items(type="object",
     *         @SWG\Property(property="service_payment_fee_id", type="integer"),
     *         @SWG\Property(property="price", type="integer", description="Số tiền thực thu của lần thu hiện tại"),
     *     ),
     * ),
     * @var array
     */
    public $service_payment_fees;

    /**
     * @SWG\Property(description="payment_date: ngày nộp", type="integer")
     * @var integer
     */
    public $payment_date;

    /**
     * @SWG\Property(description="execution_date: ngày thực hiện", type="integer")
     * @var integer
     */
    public $execution_date;

    /**
     * @SWG\Property(description="payment_gen_code : code yêu cầu thanh toán", type="string")
     * @var string
     */
    public $payment_gen_code;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['apartment_id'], 'required', "on" => ['create'], 'message' => Yii::t('frontend', 'Chưa chọn căn hộ cần tạo phiếu')],
            [['service_payment_fees'], 'required', "on" => ['create'], 'message' => Yii::t('frontend', 'Chưa chọn phí cần tạo phiếu')],
            [['id','apartment_id', 'type_payment', 'payment_date', 'execution_date'], 'integer'],
            [['payer_name', 'management_user_name', 'description', 'payment_gen_code'], 'string'],
            [['service_payment_fees'], 'safe'],
            [['payment_gen_code'], 'validatePaymentGenCde']
        ];
    }

    public function validatePaymentGenCde($attribute, $params, $validator)
    {
        $paymentGenCode = PaymentGenCode::findOne(['code' => $this->payment_gen_code]);
        if (empty($paymentGenCode)) {
            $this->addError($attribute, Yii::t('frontend', 'Mã xác nhận thanh toán không hợp lệ'));
        }else{
            $idItems = [];
            $paymentGenCodeItems = PaymentGenCodeItem::find()->where(['payment_gen_code_id' => $paymentGenCode->id])->all();
            $priceIn = [];
            foreach ($paymentGenCodeItems as $paymentGenCodeItem){
                $idItems[] = (int)$paymentGenCodeItem->service_payment_fee_id;
                $priceIn[(int)$paymentGenCodeItem->service_payment_fee_id] = (int)$paymentGenCodeItem->amount;
            }
            $idFees = [];
            foreach ($this->service_payment_fees as $fee){
                $idFees[] = (int)$fee['service_payment_fee_id'];
                if(!isset($priceIn[(int)$fee['service_payment_fee_id']]) || $priceIn[(int)$fee['service_payment_fee_id']] != (int)$fee['price']){
                    $this->addError($attribute, Yii::t('frontend', 'Bạn phải thanh toán đầy đủ số tiền từ xác nhận thanh toán'));
                }
            }
            if((count(array_diff($idItems, $idFees)) > 0) || (count(array_diff($idFees, $idItems)) > 0)){
                $this->addError($attribute, Yii::t('frontend', 'Tồn tại phí không thuộc mã xác nhận thanh toán'));
            }
        }
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $apartment = Apartment::findOne(['id' => $this->apartment_id, 'is_deleted' => Apartment::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment->id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartment) || empty($apartmentMapResidentUser)){
                Yii::error("Invalid data apartment or apartmentMapResidentUser");
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            $feeIds = [];
            $feePriceIds = [];
            $paymentGenCode = PaymentGenCode::findOne(['code' => $this->payment_gen_code]);
            $payment_gen_code_id = null;
            if(!empty($paymentGenCode)){
                $payment_gen_code_id = $paymentGenCode->id;
            }
            foreach ($this->service_payment_fees as $fee){
                $check = PaymentGenCodeItem::find()->where(['service_payment_fee_id' => $fee['service_payment_fee_id'], 'status' => PaymentGenCodeItem::STATUS_UNPAID]);
                if(!empty($payment_gen_code_id)){
                    $check = $check->andFilterWhere(['not', ['payment_gen_code_id' => $payment_gen_code_id]]);
                }
                $check = $check->one();
                if(!empty($check)){
                    Yii::error('PaymentGenCodeItem is Lock');
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "There is a fee in the request for payment, a payment need to be made"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                $feeIds[] = $fee['service_payment_fee_id'];
                $feePriceIds[$fee['service_payment_fee_id']] = $fee['price'];
            }
            if(empty($feeIds)){
                Yii::error('feeIds empty');
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            foreach ($feeIds as $feeId) {
                $servicePaymentFee = ServicePaymentFee::findOne(['id' => $feeId]);
                if ($servicePaymentFee) {
                    $idServiceMapManagement = $servicePaymentFee->service_map_management_id;
                    // $a = $this->service_payment_fees['service_payment_fee_id'];
                    $serviceMapManagement = ServiceMapManagement::findOne(['id' => $idServiceMapManagement, 'is_deleted' => ServiceMapManagement::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
                    $ServiceUtilityBooking = ServiceUtilityBooking::findOne(
                        [
                            'building_cluster_id' => $buildingCluster->id,
                            // 'building_area_id' => $servicePaymentFee->building_area_id,
                            // 'apartment_id' => $servicePaymentFee->apartment_id,
                            'service_map_management_id' => $serviceMapManagement->id,
                            'id' => $servicePaymentFee->service_utility_booking_id,
                        ]
                    );
                    if($ServiceUtilityBooking) {
                        $ServiceUtilityBooking->is_paid = ServiceUtilityBooking::IS_PAID;
                        if (!$ServiceUtilityBooking->save()) {
                            $transaction->rollBack();
                            Yii::error($ServiceUtilityBooking->errors);
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "System busy"),
                            ];
                        }
                    }
                }
            }
            
            $ServiceBill = new ServiceBill();
            $ServiceBill->load(CUtils::arrLoad($this->attributes), '');
            $ServiceBill->building_cluster_id = $apartment->building_cluster_id;
            $ServiceBill->building_area_id = $apartment->building_area_id;
            $ServiceBill->status = ServiceBill::STATUS_PAID; // Tạo từ web thì mặc định là đã thanh toán
            $ServiceBill->management_user_id = $user->id;
            $ServiceBill->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
            $ServiceBill->resident_user_name = $apartmentMapResidentUser->resident_user_first_name;
            if(!empty($paymentGenCode)){
                $ServiceBill->payment_gen_code_id = $paymentGenCode->id;
            }
            if((!isset($ServiceBill->type) || $ServiceBill->type = ServiceBill::TYPE_0) && ($ServiceBill->type_payment == ServiceBill::TYPE_PAYMENT_INTERNET_BANKING)){
                $ServiceBill->bank_name = $buildingCluster->bank_name;
                $ServiceBill->bank_holders = $buildingCluster->bank_holders;
                $ServiceBill->bank_account = $buildingCluster->bank_account;
            }
            $ServiceBill->generateCode();
            if (!$ServiceBill->save()) {
                Yii::error($ServiceBill->getErrors());
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceBill->getErrors()
                ];
            } else {
                $r = $ServiceBill->generateNumber();
                if($r == false){
                    Yii::error('Bill generateNumber error');
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Generate Number Bill Error"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }

                //thêm các item fee mới
                $servicePaymentFees = ServicePaymentFee::find()->where(['status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'id' => $feeIds])->all();
                if(empty($servicePaymentFees)){
                    Yii::error('Không còn phí để lập phiếu');
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Không còn phí để lập phiếu"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
                if(!$ServiceBill->updatePaymentFees($servicePaymentFees, $feePriceIds, true)){
                    Yii::error('updatePaymentFees error');
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }

                if(!$ServiceBill->apartment->updateCurrentDebt()){
                    Yii::error('updateCurrentDebt error');
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                };

                //update trạng thái của yêu cầu thanh toán nếu có
                if(!empty($paymentGenCode)){
                    //check các phí đã thanh toán hết để update item code
                    $paymentFees = ServicePaymentFee::find()->where(['id' => $feeIds, 'more_money_collecte' => 0])->all();
                    $idFees = [];
                    foreach ($paymentFees as $fee){
                        $idFees[] = $fee->id;
                    }
                    if(!empty($idFees)){
                        PaymentGenCodeItem::updateAll(['status' => PaymentGenCodeItem::STATUS_PAID], ['service_payment_fee_id' => $idFees]);
                    }
                    $codeItem = PaymentGenCodeItem::findOne(['status' => PaymentGenCodeItem::STATUS_UNPAID, 'payment_gen_code_id' => $paymentGenCode->id]);
                    if(empty($codeItem)){
                        $paymentGenCode->status = PaymentGenCode::STATUS_PAID;
                        if(!$paymentGenCode->save()){
                            Yii::error($paymentGenCode->errors);
                            $transaction->rollBack();
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "Invalid data"),
                                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                            ];
                        }
                    }
                }

                $transaction->commit();
                return ServiceBillResponse::findOne(['id' => $ServiceBill->id]);
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

    public function update()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
//            $feeIds = [];
//            $feePriceIds = [];
//            foreach ($this->service_payment_fees as $fee){
//                $check = PaymentGenCodeItem::findOne(['service_payment_fee_id' => $fee['service_payment_fee_id']]);
//                if(!empty($check)){
//                    $transaction->rollBack();
//                    return [
//                        'success' => false,
//                        'message' => Yii::t('frontend', "There is a fee in the request for payment, a payment need to be made"),
//                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                    ];
//                }
//                $feeIds[] = $fee['service_payment_fee_id'];
//                $feePriceIds[$fee['service_payment_fee_id']] = $fee['price'];
//            }
//            if(empty($feeIds)){
//                $transaction->rollBack();
//                return [
//                    'success' => false,
//                    'message' => Yii::t('frontend', "Invalid data"),
//                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                ];
//            }

            $ServiceBill = ServiceBillResponse::findOne(['id' => (int)$this->id]);
            if ($ServiceBill) {
                if(in_array($ServiceBill->status, [ServiceBill::STATUS_CANCEL, ServiceBill::STATUS_BLOCK])){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Status incorrect"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }

                $ServiceBill->load(CUtils::arrLoad($this->attributes), '');
                Yii::info($ServiceBill->attributes);
                if (!$ServiceBill->save()) {
                    Yii::info($ServiceBill->getErrors());
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $ServiceBill->getErrors()
                    ];
                } else {
//                    //xóa toàn bộ item cũ
//                    if(!$ServiceBill->resetPaymentFees(true)){
//                        $transaction->rollBack();
//                        return [
//                            'success' => false,
//                            'message' => Yii::t('frontend', "Invalid data"),
//                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
//                        ];
//                    }
//
//                    //thêm item mới
//                    $servicePaymentFees = ServicePaymentFee::find()->where(['status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'id' => $feeIds])->all();
//                    if(!$ServiceBill->updatePaymentFees($servicePaymentFees, $feePriceIds, true)){
//                        $transaction->rollBack();
//                        return [
//                            'success' => false,
//                            'message' => Yii::t('frontend', "Invalid data"),
//                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
//                        ];
//                    }
//
//                    if(!$ServiceBill->apartment->updateCurrentDebt()){
//                        $transaction->rollBack();
//                        return [
//                            'success' => false,
//                            'message' => Yii::t('frontend', "Invalid data"),
//                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
//                        ];
//                    };

//                    $total_price = 0;
//                    if(!empty($this->service_payment_fee_id) && is_array($this->service_payment_fee_id)){
//                        ServicePaymentFee::updateAll(['service_bill_id' => $ServiceBill->id, 'service_bill_code' => $ServiceBill->code], ['id' => $this->service_payment_fee_id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT]);
//                        $servicePaymentFees = ServicePaymentFee::find()->where(['status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'id' => $this->service_payment_fee_id])->all();
//                        if(!$ServiceBill->updatePaymentFees($servicePaymentFees)){
//                            $transaction->rollBack();
//                            return [
//                                'success' => false,
//                                'message' => Yii::t('frontend', "Invalid data"),
//                                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
//                            ];
//                        }
//                    }
//                    if(!empty($this->service_bill_item_id_delete) && is_array($this->service_bill_item_id_delete)){
//                        $serviceBillItems = ServiceBillItem::find()->where(['id' => $this->service_bill_item_id_delete])->all();
//                        $feeIds = [];
//                        foreach ($serviceBillItems as $serviceBillItem){
//                            $feeIds[] = $serviceBillItem->service_payment_fee_id;
//                        }
//                        ServiceBillItem::deleteAll(['service_bill_id' => $ServiceBill->id,'id' => $this->service_bill_item_id_delete]);
//                        ServicePaymentFee::updateAll(['service_bill_id' => null, 'service_bill_code' => null], ['id' => $feeIds]);
//                    }
                    $transaction->commit();
                    return $ServiceBill;
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

    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!$this->id){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $ServiceBill = ServiceBill::findOne(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id]);
            if(empty($ServiceBill)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $payment_gen_code_id = $ServiceBill->payment_gen_code_id;
            if(in_array($ServiceBill->status, [ServiceBill::STATUS_CANCEL, ServiceBill::STATUS_PAID, ServiceBill::STATUS_BLOCK])){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Status incorrect"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if(!$ServiceBill->resetPaymentFees(true)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }

            if($ServiceBill->delete()){

                if(!empty($payment_gen_code_id)){
                    $paymentGenCode = PaymentGenCode::findOne(['id' => $payment_gen_code_id]);
                    if(!empty($paymentGenCode)){
                        $paymentGenCode->status = PaymentGenCode::STATUS_UNPAID;
                        if($paymentGenCode->save()){
                            $transaction->rollBack();
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "System busy"),
                                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                            ];
                        }
                        PaymentGenCodeItem::updateAll(['status' => PaymentGenCodeItem::STATUS_UNPAID],['payment_gen_code_id' => $paymentGenCode->id]);
                    }
                }

                $transaction->commit();
                return [
                    'success' => true,
                    'message' => Yii::t('frontend', "Delete Success")
                ];
            }else{
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

}
