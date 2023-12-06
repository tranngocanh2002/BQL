<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceBillItem;
use common\models\ServiceMapManagement;
use common\models\ServiceBill;
use common\models\ServicePaymentFee;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\BuildingCluster;
use common\models\ManagementUserNotify;
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
     * @SWG\Property(description="status", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="payer name : người thanh toán", default="", type="string")
     * @var string
     */
    public $payer_name;

    /**
     * @SWG\Property(description="service payment fee id : mảng id thêm vào", type="array",
     *     @SWG\Items(type="integer", default=0),
     * ),
     * @var array
     */
    public $service_payment_fee_id;

    /**
     * @SWG\Property(description="service bill item id delete: mảng id bị xóa", type="array",
     *     @SWG\Items(type="integer", default=0),
     * ),
     * @var array
     */
    public $service_bill_item_id_delete;

       /**
     * @SWG\Property(description="payment_gen_code : code yêu cầu thanh toán", type="string")
     * @var string
     */
    public $payment_gen_code;

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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['status'], 'required', "on" => ['update']],
            [['apartment_id'], 'required'],
            [['id', 'apartment_id', 'status'], 'integer'],
            ['status', 'in', 'range' => [ServiceBill::STATUS_DRAFT, ServiceBill::STATUS_UNPAID]],
            [['payer_name'], 'string'],
            [['service_payment_fee_id', 'service_bill_item_id_delete'], 'safe'],
            [['apartment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_id' => 'id']],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if (empty($apartmentMapResidentUser)) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $ServiceBill = new ServiceBillResponse();
            $ServiceBill->detail = true;
            $ServiceBill->load(CUtils::arrLoad($this->attributes), '');
            $ServiceBill->status = ServiceBill::STATUS_DRAFT;
            $ServiceBill->type_payment = $ServiceBill::TYPE_PAYMENT_INTERNET_BANKING;
            $ServiceBill->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
            $ServiceBill->building_area_id = $apartmentMapResidentUser->building_area_id;
            $ServiceBill->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
            $ServiceBill->resident_user_name = $apartmentMapResidentUser->resident_user_first_name;
            $ServiceBill->generateCode();
            //chỉ lấy provider id vào bill
            if (!empty($this->service_payment_fee_id) && is_array($this->service_payment_fee_id)) {
                $servicePaymentFee = ServicePaymentFee::findOne(['id' => $this->service_payment_fee_id]);
                if (!empty($servicePaymentFee)) {
                    if (!empty($servicePaymentFee->serviceMapManagement)) {
                        $ServiceBill->service_provider_id = $servicePaymentFee->serviceMapManagement->service_provider_id;
                    }
                }
            }
            if (!$ServiceBill->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceBill->getErrors()
                ];
            } else {
                $ServiceBill->generateNumber();
                $ServiceBill->save();
                //tạo nháp không xử lý vào item , không tác động fee
//                if (!empty($this->service_payment_fee_id) && is_array($this->service_payment_fee_id)) {
//                    ServicePaymentFee::updateAll(['service_bill_id' => $ServiceBill->id, 'service_bill_code' => $ServiceBill->code], ['id' => $this->service_payment_fee_id, 'status' => ServicePaymentFee::STATUS_UNPAID]);
//                    $total_price = 0;
//                    $servicePaymentFees = ServicePaymentFee::find()->where(['status' => ServicePaymentFee::STATUS_UNPAID, 'id' => $this->service_payment_fee_id])->all();
//                    if (!$ServiceBill->updatePaymentFees($servicePaymentFees)) {
//                        $transaction->rollBack();
//                        return [
//                            'success' => false,
//                            'message' => Yii::t('resident', "Invalid data"),
//                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
//                        ];
//                    }
//                }
                $transaction->commit();
                if($ServiceBill->status == ServiceBill::STATUS_UNPAID){
                    $ServiceBill->sendNotifyToManagementUser();
                }
                return $ServiceBill;
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('resident', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                // 'errors' => $ex->getMessage()
            ];
        }
    }

    public function update()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $ServiceBill = ServiceBillResponse::findOne(['id' => (int)$this->id]);
            $status_old = $ServiceBill->status;
            if ($ServiceBill) {
                $ServiceBill->detail = true;
                $ServiceBill->load(CUtils::arrLoad($this->attributes), '');
                if (!$ServiceBill->save()) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $ServiceBill->getErrors()
                    ];
                } else {
                    if (!empty($this->service_payment_fee_id) && is_array($this->service_payment_fee_id)) {
                        if (!empty($this->service_payment_fee_id) && is_array($this->service_payment_fee_id)) {
                            ServicePaymentFee::updateAll(['service_bill_id' => $ServiceBill->id, 'service_bill_code' => $ServiceBill->code], ['id' => $this->service_payment_fee_id, 'status' => ServicePaymentFee::STATUS_UNPAID]);
                            $servicePaymentFees = ServicePaymentFee::find()->where(['status' => ServicePaymentFee::STATUS_UNPAID, 'id' => $this->service_payment_fee_id, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])->all();
                            if (!$ServiceBill->updatePaymentFees($servicePaymentFees)) {
                                $transaction->rollBack();
                                return [
                                    'success' => false,
                                    'message' => Yii::t('resident', "Invalid data"),
                                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                                ];
                            }
                        }
                    }
                    if (!empty($this->service_bill_item_id_delete) && is_array($this->service_bill_item_id_delete)) {
                        $serviceBillItems = ServiceBillItem::find()->where(['id' => $this->service_bill_item_id_delete])->all();
                        $feeIds = [];
                        foreach ($serviceBillItems as $serviceBillItem) {
                            $feeIds[] = $serviceBillItem->service_payment_fee_id;
                        }
                        ServiceBillItem::deleteAll(['id' => $this->service_bill_item_id_delete]);
                        ServicePaymentFee::updateAll(['service_bill_id' => null, 'service_bill_code' => null], ['id' => $feeIds]);
                    }
                    $transaction->commit();
                    if($status_old == ServiceBill::STATUS_DRAFT && $ServiceBill->status == ServiceBill::STATUS_UNPAID){
                        $ServiceBill->sendNotifyToManagementUser();
                    }
                    return $ServiceBill;
                }
            } else {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('resident', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                // 'errors' => $ex->getMessage()
            ];
        }

    }

    public function delete()
    {
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $user = Yii::$app->user->getIdentity();
        $ServiceBill = ServiceBill::findOne($this->id);
        if ($ServiceBill->building_cluster_id != $user->building_cluster_id || $ServiceBill->status != ServiceBill::STATUS_UNPAID) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $ServiceBill->is_deleted = ServiceBill::DELETED;
        if ($ServiceBill->save()) {
            ServicePaymentFee::updateAll(['service_bill_id' => null, 'service_bill_code' => null], ['service_bill_id' => $ServiceBill->id]);
            return [
                'success' => true,
                'message' => Yii::t('resident', "Delete Success")
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function approvebillvnpay()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $paymentGenCode     = Yii::$app->request->post('payment_gen_code') ?? "";
            $servicePaymentFees = Yii::$app->request->post('service_payment_fees') ?? [];
            $apartmentId        = Yii::$app->request->post('apartment_id') ?? 0;
            $buildingClusterId  = Yii::$app->request->post('building_cluster_id') ?? 1;
            // $user = Yii::$app->user->getIdentity();
            // $buildingCluster = Yii::$app->building->BuildingCluster;
            $buildingCluster = BuildingCluster::findOne(['id' => $buildingClusterId]);
            $apartment = Apartment::findOne(['id' => $apartmentId, 'is_deleted' => Apartment::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
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
            $paymentGenCode = PaymentGenCode::findOne(['code' => $paymentGenCode]);
            // $paymentGenCode = PaymentGenCode::findOne(['code' => 'GDZXEPZN']);
            $payment_gen_code_id = null;
            if(!empty($paymentGenCode)){
                $payment_gen_code_id = $paymentGenCode->id;
            }
            
            // for($index = 0 ; $index = count($servicePaymentFees); $index ++)
            // {
            //     $feeIds[] = $fee['service_payment_fee_id'];
            // }
            foreach ($servicePaymentFees as $fee){
            // foreach ($a as $fee){
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
            // return [
            //     'success' => false,
            //     'message' => Yii::t('frontend', "abc"),
            //     'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            //     'data' => [
            //         'payment_gen_code' => $paymentGenCode,
            //         'service_payment_fees' => $servicePaymentFees,
            //         'apartment_id' => $this->apartment_id,
            //         'apartmentId' => $apartmentId,
            //         'feeIds'    => $feeIds,
            //         'feePriceIds' => $feePriceIds
            //     ]
            // ];
            if(empty($feeIds)){
                Yii::error('feeIds empty');
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $codeItem = Yii::$app->request->post('payment_gen_code') ?? "";
            $ServiceBill = ServiceBill::findOne(['code' => $codeItem]);
            // $ServiceBill = new ServiceBill();
            $ServiceBill->load(CUtils::arrLoad($this->attributes), '');
            $ServiceBill->building_cluster_id = $apartment->building_cluster_id;
            $ServiceBill->building_area_id = $apartment->building_area_id;
            // $ServiceBill->status = ServiceBill::STATUS_PAID; // mặc định là đã thanh toán
            $ServiceBill->status = ServiceBill::STATUS_PAID; // mặc định là chưa thanh toán
            // $ServiceBill->management_user_id = $user->id;
            $ServiceBill->management_user_id = 1;
            $ServiceBill->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
            $ServiceBill->resident_user_name = $apartmentMapResidentUser->resident_user_first_name;
            $ServiceBill->type_payment = ServiceBill::TYPE_PAYMENT_VNPAY;
            $ServiceBill->execution_date = time();
            $ServiceBill->payment_date = time();
            if(!empty($paymentGenCode)){
                $ServiceBill->payment_gen_code_id = $paymentGenCode->id;
            }
            if((!isset($ServiceBill->type) || $ServiceBill->type = ServiceBill::TYPE_0) && ($ServiceBill->type_payment == ServiceBill::TYPE_PAYMENT_INTERNET_BANKING)){
                $ServiceBill->bank_name = $buildingCluster->bank_name;
                $ServiceBill->bank_holders = $buildingCluster->bank_holders;
                $ServiceBill->bank_account = $buildingCluster->bank_account;
            }
            // $ServiceBill->generateCodeVnpay(Yii::$app->request->post('payment_gen_code') ?? "");
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
                $serviceUtilityBookingId = [];
                foreach($servicePaymentFees as $servicePaymentFee)
                {
                    $serviceUtilityBookingId = $servicePaymentFee->service_utility_booking_id;
                }
                ServiceUtilityBooking::updateAll(['status' => PaymentGenCodeItem::STATUS_PAID], ['id' => $serviceUtilityBookingId]);
                if(empty($servicePaymentFees)){
                    Yii::error('Không còn phí để lập phiếu');
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Không còn phí để lập phiếu"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
                $serviceUtilityBookingId = [];
                foreach($servicePaymentFees as $servicePaymentFee)
                {
                    $serviceUtilityBookingId = $servicePaymentFee->service_utility_booking_id;
                }
                ServiceUtilityBooking::updateAll(['status' => PaymentGenCodeItem::STATUS_PAID], ['id' => $serviceUtilityBookingId]);
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
                $managementUserNotify = ManagementUserNotify::find()
                ->where(['code' => Yii::$app->request->post('payment_gen_code')])
                ->all();
            
                foreach ($managementUserNotify as $notification) {
                    $notification->service_bill_id = $ServiceBill->id;
                    $notification->save(); 
                }
                $transaction->commit();
                return ServiceBillResponse::findOne(['id' => $ServiceBill->id]);
            }
            return true;
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
