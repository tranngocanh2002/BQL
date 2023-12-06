<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\ServiceBill;
use common\models\ServiceBillItem;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBillResponse")
 * )
 */
class ServiceBillResponse extends ServiceBill
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="management_user_name", type="string"),
     * @SWG\Property(property="management_user_auth_group", type="string", description="nhóm quyền của nhân viên"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="payer_name", type="string"),
     * @SWG\Property(property="management_user_id", type="integer"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="type_payment", type="integer", description="0 - Tiền mặt, 1 - chuyển khoản, 2 - thanh toán ngân lượng, 3 - cà thẻ, 4 - ví momo"),
     * @SWG\Property(property="type_payment_name", type="string"),
     * @SWG\Property(property="status", type="integer", description="-1 - nháp, 0 - Chưa thanh toán, 1 - Đã thanh toán, 2- Đã hủy, 10 - Chốt sổ"),
     * @SWG\Property(property="status_name", type="string"),
     * @SWG\Property(property="type", type="integer", description="0 - Phiếu thu, 1 - Phiếu chi"),
     * @SWG\Property(property="type_name", type="string", description="0 - Phiếu thu, 1 - Phiếu chi"),
     * @SWG\Property(property="total_price", type="integer"),
     * @SWG\Property(property="number", type="string"),
     * @SWG\Property(property="payment_date", type="integer"),
     * @SWG\Property(property="execution_date", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="updated_name", type="string"),
     * @SWG\Property(property="note", type="string"),
     * @SWG\Property(property="bank_name", type="string", description="Tài khoản ngân hàng nhận tiền phiếu chi"),
     * @SWG\Property(property="bank_account", type="string", description="Tài khoản ngân hàng nhận tiền phiếu chi"),
     * @SWG\Property(property="bank_holders", type="string", description="Tài khoản ngân hàng nhận tiền phiếu chi"),
     * @SWG\Property(property="service_bill_items", type="array",
     *      @SWG\Items(type="object", ref="#/definitions/ServiceBillItemResponse"),
     * ),
     * @SWG\Property(property="service_payment_fees", type="array",
     *      @SWG\Items(type="object",
     *          @SWG\Property(property="id", type="integer"),
     *          @SWG\Property(property="service_map_management_id", type="integer"),
     *          @SWG\Property(property="service_map_management_service_name", type="string"),
     *          @SWG\Property(property="service_map_management_service_name_en", type="string"),
     *          @SWG\Property(property="price", type="integer"),
     *          @SWG\Property(property="money_collected", type="integer"),
     *          @SWG\Property(property="more_money_collecte", type="integer"),
     *          @SWG\Property(property="fee_of_month", type="integer"),
     *          @SWG\Property(property="service_bill_item", type="integer"),
     *      ),
     * ),
     * @SWG\Property(property="service_provider", type="object", ref="#/definitions/ServiceProviderResponse"),
     */
    public function fields()
    {
        return [
            'id',
            'code',
            'apartment_id',
            'apartment_name' => function ($model) {
                if (!empty($model->apartment)) {
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_parent_path' => function ($model) {
                if (!empty($model->apartment)) {
                    return trim($model->apartment->parent_path, '/');
                }
                return '';
            },
            'management_user_id',
            'management_user_name',
            'management_user_auth_group' => function ($model) {
                if ($model->managementUser) {
                    if ($model->managementUser->authGroup) {
                        return $model->managementUser->authGroup->name;
                    }
                }
                return '';
            },
            'resident_user_id',
            'resident_user_name',
            'payer_name',
            'type_payment',
            'type_payment_name' => function ($model) {
                return isset(ServiceBill::$type_payment_lst[$model->type_payment]) ? ServiceBill::$type_payment_lst[$model->type_payment] : "";
            },
            'status',
            'status_name' => function ($model) {
                return isset(ServiceBill::$status_lst[$model->status]) ? ServiceBill::$status_lst[$model->status] : "";
            },
            'type',
            'type_name' => function ($model) {
                return isset(ServiceBill::$type_lst[$model->type]) ? ServiceBill::$type_lst[$model->type] : "";
            },
            'total_price',
            'service_bill_items' => function ($model) {
                return ServiceBillItemResponse::find()->with(['serviceMapManagement'])->where(['service_bill_id' => $model->id])->all();
            },
            'service_payment_fees' => function ($model) {
                $serviceBillItems = ServiceBillItem::find()->with(['servicePaymentFee'])->where(['service_bill_id' => $model->id])->all();
                $res = [];
                foreach ($serviceBillItems as $serviceBillItem) {
                    if (!empty($serviceBillItem->servicePaymentFee)) {
                        $service_map_management_service_name = '';
                        $service_map_management_service_name_en = '';
                        if($serviceBillItem->servicePaymentFee->for_type == ServicePaymentFee::FOR_TYPE_1){
                            $service_map_management_service_name = 'Đặt cọc - ';
                            $service_map_management_service_name_en = 'Deposit - ';
                        }else if($serviceBillItem->servicePaymentFee->for_type == ServicePaymentFee::FOR_TYPE_2){
                            $service_map_management_service_name = 'Phát sinh - ';
                            $service_map_management_service_name_en = 'Incurred - ';
                        }
                        if (!empty($serviceBillItem->servicePaymentFee->serviceMapManagement)) {
                            $service_map_management_service_name .= $serviceBillItem->servicePaymentFee->serviceMapManagement->service_name;
                            $service_map_management_service_name_en .= $serviceBillItem->servicePaymentFee->serviceMapManagement->service_name_en;
                            //nếu phí từ book sẽ lấy thêm thông tin tiện ích
                            $booking = ServiceUtilityBooking::find()->where(['like', 'service_payment_fee_ids_text_search', ','.$serviceBillItem->servicePaymentFee->id.','])->one();
                            if(!empty($booking)){
                                if(!empty($booking->serviceUtilityFree)){
                                    $service_map_management_service_name .= ' - ' .$booking->serviceUtilityFree->name;
                                    $service_map_management_service_name_en .= ' - ' .$booking->serviceUtilityFree->name_en;
                                }
                            }
                        }
                        $res[] = [
                            'id' => $serviceBillItem->servicePaymentFee->id,
                            'service_map_management_id' => $serviceBillItem->servicePaymentFee->service_map_management_id,
                            'service_map_management_service_name' => $service_map_management_service_name,
                            'service_map_management_service_name_en' => $service_map_management_service_name_en,
                            'price' => $serviceBillItem->servicePaymentFee->price,
                            'money_collected' => $serviceBillItem->servicePaymentFee->money_collected,
                            'more_money_collecte' => $serviceBillItem->servicePaymentFee->more_money_collecte,
                            'fee_of_month' => $serviceBillItem->servicePaymentFee->fee_of_month,
                            'service_bill_item' => $serviceBillItem->price,
                            'for_type' => $serviceBillItem->servicePaymentFee->for_type,
                        ];
                    }
                }
                return $res;
            },
            'service_provider' => function ($model) {
                $serviceBillItem = ServiceBillItem::findOne(['service_bill_id' => $model->id]);
                $serviceProvider = null;
                if (!empty($serviceBillItem)) {
                    $serviceMapManagement = $serviceBillItem->serviceMapManagement;
                    if (!empty($serviceMapManagement)) {
                        $serviceProvider = ServiceProviderResponse::findOne(['id' => $serviceMapManagement->service_provider_id]);
                    }
                }
                return $serviceProvider;
            },
            'number',
            'payment_date',
            'execution_date',
            'note',
            'bank_name',
            'bank_account',
            'bank_holders',
            'created_at',
            'updated_at',
            'updated_name' => function ($model) {
                $managementUser = ManagementUser::findOne(['id' => $model->updated_by]);
                if (!empty($managementUser)) {
                    return $managementUser->first_name;
                }
                return '';
            },
        ];
    }
}
