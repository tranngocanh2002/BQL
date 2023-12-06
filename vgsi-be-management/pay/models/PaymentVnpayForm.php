<?php

namespace pay\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\payment\form\MomoRequestForm;
use common\helpers\payment\MomoPay;
use common\helpers\StringUtils;
use common\models\PaymentConfig;
use common\models\PaymentGenCode;
use pay\models\ServicePaymentFeeResponse;
use common\models\PaymentGenCodeItem;
use common\models\PaymentOrder;
use common\models\PaymentOrderItem;
use common\models\ResidentUser;
use resident\models\PaymentGenCodeForm;
use common\models\ApartmentMapResidentUser;
use common\models\ServicePaymentFee;
use common\models\ServiceBill;
use resident\models\ServiceBillResponse;
use DateTime;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentMomoForm")
 * )
 */
class PaymentVnpayForm extends Model
{
     /**
     * @SWG\Property(description="type: 0 - chuyển khoản, 1 - thanh toán online", default=0, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="apartment id", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="service payment fee ids : mảng id thêm vào", type="array",
     *     @SWG\Items(type="integer", default=0),
     * ),
     * @var array
     */
    public $service_payment_fee_ids;

    /**
     * @SWG\Property(description="code truyền lên khi del code", default="", type="string")
     * @var string
     */
    public $code;

    /**
     * @SWG\Property(description="user_phone: số điện thoại", default="", type="integer")
     * @var integer
     */
    public $user_phone;

    /**
     * {@inheritdoc}
     */

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['type', 'apartment_id','service_payment_fee_ids','user_phone'], 'required'],
            // [['type', 'apartment_id'], 'integer'],
            // [['phone', 'email', 'name', 'payment_code', 'token'], 'string'],
        ];
    }


    public function create(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // config
            $paymentConfig = PaymentConfig::find()->where(['building_cluster_id'=> 1,'gate'=>1])->one();

            $vnp_TmnCode    = $paymentConfig->merchant_name; //Mã định danh merchant kết nối (Terminal Id)
            $vnp_HashSecret = $paymentConfig->secret_key; //Secret key
            $vnp_Url        = $paymentConfig->checkout_url;
            // $vnp_Returnurl = "http://localhost/payment-vnpay/return-url";
            $vnp_Returnurl = $paymentConfig->return_url;
            $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
            $apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";

            //input format

            $startTime = date("YmdHis");
            $expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
            $vnp_TxnRef = $this->generateRandomString(8); //Mã giao dịch thanh toán tham chiếu của merchant
            $vnp_Amount = Yii::$app->request->get('amount', 500000); //số tiền thanh toán
            $vnp_Locale = $_POST['language'] ?? 'Vi'; //Ngôn ngữ chuyển hướng thanh toán
            $vnp_BankCode = Yii::$app->request->get('bank_code', "VNBANK"); //Xét mã phương thức thanh toán nếu không truyền lên thì sẽ chuyển hướng đến trang chọn phương thức của VNPay
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount"  => $vnp_Amount * 100,
                "vnp_Command" => "pay",
                "vnp_CreateDate"=> date('YmdHis'),
                "vnp_CurrCode"  => "VND",
                "vnp_IpAddr"    => $vnp_IpAddr,
                "vnp_Locale"    => $vnp_Locale,
                "vnp_OrderInfo" => Yii::$app->request->get('type', 5)."/".Yii::$app->request->get('apartment_id', 0)."/".Yii::$app->request->get('user_phone', 0)."/".json_encode(Yii::$app->request->get('service_payment_fee_ids', 0)),
                "vnp_OrderType" => "other",
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef"    => $vnp_TxnRef,
                "vnp_ExpireDate"=> $expire,
            );

            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            return [
                'success' => true,
                'message' => 'Success',
                'statusCode' => 200,
                'data' => [
                    'url' => $vnp_Url
                ]
            ];
            header('Location: ' . $vnp_Url);
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

    public function returnUrl(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // config
            $paymentConfig = PaymentConfig::find()->where(['building_cluster_id'=> 1,'gate'=>1])->one();

            $vnp_TmnCode    = $paymentConfig->merchant_name; //Mã định danh merchant kết nối (Terminal Id)
            $vnp_HashSecret = $paymentConfig->secret_key; //Secret key
            $vnp_Url        = $paymentConfig->checkout_url;
            // $vnp_Returnurl = "http://localhost/payment-vnpay/return-url";
            $vnp_Returnurl = $paymentConfig->return_url;
            $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
            $apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";

            $dataUser = explode("/",$_GET['vnp_OrderInfo']);
            $vnp_SecureHash = $_GET['vnp_SecureHash'];
            $inputData = array();
            foreach ($_GET as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }
            unset($inputData['vnp_SecureHash']);
            ksort($inputData);
            $i = 0;
            $hashData = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }
    
            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            if ($secureHash == $vnp_SecureHash) {
                if ($_GET['vnp_ResponseCode'] == '00') {
                    $orderId = $_GET['vnp_TxnRef'];
                    $order = ServiceBill::findOne(['code' => $orderId]);
                    if(!empty($order))
                    {
                        $order->status = 1 ;
                        $order->save();
                        $transaction->commit();
                    }
                    // $this->createPaymentGenCode($dataUser,$_GET['vnp_TxnRef']);
                   return [
                        'success' => true,
                        'message' => Yii::t('pay', "SUCCESS"),
                        'statusCode' => 200,
                        'data'    => [
                            'vnp_TxnRef' => $_GET['vnp_TxnRef'],
                            'vnp_Amount' => $_GET['vnp_Amount'],
                            'vnp_dataUser' => $dataUser
                        ]
                    ];
                } 
                return [
                    'success' => false,
                    'message' => Yii::t('pay', "Error"),
                    'statusCode' => 201
                ];
            } 
            return [
                'success' => false,
                'message' => Yii::t('pay', "Error"),
                'statusCode' => 202
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('pay', "Error"),
                'statusCode' => 204
            ];
        }
    }

    protected function createPaymentGenCode($dataUser,$vnp_TxnRef)
    {
        $resultFeeId = [];
        $type = (int) ($dataUser[0] ?? 5);
        $apartment_id = (int) ($dataUser[1] ?? 0);
        $user_phone = (int) ($dataUser[2] ?? 0);
        $service_payment_fee_ids = json_decode($dataUser[3]);

        foreach (json_decode($dataUser[3]) as $item) {
            $resultFeeId[] = $item;
        }

        $service_payment_fee_ids = $resultFeeId;
        $transaction = Yii::$app->db->beginTransaction();
        $user = ResidentUser::find()->where(['phone' => $this->user_phone]);

        try {
            if (!is_array($this->service_payment_fee_ids)) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment_id, 'resident_user_phone' => $user_phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);

            if (empty($apartmentMapResidentUser)) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            //check phi âm => check = 0
            $servicePaymentFeeCheckAm = ServicePaymentFee::find()
                ->where(['building_cluster_id' => $apartmentMapResidentUser->building_cluster_id, 'apartment_id' => $this->apartment_id, 'id' => $this->service_payment_fee_ids])
                ->andWhere(['more_money_collecte' => 0])
                ->one();
            if(!empty($servicePaymentFeeCheckAm)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            $paymentGenCode = new PaymentGenCode();
            $paymentGenCode->building_cluster_id = 1;
            $paymentGenCode->description         = "X";
            $paymentGenCode->image               = "/uploads/images/202308/1692603179-1cf66d88-d826-4834-8cde-e21684ab1ec1.jpg";
            $paymentGenCode->apartment_id        = 83;
            $paymentGenCode->type                = 1;
            $paymentGenCode->resident_user_id    = 159;
            $paymentGenCode->status              = 0;
            $paymentGenCode->code                = $vnp_TxnRef ?? "3DNRQAZ5";
            if($paymentGenCode->type == PaymentGenCode::PAY_ONLINE){
                $paymentGenCode->lock_time = time() + (60 * 5);
            }
            if (!$paymentGenCode->save()) {
                Yii::error($paymentGenCode->errors);
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $paymentGenCode->getErrors()
                ];
            }

            $transaction->commit();

            return [
                'payment_code' => $paymentGenCode->code,
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

    }
    public function ipn(){
        $transaction = Yii::$app->db->beginTransaction();
        
        // config
        $paymentConfig = PaymentConfig::find()->where(['building_cluster_id'=> 1,'gate'=>1])->one();

        $vnp_TmnCode    = $paymentConfig->merchant_name; //Mã định danh merchant kết nối (Terminal Id)
        $vnp_HashSecret = $paymentConfig->secret_key; //Secret key
        $vnp_Url        = $paymentConfig->checkout_url;
        // $vnp_Returnurl = "http://localhost/payment-vnpay/return-url";
        $vnp_Returnurl = $paymentConfig->return_url;
        $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
        $apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";

        $inputData = array();
        $returnData = array();
        
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
        $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
        $vnp_Amount = $inputData['vnp_Amount']/100; // Số tiền thanh toán VNPAY phản hồi
        
        $Status = 0; // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo 
        $orderId = $inputData['vnp_TxnRef'];
        
        try {
            //Check Orderid    
            //Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash) {
                $order = ServiceBill::findOne(['code' => $orderId]);
                if (!empty($order)) {
                    $totalPrice = $order->total_price ?? 0;
                    $status = $order->status ?? 0;
                    if( $totalPrice == $vnp_Amount) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền 
                    {
                        if (0 == $status) {
                            if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
                                $result = ServiceBill::STATUS_PAID; // Trạng thái thanh toán thành công
                            }else{
                                $result = ServiceBill::STATUS_UNPAID; // Trạng thái thanh toán thất bại
                            }
                            $order->status = $result ;
                            $order->save();
                            $transaction->commit();
                            // Trạng thái thanh toán thất bại / lỗi
                            //Cài đặt Code cập nhật kết quả thanh toán, tình trạng đơn hàng vào DB
                            //
                            //
                            //
                            //Trả kết quả về cho VNPAY: Website/APP TMĐT ghi nhận yêu cầu thành công                
                            $returnData['RspCode'] = '00';
                            $returnData['Message'] = 'Confirm Success';
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                        }
                    }
                    else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }
        $transaction->rollBack();
        //Trả lại VNPAY theo định dạng JSON
        echo json_encode($returnData);
    }
    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $charactersLength = strlen($characters);
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        // $ServiceBill = new ServiceBill();
        // $randomString = $ServiceBill->generateCodeVnpay($randomString);
        return $randomString;
    }

    public function createBillVnpay(){
        $transaction = Yii::$app->db->beginTransaction();

        $code                = Yii::$app->request->post('code') ?? "";
        $building_cluster_id = Yii::$app->request->post('building_cluster_id') ?? 1;
        $total_price         = Yii::$app->request->post('total_price') ?? 0;

        $ServiceBill = new ServiceBill();
        $ServiceBill->load(CUtils::arrLoad($this->attributes), '');
        $ServiceBill->status = ServiceBill::STATUS_UNPAID;
        $ServiceBill->building_cluster_id = $building_cluster_id;
        $ServiceBill->total_price = $total_price;
        $ServiceBill->code = $code;
        // $ServiceBill->generateCodeVnpay($code ?? "");
        if (!$ServiceBill->save()) {
            Yii::error($ServiceBill->getErrors());
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceBill->getErrors()
            ];
        }
        $transaction->commit();
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Success")  
        ];
    }
}
