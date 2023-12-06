<?php

namespace pay\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\payment\form\MomoConfirmForm;
use common\helpers\payment\MomoPay;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\PaymentConfig;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\PaymentOrder;
use common\models\PaymentOrderItem;
use common\models\ServiceBill;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentMomoNotifyForm")
 * )
 */
class PaymentMomoNotifyForm extends Model
{

    public $partnerCode;
    public $accessKey;
    public $amount;
    public $partnerRefId;
    public $partnerTransId;
    public $transType;
    public $momoTransId;
    public $status;
    public $message;
    public $responseTime;
    public $storeId;
    public $signature;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'partnerCode',
                'accessKey',
                'amount',
                'partnerRefId',
                'partnerTransId',
                'transType',
                'momoTransId',
                'status',
                'errorCode',
                'message',
                'responseTime',
                'storeId',
                'signature',
            ], 'safe'],
        ];
    }


    public function ipn()
    {
        $res = [
            "amount" => $this->amount,
            "partnerRefId" => $this->partnerRefId,
            "momoTransId" => $this->momoTransId,
            "signature" => $this->signature
        ];
        $res['status'] = -1;
        $res['message'] = "Không thành công";
        $requestType = 'revertAuthorize';
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if($this->status == 0){ //thành công
                $paymentOrder = PaymentOrder::findOne(['code' => $this->partnerRefId]);
                if (empty($paymentOrder)) {
                    Yii::error("paymentOrder $this->partnerRefId empty");
                    $transaction->rollBack();
                }else{
                    $building_cluster_id = $paymentOrder->building_cluster_id;
                    $paymentConfig = PaymentConfig::findOne(['building_cluster_id' => $building_cluster_id, 'gate' => PaymentConfig::GATE_MOMO, 'status' => PaymentConfig::STATUS_ACTIVE]);
                    if (empty($paymentConfig)) {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('pay', "Payment Config Empty"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }

                    if($paymentOrder->status !== PaymentOrder::STATUS_CREATE){
                        $transaction->rollBack();
                        if($paymentOrder->status == PaymentOrder::STATUS_SUCCESS){
                            $res['status'] = 0;
                            $res['message'] = "Thành công";
                        }
                    }else{
                        $paymentOrder->status = PaymentOrder::STATUS_SUCCESS;
                        $billRes = self::createBill($paymentOrder);
                        if ($billRes['success']) {
                            $paymentOrder->service_bill_id = $billRes['service_bill_id'];
                            //update trang thái thanh toán gen code và item
                            $paymentGenCode = PaymentGenCode::findOne(['payment_order_id' => $paymentOrder->id]);
                            $paymentGenCode->status = PaymentGenCode::STATUS_PAID;
                            PaymentGenCodeItem::updateAll(['status' => PaymentGenCodeItem::STATUS_PAID], ['payment_gen_code_id' => $paymentGenCode->id]);

                            if (!$paymentOrder->save() || !$paymentGenCode->save()) {
                                Yii::error($paymentGenCode->errors);
                                Yii::error($paymentOrder->errors);
                                $transaction->rollBack();
                            } else {
                                $paymentOrder->successSendEmail();
                                $transaction->commit();
                                $res['status'] = 0;
                                $res['message'] = "Thành công";
                                $requestType = 'capture';
                            }
                        } else {
                            Yii::error('Tao bill error');
                            $transaction->rollBack();
                        }
                    }
                }
            }else{
                $transaction->rollBack();
            }
        } catch (\Exception $e) {
            Yii::error($e);
            $transaction->rollBack();
        }

        //xác nhận giao dịch sang momo
        $momoConfirm = new MomoConfirmForm();
        $momoConfirm->partnerCode = $this->partnerCode;
        $momoConfirm->partnerRefId = $this->partnerRefId;
        $momoConfirm->requestType = $requestType;
        $momoConfirm->requestId = time();
        $momoConfirm->momoTransId = $this->momoTransId;
        $momoConfirm->customerNumber = $paymentOrder->txt_phone;
        $momoConfirm->serectkey = $paymentConfig->secret_key;

        $momoPay = MomoPay::instance();
        $res = $momoPay->confirm($momoConfirm);
        Yii::info($res);

        return $res;
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
