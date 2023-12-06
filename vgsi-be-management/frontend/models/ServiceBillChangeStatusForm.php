<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\ServiceBill;
use common\models\ServiceBillChagneStatusItem;
use common\models\ServiceBillItem;
use common\models\ServiceMapManagement;
use common\models\ServiceBillChagneStatus;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBillChangeStatusForm")
 * )
 */
class ServiceBillChangeStatusForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="status: -1 - nháp, 0 - Chưa thanh toán, 1 - Đã thanh toán, 2- Đã hủy, 10 - Chốt sổ | bắt buộc khi đổi trạng thái, hủy ko cần truyền", default=0, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="type payment: 0 - tiền mặt, 1 - chuyển khoản | không bắt buộc", default=0, type="integer")
     * @var integer
     */
    public $type_payment;

    /**
     * @SWG\Property(description="note: lý do đổi trạng thái hoạc hủy", default="", type="string")
     * @var string
     */
    public $note;

    public $ids;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'status', 'type_payment'], 'integer'],
            [['note'], 'string'],
            ['status', 'in', 'range' => [ServiceBill::STATUS_UNPAID, ServiceBill::STATUS_PAID, ServiceBill::STATUS_CANCEL, ServiceBill::STATUS_BLOCK]],
            [['ids'], 'safe']
        ];
    }

    public function changeStatus()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
//            if(in_array($this->status, [ServiceBill::STATUS_CANCEL, ServiceBill::STATUS_BLOCK])){
//                $transaction->rollBack();
//                return [
//                    'success' => false,
//                    'message' => Yii::t('frontend', "Status incorrect"),
//                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                ];
//            }
            $user = Yii::$app->user->getIdentity();
            $ServiceBill = ServiceBillResponse::findOne(['id' => (int)$this->id, 'building_cluster_id' => $user->building_cluster_id, 'is_deleted' => ServiceBill::NOT_DELETED]);
            if ($ServiceBill) {
                $status_old = $ServiceBill->status;
                $ServiceBill->load(CUtils::arrLoad($this->attributes), '');
                $status_new = $ServiceBill->status;
                if($status_old == $status_new){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                if(
                ($status_old == ServiceBill::STATUS_DRAFT && !in_array($this->status, [ServiceBill::STATUS_UNPAID]))
                || ($status_old == ServiceBill::STATUS_UNPAID && !in_array($this->status, [ServiceBill::STATUS_PAID]))
                || ($status_old == ServiceBill::STATUS_PAID && !in_array($this->status, [ServiceBill::STATUS_BLOCK]))
                || ($status_old == ServiceBill::STATUS_BLOCK && !in_array($this->status, [ServiceBill::STATUS_PAID]))
                || ($status_old == ServiceBill::STATUS_CANCEL)
                || ($this->status == ServiceBill::STATUS_CANCEL)
                ){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Status incorrect"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }

                if(empty($ServiceBill->management_user_id)){
                    $ServiceBill->management_user_id = $user->id;
                }
                if (!$ServiceBill->save()) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $ServiceBill->getErrors()
                    ];
                }
                $serviceBillItems = ServiceBillItem::find()->where(['service_bill_id' => $ServiceBill->id])->all();
                $arrIds = [];
                foreach ($serviceBillItems as $serviceBillItem){
                    $arrIds[] = $serviceBillItem->service_payment_fee_id;
                }
                if(!empty($arrIds) && $this->status == ServiceBill::STATUS_PAID){
                    $servicePaymentFees = ServicePaymentFee::find()->where(['id' => $arrIds, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])->all();
                    foreach ($servicePaymentFees as $servicePaymentFee){
                        if($servicePaymentFee->price == $servicePaymentFee->money_collected && $servicePaymentFee->status == ServicePaymentFee::STATUS_UNPAID){
                            $servicePaymentFee->status = ServicePaymentFee::STATUS_PAID;
                            if(!$servicePaymentFee->save()){
                                Yii::error($servicePaymentFee->errors);
                                $transaction->rollBack();
                                return [
                                    'success' => false,
                                    'message' => Yii::t('frontend', "Invalid data"),
                                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                                ];
                            }
                        }
                    }
                    if(!$ServiceBill->apartment->updateCurrentDebt()){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                        ];
                    };
                }else if(!empty($arrIds) && $this->status == ServiceBill::STATUS_UNPAID){
                    ServicePaymentFee::updateAll(['status' => ServicePaymentFee::STATUS_UNPAID], ['id' => $arrIds]);
                    if(!$ServiceBill->apartment->updateCurrentDebt()){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                        ];
                    };
                }
                $transaction->commit();
                return [
                    'success' => true,
                    'message' => Yii::t('frontend', "Change Status Success"),
                ];
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

    public function block()
    {
        if(empty($this->ids) || !is_array($this->ids)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $checkBill = ServiceBillResponse::find()->where(['id' => $this->ids, 'building_cluster_id' => $buildingCluster->id, 'is_deleted' => ServiceBill::NOT_DELETED])
            ->andWhere(['<>', 'status', ServiceBill::STATUS_PAID])->one();
            if(!empty($checkBill)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Incorrect status"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            ServiceBill::updateAll(['status' => ServiceBill::STATUS_BLOCK],['id' => $this->ids, 'building_cluster_id' => $buildingCluster->id, 'is_deleted' => ServiceBill::NOT_DELETED, 'status' => ServiceBill::STATUS_PAID]);
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Change Status Success"),
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

    public function cancel()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $ServiceBill = ServiceBillResponse::findOne(['id' => (int)$this->id, 'building_cluster_id' => $buildingCluster->id, 'is_deleted' => ServiceBill::NOT_DELETED]);
            if ($ServiceBill) {
                if(!in_array($ServiceBill->status, [ServiceBill::STATUS_DRAFT, ServiceBill::STATUS_UNPAID, ServiceBill::STATUS_PAID])){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Status incorrect"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                if(!empty($this->note)){
                    $ServiceBill->note = $this->note;
                }

                //check phi tồn tại trong phiếu chi thì ko được hủy
                $itemFeeIds = [];
                $billItems = ServiceBillItem::find()->where(['service_bill_id' => $ServiceBill->id])->groupBy(['service_payment_fee_id'])->all();
                foreach ($billItems as $billItem){
                    $itemFeeIds[$billItem->service_payment_fee_id] = $billItem->service_payment_fee_id;
                }
                $billIds = [];
                $billItemNews = ServiceBillItem::find()->where(['service_payment_fee_id' => $itemFeeIds])
                    ->andWhere(['<>', 'service_bill_id', $ServiceBill->id])->groupBy(['service_bill_id'])->all();
                foreach ($billItemNews as $billItemNew){
                    $billIds[$billItemNew->service_bill_id] = $billItemNew->service_bill_id;
                }
                $billHuy = ServiceBill::findOne(['id' => $billIds, 'type' => ServiceBill::TYPE_1, 'status' => [ServiceBill::STATUS_PAID, ServiceBill::STATUS_BLOCK]]);
                if(!empty($billHuy)){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Phiếu thu này đang chứa phí có trong phiếu chi, không được hủy"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }

                $ServiceBill->status = ServiceBill::STATUS_CANCEL;
                if (!$ServiceBill->save()) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $ServiceBill->getErrors()
                    ];
                }

                if(!$ServiceBill->resetPaymentFees(false)){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "System busy"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }

                if(!empty($ServiceBill->payment_gen_code_id)){
                    $paymentGenCode = PaymentGenCode::findOne(['id' => $ServiceBill->payment_gen_code_id]);
                    if(!empty($paymentGenCode)){
                        $paymentGenCode->status = PaymentGenCode::STATUS_UNPAID;
                        if(!$paymentGenCode->save()){
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
                    'message' => Yii::t('frontend', "Cancel Success"),
                ];
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

}
