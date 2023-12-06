<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceBillItem;
use common\models\ServiceBill;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBillInvoiceForm")
 * )
 */
class ServiceBillInvoiceForm extends Model
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
     * @SWG\Property(description="service_payment_fee_id : phí sẽ chi - phí ở trạng thái đã thanh toán", type="integer")
     * @var integer
     */
    public $service_payment_fee_id;

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
     * @SWG\Property(description="bank_name: Tên ngân hàng nhận phí", type="string")
     * @var string
     */
    public $bank_name;

    /**
     * @SWG\Property(description="bank_account: Tài khoản nhận phí", type="string")
     * @var string
     */
    public $bank_account;

    /**
     * @SWG\Property(description="bank_holders: Chủ tài khoản nhận phí", type="string")
     * @var string
     */
    public $bank_holders;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['apartment_id', 'service_payment_fee_id'], 'required', "on" => ['create']],
            [['id','apartment_id', 'type_payment', 'payment_date', 'execution_date', 'service_payment_fee_id'], 'integer'],
            [['payer_name', 'management_user_name', 'description', 'bank_name', 'bank_account', 'bank_holders'], 'string'],
        ];
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

            $ServiceBill = new ServiceBill();
            $ServiceBill->load(CUtils::arrLoad($this->attributes), '');
            $ServiceBill->building_cluster_id = $apartment->building_cluster_id;
            $ServiceBill->building_area_id = $apartment->building_area_id;
            $ServiceBill->status = ServiceBill::STATUS_PAID; // Tạo từ web thì mặc định là đã thanh toán
            $ServiceBill->type = ServiceBill::TYPE_1;
            $ServiceBill->management_user_id = $user->id;
            $ServiceBill->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
            $ServiceBill->resident_user_name = $apartmentMapResidentUser->resident_user_first_name;
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
                $servicePaymentFee = ServicePaymentFee::findOne(['status' => ServicePaymentFee::STATUS_PAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'id' => $this->service_payment_fee_id]);
                if(empty($servicePaymentFee) ||!empty($servicePaymentFee->service_bill_invoice_id)){
                    Yii::error('servicePaymentFee null');
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $ServiceBill->getErrors()
                    ];
                }

                $serviceBillItem = new ServiceBillItem();
                $serviceBillItem->service_bill_id = $ServiceBill->id;
                $serviceBillItem->service_payment_fee_id = $servicePaymentFee->id;
                $serviceBillItem->service_map_management_id = $servicePaymentFee->service_map_management_id;
                $serviceBillItem->description = $servicePaymentFee->description;
                $serviceBillItem->price = $servicePaymentFee->price;
                $serviceBillItem->fee_of_month = $servicePaymentFee->fee_of_month;
                if (!$serviceBillItem->save()) {
                    Yii::error($serviceBillItem->getErrors());
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $serviceBillItem->getErrors()
                    ];
                }

                $servicePaymentFee->service_bill_invoice_id = $ServiceBill->id;
                $ServiceBill->total_price = $servicePaymentFee->price;
                if(!$servicePaymentFee->save() || !$ServiceBill->save()){
                    Yii::error($servicePaymentFee->getErrors());
                    Yii::error($ServiceBill->getErrors());
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $servicePaymentFee->getErrors()
                    ];
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
                    $serviceBillItem = ServiceBillItem::findOne(['service_bill_id' => $ServiceBill->id]);
                    if(!empty($serviceBillItem)){
                        ServicePaymentFee::updateAll(['service_bill_invoice_id' => null], ['id' => $serviceBillItem->service_payment_fee_id]);
                        if(!$serviceBillItem->delete()){
                            $transaction->rollBack();
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "Invalid data"),
                                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                            ];
                        }
                    }

                    //thêm các item fee mới
                    $servicePaymentFee = ServicePaymentFee::findOne(['status' => ServicePaymentFee::STATUS_PAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'id' => $this->service_payment_fee_id]);
                    if(empty($servicePaymentFee)){
                        Yii::error('servicePaymentFee null');
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $ServiceBill->getErrors()
                        ];
                    }
                    $serviceBillItem = new ServiceBillItem();
                    $serviceBillItem->service_bill_id = $ServiceBill->id;
                    $serviceBillItem->service_payment_fee_id = $servicePaymentFee->id;
                    $serviceBillItem->service_map_management_id = $servicePaymentFee->service_map_management_id;
                    $serviceBillItem->description = $servicePaymentFee->description;
                    $serviceBillItem->price = $servicePaymentFee->price;
                    $serviceBillItem->fee_of_month = $servicePaymentFee->fee_of_month;
                    if (!$serviceBillItem->save()) {
                        Yii::error($serviceBillItem->getErrors());
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $serviceBillItem->getErrors()
                        ];
                    }

                    $servicePaymentFee->service_bill_invoice_id = $ServiceBill->id;
                    $ServiceBill->total_price = $servicePaymentFee->price;
                    if(!$servicePaymentFee->save() || !$ServiceBill->save()){
                        Yii::error($servicePaymentFee->getErrors());
                        Yii::error($ServiceBill->getErrors());
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $servicePaymentFee->getErrors()
                        ];
                    }

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

            if(in_array($ServiceBill->status, [ServiceBill::STATUS_CANCEL, ServiceBill::STATUS_PAID, ServiceBill::STATUS_BLOCK])){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Status incorrect"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $serviceBillItem = ServiceBillItem::findOne(['service_bill_id' => $ServiceBill->id]);
            if(!empty($serviceBillItem)){
                ServicePaymentFee::updateAll(['service_bill_invoice_id' => null], ['id' => $serviceBillItem->service_payment_fee_id]);
                if(!$serviceBillItem->delete()){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success")
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
