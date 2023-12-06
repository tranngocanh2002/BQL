<?php

namespace pay\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\payment\form\MomoRequestForm;
use common\helpers\payment\MomoPay;
use common\helpers\StringUtils;
use common\models\PaymentConfig;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\PaymentOrder;
use common\models\PaymentOrderItem;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentMomoForm")
 * )
 */
class PaymentMomoForm extends Model
{
    /**
     * @SWG\Property(description="name")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="email")
     * @var string
     */
    public $email;

    /**
     * @SWG\Property(description="phone")
     * @var string
     */
    public $phone;

    /**
     * @SWG\Property(description="payment_code")
     * @var string
     */
    public $payment_code;

    /**
     * @SWG\Property(description="token")
     * @var string
     */
    public $token;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_code', 'token'], 'required'],
            [['phone', 'email', 'name', 'payment_code', 'token'], 'string'],
        ];
    }


    public function create(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $paymentGenCode = PaymentGenCode::findOne(['code' => $this->payment_code]);
            if(empty($paymentGenCode)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('pay', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if(empty($this->email)){
                if($paymentGenCode->apartment){
                    if($paymentGenCode->apartment->residentUser){
                        $this->email = $paymentGenCode->apartment->residentUser->email;
                        if(empty($this->name)){
                            $this->name = $paymentGenCode->apartment->resident_user_name;
                        }
                        if(empty($this->phone)){
                            $this->phone = $paymentGenCode->apartment->residentUser->phone;
                        }
                    }
                }
            }
            $paymentGenCodeItems = PaymentGenCodeItem::find()->where(['payment_gen_code_id' => $paymentGenCode->id])->all();
            if(empty($paymentGenCodeItems)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('pay', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            $building_cluster_id = $paymentGenCode->building_cluster_id;
            $paymentConfig = PaymentConfig::findOne(['building_cluster_id' => $building_cluster_id, 'gate' => PaymentConfig::GATE_MOMO, 'status' => PaymentConfig::STATUS_ACTIVE]);
            if (empty($paymentConfig)) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('pay', "Payment Config Empty"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            //tạo payment order
            $paymentOrder = new PaymentOrder();
            $paymentOrder->building_cluster_id = $building_cluster_id;
            $paymentOrder->apartment_id = $paymentGenCode->apartment_id;
            $paymentOrder->pay_gate = PaymentConfig::GATE_MOMO;
            $paymentOrder->total_amount = 0;
            $paymentOrder->txh_name = $this->name;
            $paymentOrder->txt_email = $this->email;
            $paymentOrder->txt_phone = $this->phone;
            $paymentOrder->code = 'MM_'.StringUtils::randomStr(6) . '_' . time();
            if (!$paymentOrder->save()) {
                Yii::error($paymentOrder->errors);
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('pay', "Create Payment Order Error"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            foreach ($paymentGenCodeItems as $paymentGenCodeItem){
                $paymentOrderItem = new PaymentOrderItem();
                $paymentOrderItem->building_cluster_id = $building_cluster_id;
                $paymentOrderItem->payment_order_id = $paymentOrder->id;
                $paymentOrderItem->service_payment_fee_id = $paymentGenCodeItem->service_payment_fee_id;
                $paymentOrderItem->amount = $paymentGenCodeItem->amount;
                if(!$paymentOrderItem->save()){
                    Yii::error($paymentOrderItem->errors);
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('pay', "Create Payment Order Item Error"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                $paymentOrder->total_amount += $paymentGenCodeItem->amount;
            };

            $paymentGenCode->payment_order_id = $paymentOrder->id;

            if(!$paymentOrder->save() || !$paymentGenCode->save()){
                Yii::error($paymentOrder->errors);
                Yii::error($paymentGenCode->errors);
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('pay', "Update Payment Order Error"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            //update timeout payment gen code
            $time_limit = 5; //phút
            $paymentGenCode->lock_time = time() + (60*2*$time_limit);
            if(!$paymentGenCode->save()){
                Yii::error($paymentGenCode->errors);
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('pay', "Update lock_time paymentGenCode error"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            };

            $transaction->commit();
            $momoRequestForm = new MomoRequestForm();
            $momoRequestForm->partnerCode = '';
            $momoRequestForm->partnerRefId = $paymentOrder->code;
            $momoRequestForm->customerNumber = $this->phone;
            $momoRequestForm->appData = $this->token;
            $momoRequestForm->description = 'Thanh toán phí dịch vụ';
            $momoRequestForm->amount = $paymentOrder->total_amount;
            $momoRequestForm->partnerTransId = $this->payment_code;

            $momoPay = MomoPay::instance();
            return $momoPay->payApp($momoRequestForm);

//            return [
//                'success' => true,
//                'message' => Yii::t('pay', 'Success'),
//            ];
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
}
