<?php

namespace pay\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\NganLuongV2;
use common\helpers\NganLuongV3;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\PaymentConfig;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\PaymentOrder;
use common\models\PaymentOrderItem;
use common\models\ServiceBill;
use common\models\ServicePaymentFee;
use frontend\models\ServiceBillForm;
use frontend\models\ServiceBillResponse;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentSuccessForm")
 * )
 */
class PaymentSuccessForm extends Model
{
    /**
     * @SWG\Property(description="error_code")
     * @var string
     */
    public $error_code;

    /**
     * @SWG\Property(description="token")
     * @var string
     */
    public $token;

    /**
     * @SWG\Property(description="order_code")
     * @var string
     */
    public $order_code;

    /**
     * @SWG\Property(description="order_id")
     * @var string
     */
    public $order_id;

    /**
     * @SWG\Property(description="payment_id")
     * @var string
     */
    public $payment_id;

    /**
     * @SWG\Property(description="payment_type")
     * @var string
     */
    public $payment_type;

    /**
     * @SWG\Property(description="transaction_info")
     * @var string
     */
    public $transaction_info;

    /**
     * @SWG\Property(description="price")
     * @var string
     */
    public $price;

    /**
     * @SWG\Property(description="error_text")
     * @var string
     */
    public $error_text;

    /**
     * @SWG\Property(description="secure_code")
     * @var string
     */
    public $secure_code;

    public $is_update;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_code'], 'required'],
            [['error_code', 'token', 'order_code', 'order_id'], 'string'],
            [['transaction_info', 'price', 'payment_id', 'payment_type', 'error_text', 'secure_code', 'is_update'], 'safe'],
        ];
    }


    public function success()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $paymentOrder = PaymentOrder::findOne(['code' => $this->order_code]);
            if (empty($paymentOrder)) {
                Yii::error("paymentOrder $this->order_code empty");
                $transaction->rollBack();
                return [
                    'success' => false,
                    'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                ];
            }
            if($paymentOrder->status !== PaymentOrder::STATUS_CREATE){
                $transaction->rollBack();
                $res = [
                    'success' => true,
                ];
                if($paymentOrder->status == PaymentOrder::STATUS_SUCCESS){
                    $res = [
                        'success' => false,
                        'message' => $paymentOrder->error_text,
                    ];
                }
                return $res;
            }
            $paymentConfig = PaymentConfig::findOne(['building_cluster_id' => $paymentOrder->building_cluster_id, 'gate' => PaymentConfig::GATE_NGANLUONG, 'status' => PaymentConfig::STATUS_ACTIVE]);
            if (empty($paymentConfig)) {
                Yii::error("paymentConfig empty");
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('pay', "Payment Config Empty"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if (!empty($this->token)) {
                $nlcheckout = new NganLuongV3($paymentConfig->merchant_id, $paymentConfig->merchant_pass, $paymentConfig->receiver_account, $paymentConfig->checkout_url);
                $nl_result = $nlcheckout->GetTransactionDetail($this->token);
                Yii::info($nl_result);
                if ($nl_result) {
                    $nl_errorcode = (string)$nl_result->error_code;
                    $nl_transaction_status = (string)$nl_result->transaction_status;
                    $paymentOrder->error_code = $nl_errorcode;
                    $paymentOrder->transaction_status = $nl_transaction_status;
                    if ($nl_errorcode == '00') {
                        if ($nl_transaction_status == '00') {
                            if (!$this->is_update) {
                                $transaction->commit();
                                return [
                                    'success' => true,
                                    'message' => 'success',
                                ];
                            }
                            $paymentOrder->status = PaymentOrder::STATUS_SUCCESS;

                            $billRes = self::createBill($paymentOrder);
                            if ($billRes['success']) {
                                $paymentOrder->service_bill_id = $billRes['service_bill_id'];
                            } else {
                                Yii::error('Tao bill error');
                                $transaction->rollBack();
                                return [
                                    'success' => false,
                                    'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                                ];
                            }

                            //update trang thái thanh toán gen code và item
                            $paymentGenCode = PaymentGenCode::findOne(['payment_order_id' => $paymentOrder->id]);
                            if(!empty($paymentGenCode)){
                                $paymentGenCode->status = PaymentGenCode::STATUS_PAID;
                                PaymentGenCodeItem::updateAll(['status' => PaymentGenCodeItem::STATUS_PAID], ['payment_gen_code_id' => $paymentGenCode->id]);
                                if(!$paymentGenCode->save()){
                                    Yii::error($paymentGenCode->errors);
                                    $transaction->rollBack();
                                    return [
                                        'success' => false,
                                        'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                                    ];
                                }
                            }
                            if (!$paymentOrder->save()) {
                                Yii::error($paymentOrder->errors);
                                $transaction->rollBack();
                                return [
                                    'success' => false,
                                    'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                                ];
                            } else {
                                $paymentOrder->successSendEmail();
                                $transaction->commit();
                                return [
                                    'success' => true,
                                    'message' => 'success',
                                ];
                            }

//                            header("Location: $paymentConfig->return_web_url?service_bill_id=$paymentOrder->service_bill_id&status=$paymentOrder->status&message=success");
                        }
                    } else {
                        if (!$this->is_update) {
                            $transaction->commit();
                            return [
                                'success' => false,
                            ];
                        }
                        $paymentOrder->status = PaymentOrder::STATUS_ERROR;
                        $paymentOrder->error_text = $nlcheckout->GetErrorMessage($nl_errorcode);
                        if (!$paymentOrder->save()) {
                            Yii::error($paymentOrder->errors);
                            $transaction->rollBack();
                            return [
                                'success' => false,
                                'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                            ];
                        } else {
                            $transaction->commit();
                            return [
                                'success' => false,
                            ];
                        }

//                        header("Location: $paymentConfig->return_web_url?service_bill_id=$paymentOrder->service_bill_id&status=$paymentOrder->status&message=$paymentOrder->error_text");
                    }
                } else {
                    $transaction->commit();
                    return [
                        'success' => false,
                    ];
                }
            } else {
                $nl = new NganLuongV2();
                $nl->merchant_site_code = $paymentConfig->merchant_id;
                $nl->secure_pass = $paymentConfig->merchant_pass;
                //Tạo link thanh toán đến nganluong.vn
                $checkpay = $nl->verifyPaymentUrl($this->transaction_info, $this->order_code, $this->price, $this->payment_id, $this->payment_type, $this->error_text, $this->secure_code);

                $paymentOrder->error_code = $this->error_code;
                $paymentOrder->error_text = $this->error_text;

                Yii::info($checkpay);

                if ($checkpay) {
                    if (!$this->is_update) {
                        $transaction->commit();
                        return [
                            'success' => true,
                            'message' => 'success',
                        ];
                    }

                    $paymentOrder->status = PaymentOrder::STATUS_SUCCESS;

                    $billRes = self::createBill($paymentOrder);
                    if ($billRes['success']) {
                        $paymentOrder->service_bill_id = $billRes['service_bill_id'];
                    } else {
                        Yii::error('Tao bill error');
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                        ];
                    }

                    //update trang thái thanh toán gen code và item
                    $paymentGenCode = PaymentGenCode::findOne(['payment_order_id' => $paymentOrder->id]);
                    if(!empty($paymentGenCode)){
                        $paymentGenCode->status = PaymentGenCode::STATUS_PAID;
                        PaymentGenCodeItem::updateAll(['status' => PaymentGenCodeItem::STATUS_PAID], ['payment_gen_code_id' => $paymentGenCode->id]);
                        if(!$paymentGenCode->save()){
                            Yii::error($paymentGenCode->errors);
                            $transaction->rollBack();
                            return [
                                'success' => false,
                                'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                            ];
                        }
                    }
                    if (!$paymentOrder->save()) {
                        Yii::error($paymentOrder->errors);
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                        ];
                    } else {
                        $paymentOrder->successSendEmail();
                        $transaction->commit();
                        return [
                            'success' => true,
                            'message' => 'success',
                        ];
                    }
//                    header("Location: $paymentConfig->return_web_url?service_bill_id=$paymentOrder->service_bill_id&status=$paymentOrder->status&message=success");
                } else {
                    if (!$this->is_update) {
                        $transaction->commit();
                        return [
                            'success' => false,
                        ];
                    }
                    $paymentOrder->status = PaymentOrder::STATUS_ERROR;
                    if (!$paymentOrder->save()) {
                        Yii::error($paymentOrder->errors);
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
                        ];
                    } else {
                        $transaction->commit();
                        return [
                            'success' => false,
                        ];
                    }

//                    header("Location: $paymentConfig->return_web_url?service_bill_id=$paymentOrder->service_bill_id&status=$paymentOrder->status&message=$paymentOrder->error_text");
                }
            }
            die();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'statusCode' => ErrorCode::ERROR_STATUS_INVALID,
            ];
        }

    }

    private function createBill($paymentOrder)
    {

        $apartment = Apartment::findOne(['id' => $paymentOrder->apartment_id, 'is_deleted' => Apartment::NOT_DELETED, 'building_cluster_id' => $paymentOrder->building_cluster_id]);
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment->id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if (empty($apartment) || empty($apartmentMapResidentUser)) {
            Yii::error("Invalid data apartment or apartmentMapResidentUser");
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $paymentOrderItems = PaymentOrderItem::find()->where(['payment_order_id' => $paymentOrder->id])->all();
        $paymentGenCode = PaymentGenCode::findOne(['payment_order_id' => $paymentOrder->id]);
        $feeIds = [];
        $feePriceIds = [];
        if(!empty($paymentOrderItems) && !empty($paymentGenCode)){
            foreach ($paymentOrderItems as $item) {
                $check = PaymentGenCodeItem::find()->where(['service_payment_fee_id' => $item->service_payment_fee_id, 'status' => PaymentGenCodeItem::STATUS_UNPAID])
                    ->andWhere(['<>', 'payment_gen_code_id', $paymentGenCode->id])->one();
                if (!empty($check)) {
                    Yii::error('PaymentGenCodeItem is Lock');
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                $feeIds[] = $item->service_payment_fee_id;
                $feePriceIds[$item->service_payment_fee_id] = $item->amount;
            }
        }
        if (empty($feeIds)) {
            Yii::error('feeIds empty');
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $params = [
            'apartment_id' => $paymentOrder->apartment_id,
            'payer_name' => $paymentOrder->txh_name,
            'type_payment' => ServiceBill::TYPE_PAYMENT_ONLINE,
            'description' => 'Thanh toán chuyển khoản online',
            'payment_date' => time(),
            'execution_date' => time(),
        ];
        $ServiceBill = new ServiceBill();
        $ServiceBill->load($params, '');
        $ServiceBill->building_cluster_id = $apartment->building_cluster_id;
        $ServiceBill->building_area_id = $apartment->building_area_id;
        $ServiceBill->status = ServiceBill::STATUS_PAID; // Tạo từ web thì mặc định là đã thanh toán
        $ServiceBill->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
        $ServiceBill->resident_user_name = $apartmentMapResidentUser->resident_user_first_name;
        $ServiceBill->generateCode();
        if (!$ServiceBill->save()) {
            Yii::error($ServiceBill->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceBill->getErrors()
            ];
        } else {
            $r = $ServiceBill->generateNumber();
            if ($r == false) {
                Yii::error('Bill generateNumber error');
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Generate Number Bill Error"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }

            //thêm các item fee mới
            $servicePaymentFees = ServicePaymentFee::find()->where(['status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'id' => $feeIds])->all();
            if (!$ServiceBill->updatePaymentFees($servicePaymentFees, $feePriceIds, true)) {
                Yii::error('updatePaymentFees error');
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }

            if (!$ServiceBill->apartment->updateCurrentDebt()) {
                Yii::error('updateCurrentDebt error');
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            };
            return [
                'success' => true,
                'service_bill_id' => $ServiceBill->id
            ];
        }
    }
}
