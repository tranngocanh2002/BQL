<?php

namespace frontend\models;

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
     * @SWG\Property(description="reason", default="", type="string", description="lý do")
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
            [['service_payment_fee_ids', 'code'], 'safe'],
            [['reason'], 'string'],
        ];
    }

    public function del()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(empty($this->code) || empty($this->apartment_id)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id , 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if (empty($apartmentMapResidentUser)) {
                Yii::error('Không đúng căn hộ');
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Apartment Map Resident User Empty"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $paymentGenCode = PaymentGenCode::findOne(['apartment_id' => $this->apartment_id, 'code' => $this->code, 'status' => PaymentGenCode::STATUS_UNPAID]);
            if(empty($paymentGenCode)){
                $transaction->rollBack();
                Yii::error('Không tồn tại code');
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Payment Gen Code Empty"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

//            if($paymentGenCode->is_auto !== PaymentGenCode::IS_AUTO){
//                    $transaction->rollBack();
//                    Yii::error('Không phải tạo tự động , không được xóa');
//                    return [
//                        'success' => false,
//                        'message' => Yii::t('frontend', "Not is auto create, not delete"),
//                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                    ];
//            }
            $payment_fee_ids = [];
            $paymentGenCodeItemUnpaids = PaymentGenCodeItem::find()->where(['status' => PaymentGenCodeItem::STATUS_UNPAID,'payment_gen_code_id' => $paymentGenCode->id])->all();
            $arr_or = ['or'];
            foreach ($paymentGenCodeItemUnpaids as $paymentGenCodeItemUnpaid){
                $payment_fee_ids[] = $paymentGenCodeItemUnpaid->service_payment_fee_id;
                $arr_or[] = ['like', 'service_payment_fee_ids_text_search', ','.$paymentGenCodeItemUnpaid->service_payment_fee_id.','];
            }
            PaymentGenCodeItem::updateAll(['status' => PaymentGenCodeItem::STATUS_CANCEL], ['status' => PaymentGenCodeItem::STATUS_UNPAID,'payment_gen_code_id' => $paymentGenCode->id]);

            //3 - book đã hủy (hệ thống hủy hoặc cư dân hủy), sau đó mới hủy yêu cầu thanh toán (chỉ yêu cầu thanh toán cư dân tạo và hệ thống tạo)
            $serviceUtilityBookings = ServiceUtilityBooking::find()->where(['service_payment_fee_id' => $payment_fee_ids, 'status' => [ServiceUtilityBooking::STATUS_CANCEL, ServiceUtilityBooking::STATUS_CANCEL_SYSTEM]])
                ->andWhere($arr_or)->all();
            $payment_fee_new_ids = [];
            foreach ($serviceUtilityBookings as $serviceUtilityBooking){
                $payment_fee_new_ids = array_merge($payment_fee_new_ids, $serviceUtilityBooking->getPaymentIds());
            }
            ServicePaymentFee::updateAll(['status' => ServicePaymentFee::STATUS_CANCEL, 'is_draft' => ServicePaymentFee::IS_DRAFT], ['id' => $payment_fee_new_ids, 'money_collected' => 0]);
            

            $itemCode = PaymentGenCodeItem::findOne(['status' => PaymentGenCodeItem::STATUS_PAID, 'payment_gen_code_id' => $paymentGenCode->id]);
            if(empty($itemCode)){
                $paymentGenCode->status = PaymentGenCode::STATUS_REJECT;
                if(!empty($this->reason)){
                    $paymentGenCode->reason = $this->reason;
                }
                if(!$paymentGenCode->save()){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Delete PaymentGenCode Error"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }else{
                $paymentGenCode->status = PaymentGenCode::STATUS_PAID;
                if(!$paymentGenCode->save()){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Update PaymentGenCode Error"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }
            $transaction->commit();
            $paymentGenCode->sendNotifyToResidentUser(PaymentGenCode::REJECT,$this->apartment_id);
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Code Success"),
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
