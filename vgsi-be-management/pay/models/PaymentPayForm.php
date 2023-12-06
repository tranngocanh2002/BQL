<?php

namespace pay\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\NganLuong;
use common\helpers\NganLuongV2;
use common\helpers\NganLuongV3;
use common\helpers\StringUtils;
use common\models\PaymentConfig;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\PaymentOrder;
use common\models\PaymentOrderItem;
use common\models\PaymentTransaction;
use common\models\ServiceBill;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentPayForm")
 * )
 */
class PaymentPayForm extends Model
{
    /**
     * @SWG\Property(description="option_payment")
     * @var string
     */
    public $option_payment;

    /**
     * @SWG\Property(description="bankcode")
     * @var string
     */
    public $bankcode;

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


    public $call_by_web;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_code'], 'required'],
            [['phone', 'email', 'name', 'option_payment', 'bankcode', 'payment_code'], 'string'],
            [['call_by_web'], 'integer'],
        ];
    }


    public function pay()
    {
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
//            if(empty($this->email)){
//                $transaction->rollBack();
//                return [
//                    'success' => false,
//                    'message' => Yii::t('pay', "Email empty"),
//                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                ];
//            }
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
            $paymentConfig = PaymentConfig::findOne(['building_cluster_id' => $building_cluster_id, 'gate' => PaymentConfig::GATE_NGANLUONG, 'status' => PaymentConfig::STATUS_ACTIVE]);
            if (empty($paymentConfig)) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('pay', "Payment Config Empty"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }else if(empty($paymentConfig->merchant_id) || empty($paymentConfig->merchant_pass) || empty($paymentConfig->checkout_url) || empty($paymentConfig->return_url) || empty($paymentConfig->notify_url)){
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
            $paymentOrder->pay_gate = PaymentConfig::GATE_NGANLUONG;
            $paymentOrder->total_amount = 0;
            $paymentOrder->txh_name = utf8_encode($this->name);
            $paymentOrder->txt_email = $this->email;
            $paymentOrder->txt_phone = $this->phone;
            $paymentOrder->code = 'NL_'.StringUtils::randomStr(6) . '_' . time();
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
                $paymentOrderItem->building_cluster_id      = $building_cluster_id;
                $paymentOrderItem->payment_order_id         = $paymentOrder->id;
                $paymentOrderItem->service_payment_fee_id   = $paymentGenCodeItem->service_payment_fee_id;
                $paymentOrderItem->amount                   = $paymentGenCodeItem->amount;
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

            $url = '';
            $time_limit = 5; //phút
            if (!empty($this->bankcode)) {
                $nlcheckout = new NganLuongV3($paymentConfig->merchant_id, $paymentConfig->merchant_pass, $paymentConfig->receiver_account, $paymentConfig->checkout_url, $time_limit);
                $total_amount = $paymentOrder->total_amount;

                $array_items[0] = array('item_name1' => 'Thanh toán phí: ' . $paymentGenCode->apartment->name . '/' . trim($paymentGenCode->apartment->parent_path, '/'),
                    'item_quantity1' => 1,
                    'item_amount1' => $total_amount,
                    'item_url1' => 'https://luci.vn/');

//            $array_items = array();
                $payment_method = $this->option_payment;
                $bank_code = @$this->bankcode;
                $order_code = $paymentOrder->code;

                $payment_type = 1;
                $discount_amount = 0;
                $order_description = '';
                $tax_amount = 0;
                $fee_shipping = 0;
                $return_url = $paymentConfig->return_url;
                $cancel_url = urlencode($paymentConfig->cancel_url . '?orderid=' . $order_code);

                $buyer_fullname = $paymentOrder->txh_name;
                $buyer_email = $paymentOrder->txt_email;
                $buyer_mobile = $paymentOrder->txt_phone;

                $buyer_address = '';

                if ($payment_method != '' && $buyer_email != "" && $buyer_mobile != "" && $buyer_fullname != "" && filter_var($buyer_email, FILTER_VALIDATE_EMAIL)) {
                    if ($payment_method == "VISA") {

                        $nl_result = $nlcheckout->VisaCheckout($order_code, $total_amount, $payment_type, $order_description, $tax_amount,
                            $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile,
                            $buyer_address, $array_items, $bank_code);

                    } elseif ($payment_method == "NL") {
                        $nl_result = $nlcheckout->NLCheckout($order_code, $total_amount, $payment_type, $order_description, $tax_amount,
                            $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile,
                            $buyer_address, $array_items);

                    } elseif ($payment_method == "ATM_ONLINE" && $bank_code != '') {
                        $nl_result = $nlcheckout->BankCheckout($order_code, $total_amount, $bank_code, $payment_type, $order_description, $tax_amount,
                            $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile,
                            $buyer_address, $array_items);
                    } elseif ($payment_method == "NH_OFFLINE") {
                        $nl_result = $nlcheckout->officeBankCheckout($order_code, $total_amount, $bank_code, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items);
                    } elseif ($payment_method == "ATM_OFFLINE") {
                        $nl_result = $nlcheckout->BankOfflineCheckout($order_code, $total_amount, $bank_code, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items);

                    } elseif ($payment_method == "IB_ONLINE") {
                        $nl_result = $nlcheckout->IBCheckout($order_code, $total_amount, $bank_code, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items);
                    } elseif ($payment_method == "CREDIT_CARD_PREPAID") {

                        $nl_result = $nlcheckout->PrepaidVisaCheckout($order_code, $total_amount, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items, $bank_code);
                    }
                    //var_dump($nl_result); die;
                    if ($nl_result->error_code == '00') {
                        $url = (string)$nl_result->checkout_url;
                        //Cập nhât order với token  $nl_result->token để sử dụng check hoàn thành sau này
                    } else {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('pay', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $nl_result->error_message
                        ];
                    }
                } else {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('pay', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            } else {
                //$ten= $_POST["txt_test"];
                $receiver = $paymentConfig->receiver_account;
                //Mã đơn hàng
                $order_code = $paymentOrder->code;
                //Khai báo url trả về
                $return_url = !empty($paymentConfig->notify_url) ? $paymentConfig->notify_url : $paymentConfig->return_url;
                // Link nut hủy đơn hàng
                $cancel_url = $paymentConfig->cancel_url;
                $notify_url = $paymentConfig->notify_url;
                //Giá của cả giỏ hàng
                $txh_name = $paymentOrder->txh_name;
                $txt_email = $paymentOrder->txt_email;
                $txt_phone = $paymentOrder->txt_phone;
                $price = $paymentOrder->total_amount;

                //Thông tin giao dịch
                $transaction_info = "Thong tin giao dich";
                $currency = "vnd";
                $quantity = 1;
                $tax = 0;
                $discount = 0;
                $fee_cal = 0;
                $fee_shipping = 0;
                $order_description = "Thong tin don hang: " . $order_code;
                $buyer_info = $txh_name . "*|*" . $txt_email . "*|*" . $txt_phone;
                $affiliate_code = "";
                $time_limit_str = date('d/m/Y,H:i', time()+(60*$time_limit));
                //Khai báo đối tượng của lớp NL_Checkout
                $nl = new NganLuongV2();
                $nl->nganluong_url = $paymentConfig->checkout_url_old;
                $nl->merchant_site_code = $paymentConfig->merchant_id;
                $nl->secure_pass = $paymentConfig->merchant_pass;
                //Tạo link thanh toán đến nganluong.vn
                $url = $nl->buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency, $quantity, $tax, $discount, $fee_cal, $fee_shipping, $order_description, $buyer_info, $affiliate_code);
                //$url= $nl->buildCheckoutUrl($return_url, $receiver, $transaction_info, $order_code, $price);

                //echo $url; die;
                if ($order_code != "") {
                    //một số tham số lưu ý
                    //&cancel_url=http://yourdomain.com --> Link bấm nút hủy giao dịch
                    //&option_payment=bank_online --> Mặc định forcus vào phương thức Ngân Hàng
                    $url .= '&cancel_url=' . $cancel_url . '&notify_url=' . $notify_url.'&time_limit='.$time_limit_str;
                    //$url .='&option_payment=bank_online';
                }
            }
            //update timeout payment gen code
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
            if(!empty($this->call_by_web)){
                header("Location: $url");
                die;
            }
            return [
                'success' => true,
                'message' => Yii::t('pay', 'Success'),
                'url_redirect' => $url,
                'return_url' => $paymentConfig->notify_url,
                'cancel_url' => $paymentConfig->cancel_url,
            ];
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
