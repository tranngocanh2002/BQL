<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceBill;
use common\models\ServiceBillItem;
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

    public $detail = false;
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="management_user_name", type="string"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="payer_name", type="string"),
     * @SWG\Property(property="management_user_id", type="integer"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="type_payment", type="integer", description="0 - Tiền mặt, 1 - chuyển khoản, 2 - Momo, 3 - Payoo"),
     * @SWG\Property(property="status", type="integer", description="0 - Chưa thanh toán, 1 - Đã thanh toán"),
     * @SWG\Property(property="type", type="integer", description="0 - Phiếu thu, 1 - Phiếu chi"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="total_price", type="integer"),
     * @SWG\Property(property="number", type="string", description="Số phiếu thu"),
     * @SWG\Property(property="payment_date", type="integer"),
     * @SWG\Property(property="execution_date", type="integer"),
     * @SWG\Property(property="note", type="string"),
     * @SWG\Property(property="service_bill_items", type="array",
     *      @SWG\Items(type="object", ref="#/definitions/ServiceBillItemResponse"),
     * ),
     * @SWG\Property(property="bank_name", type="string"),
     * @SWG\Property(property="bank_account", type="string"),
     * @SWG\Property(property="bank_holders", type="string"),
     * @SWG\Property(property="service_provider", type="object", ref="#/definitions/ServiceProviderResponse"),
     */
    public function fields()
    {
//        if($this->detail){
            return [
                'id',
                'code',
                'apartment_id',
                'apartment_name' => function($model){
                    return $model->apartment->name;
                },
                'apartment_parent_path' => function($model){
                    return trim($model->apartment->parent_path, '/');
                },
                'management_user_id',
                'management_user_name',
                'resident_user_id',
                'resident_user_name',
                'payer_name',
                'type_payment',
                'total_price',
                'status',
                'service_bill_items' => function($model){
                    /**
                     * @var $model ServiceBill
                     */
                    $items = [];
                    if(count($model->serviceBillItems)>0){
                        foreach ($model->serviceBillItems as $billItem){
                            $billItemResponse = new ServiceBillItemResponse();
                            $billItemResponse->load($billItem->toArray(), '');
                            $billItemResponse->id = $billItem->id;
                            $items[] = $billItemResponse;
                        }
                    }
                    return $items;
                },
                'service_provider' => function($model){
                    /**
                     * @var $model ServiceBill
                     */
                    if($model->serviceProvider){
                        $service_provider_info = new ServiceProviderResponse();
                        $service_provider_info->load($model->serviceProvider->toArray(), '');
                        return $service_provider_info;
                    }
                    return null;
                },
                'type',
                'number',
                'payment_date',
                'execution_date',
                'note',
                'bank_name',
                'bank_account',
                'bank_holders',
                'created_at',
                'updated_at',
            ];
//        }else{
//            return [
//                'id',
//                'code',
//                'apartment_id',
//                'apartment_name' => function($model){
//                    return $model->apartment->name;
//                },
//                'apartment_parent_path' => function($model){
//                    return trim($model->apartment->parent_path, '/');
//                },
//                'management_user_id',
//                'management_user_name',
//                'resident_user_id',
//                'resident_user_name',
//                'total_price',
//                'payer_name',
//                'type_payment',
//                'status',
//                'type',
//                'bank_name',
//                'bank_account',
//                'bank_holders',
//                'created_at',
//                'updated_at',
//            ];
//        }
    }
}
