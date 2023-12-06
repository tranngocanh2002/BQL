<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentGenCodeForm")
 * )
 */
class PaymentGenCodeForm extends Model
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
     * @SWG\Property(description="description: mo ta", default="", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="image: hinh anh", default="", type="string")
     * @var string
     */
    public $image;

    /**
     * @SWG\Property(description="reason: lý do", default="", type="string")
     * @var string
     */
    public $reason;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'apartment_id', 'service_payment_fee_ids'], 'required'],
            [['type', 'apartment_id'], 'integer'],
            [['code', 'image', 'description', 'reason'], 'string'],
            [['service_payment_fee_ids'], 'safe'],
        ];
    }

    public function gen()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!is_array($this->service_payment_fee_ids)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }


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

            //check phi âm => check = 0
            $servicePaymentFeeCheckAm = ServicePaymentFee::find()
                ->where(['building_cluster_id' => $apartmentMapResidentUser->building_cluster_id, 'apartment_id' => $this->apartment_id, 'id' => $this->service_payment_fee_ids])
//                ->andWhere(['<=', 'more_money_collecte', 0])
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
            $payment_code = $this->code ?? "";
            $paymentGenCode = new PaymentGenCode();
            $paymentGenCode->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
            $paymentGenCode->description = $this->description;
            $paymentGenCode->image = $this->image;
            $paymentGenCode->apartment_id = $this->apartment_id;
            $paymentGenCode->type = $this->type;
            $paymentGenCode->resident_user_id = $user->id;
            $paymentGenCode->generateCodeVnpay($this->type,$payment_code);
            if(!empty($payment_code))
            {
                $paymentGenCode->status = PaymentGenCode::STATUS_PAID;
            }else{
                $paymentGenCode->status = PaymentGenCode::STATUS_UNPAID;
            }
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

            //check phi nằm trong yêu cầu thanh toán khác
            $paymentGenCodes = PaymentGenCode::find()->where(['apartment_id' => $this->apartment_id, 'status' => PaymentGenCode::STATUS_UNPAID])->all();
            $id_fee_not_ins = [];
            foreach ($paymentGenCodes as $paymentGenCode){
                $paymentGenCodeItems = PaymentGenCodeItem::find()->where(['payment_gen_code_id' => $paymentGenCode->id, 'status' => PaymentGenCodeItem::STATUS_UNPAID])->all();
                foreach ($paymentGenCodeItems as $paymentGenCodeItem){
                    $id_fee_not_ins[] = $paymentGenCodeItem->service_payment_fee_id;
                }
            }

            $servicePaymentFees = ServicePaymentFee::find()->where(['building_cluster_id' => $apartmentMapResidentUser->building_cluster_id, 'apartment_id' => $this->apartment_id, 'id' => $this->service_payment_fee_ids, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])
                ->andWhere(['<>', 'more_money_collecte', 0])
                ->andWhere(['not', ['id' => $id_fee_not_ins]])
                ->all();
            if(empty($servicePaymentFees)){
                Yii::error('Không còn phí thanh toán');
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Không còn phí để tạo thanh toán"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $paymentGenCode->getErrors()
                ];
            }
            foreach ($servicePaymentFees as $servicePaymentFee){
                $check = PaymentGenCodeItem::findOne(['service_payment_fee_id' => $servicePaymentFee->id, 'status' => [PaymentGenCodeItem::STATUS_UNPAID, PaymentGenCodeItem::STATUS_PAID]]);
                if(!empty($check)){
                    continue;
                }
                $paymentGenCodeItem = new PaymentGenCodeItem();
                $paymentGenCodeItem->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
                $paymentGenCodeItem->payment_gen_code_id = $paymentGenCode->id;
                $paymentGenCodeItem->service_payment_fee_id = $servicePaymentFee->id;
                $paymentGenCodeItem->amount = $servicePaymentFee->more_money_collecte;
                if(!empty($payment_code))
                {
                    $paymentGenCodeItem->status = PaymentGenCodeItem::STATUS_PAID;
                }else{
                    $paymentGenCodeItem->status = PaymentGenCodeItem::STATUS_UNPAID;
                }
                if(!$paymentGenCodeItem->save()){
                    Yii::error($paymentGenCodeItem->errors);
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }
            $codeNotifyManager = Yii::$app->request->post('code') ?? $paymentGenCode->code;
            $paymentGenCode->sendNotifyToManagementUser($user, $codeNotifyManager ,$this->type);
            $transaction->commit();
            return [
                // 'payment_code' => $paymentGenCode->code
                'payment_code' => $payment_code
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

    public function del()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(empty($this->code) || empty($this->apartment_id)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $user = Yii::$app->user->getIdentity();
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone]);
            if (empty($apartmentMapResidentUser)) {
                Yii::error('Không đúng căn hộ');
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $paymentGenCode = PaymentGenCode::findOne(['apartment_id' => $this->apartment_id, 'code' => $this->code, 'status' => PaymentGenCode::STATUS_UNPAID]);
            if(empty($paymentGenCode)){
                $transaction->rollBack();
                Yii::error('Không tồn tại code');
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if($paymentGenCode->is_auto === PaymentGenCode::IS_AUTO){
                if($apartmentMapResidentUser->type !== ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD){
                    $transaction->rollBack();
                    Yii::error('Không phải tài khoản chủ hộ');
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }
//            else{
//                if($paymentGenCode->resident_user_id !== $user->id){
//                    $transaction->rollBack();
//                    Yii::error('Không đúng tài khoản tạo code');
//                    return [
//                        'success' => false,
//                        'message' => Yii::t('resident', "Invalid data"),
//                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                    ];
//                }
//            }


            $payment_fee_ids = [];
            $paymentGenCodeItemUnpaids = PaymentGenCodeItem::find()->where(['status' => PaymentGenCodeItem::STATUS_UNPAID,'payment_gen_code_id' => $paymentGenCode->id])->all();
            $arr_or = ['or'];
            foreach ($paymentGenCodeItemUnpaids as $paymentGenCodeItemUnpaid){
                $payment_fee_ids[] = $paymentGenCodeItemUnpaid->service_payment_fee_id;
                $arr_or[] = ['like', 'service_payment_fee_ids_text_search', ','.$paymentGenCodeItemUnpaid->service_payment_fee_id.','];
            }
            PaymentGenCodeItem::updateAll(['status' => PaymentGenCodeItem::STATUS_CANCEL], ['status' => PaymentGenCodeItem::STATUS_UNPAID,'payment_gen_code_id' => $paymentGenCode->id]);
//            3 - book đã hủy (hệ thống hủy hoặc cư dân hủy), sau đó mới hủy yêu cầu thanh toán (chỉ yêu cầu thanh toán cư dân tạo và hệ thống tạo)
            $serviceUtilityBookings = ServiceUtilityBooking::find()->where(['service_payment_fee_id' => $payment_fee_ids, 'status' => [ServiceUtilityBooking::STATUS_CANCEL, ServiceUtilityBooking::STATUS_CANCEL_SYSTEM]])
                ->andWhere($arr_or)->all();
            $payment_fee_new_ids = [];
            foreach ($serviceUtilityBookings as $serviceUtilityBooking){
                $payment_fee_new_ids = array_merge($payment_fee_new_ids, $serviceUtilityBooking->getPaymentIds());
            }
            ServicePaymentFee::updateAll(['status' => ServicePaymentFee::STATUS_CANCEL, 'is_draft' => ServicePaymentFee::IS_DRAFT], ['id' => $payment_fee_new_ids, 'money_collected' => 0]);

            $itemCode = PaymentGenCodeItem::findOne(['status' => PaymentGenCodeItem::STATUS_PAID, 'payment_gen_code_id' => $paymentGenCode->id]);
            if(empty($itemCode)){
                $paymentGenCode->status = PaymentGenCode::STATUS_CANCEL;
                if(!empty($this->reason)){
                    $paymentGenCode->reason = $this->reason;
                }
                if(!$paymentGenCode->save()){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', "Delete PaymentGenCode Error"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }else{
                $paymentGenCode->status = PaymentGenCode::STATUS_PAID;
                if(!$paymentGenCode->save()){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('resident', "Update PaymentGenCode Error"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }

            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('resident', "Delete Code Success"),
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
