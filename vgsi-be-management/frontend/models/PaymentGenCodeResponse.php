<?php

namespace frontend\models;

use common\models\ApartmentMapResidentUser;
use common\models\PaymentGenCode;
use pay\models\ServicePaymentFeeResponse;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentGenCodeResponse")
 * )
 */
class PaymentGenCodeResponse extends PaymentGenCode
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="head_household_name", type="string", description="Tên chủ hộ"),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="image", type="string"),
     * @SWG\Property(property="reason", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="type", type="integer", description="0- chuyển khoản, 1- thanh toán online"),
     * @SWG\Property(property="status", type="integer", description="-1: cư dân hủy yêu cầu, 0: chờ xác nhận, 1: đã hoàn thành, 2: bị từ chối"),
     * @SWG\Property(property="is_auto", type="integer", description="0- tạo từ app, 1- tạo tự động"),
     * @SWG\Property(property="items", type="array",
     *      @SWG\Items(type="object", ref="#/definitions/PaymentGenCodeItemResponse")
     * ),
     * @SWG\Property(property="service_payment_fees", type="array",
     *      @SWG\Items(type="object", ref="#/definitions/ServicePaymentFeeResponse")
     * ),
     * @SWG\Property(property="created_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'resident_user_id' => function($model){
                if(!$model->resident_user_id){
                    if(!empty($model->apartment)){
                        return $model->apartment->resident_user_id;
                    }
                }
                return $model->resident_user_id;
            },
            'resident_user_name' => function($model){
                if($model->residentUser){
                    $residentUser = ApartmentMapResidentUser::findOne([
                        'building_cluster_id' => $model->building_cluster_id,
                        'apartment_id' => $model->apartment_id,
                        'resident_user_phone' => $model->residentUser->phone,
                        'is_deleted' => ApartmentMapResidentUser::NOT_DELETED
                    ]);
                    if($residentUser){
                        return trim($residentUser->resident_user_first_name . ' ' . $residentUser->resident_user_last_name);
                    }
                }else if(!empty($model->apartment)){
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_id',
            'apartment_name' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_parent_path' => function($model){
                if(!empty($model->apartment)){
                    return trim($model->apartment->parent_path,'/');
                }
                return '';
            },
            'head_household_name' => function($model){
                if(!empty($model->apartment)){
                    return $model->apartment->resident_user_name;
                }
                return '';
            },
            'code',
            'image',
            'reason',
            'description',
            'type',
            'status',
            'is_auto',
            'items' => function($model){
                return PaymentGenCodeItemResponse::find()->where(['payment_gen_code_id' => $model->id])->all();
            },
            'service_payment_fees' => function($model){
                if($model->paymentGenCodeItems){
                    $service_payment_fee_ids = [];
                    foreach ($model->paymentGenCodeItems as $item){
                        $service_payment_fee_ids[] = $item->service_payment_fee_id;
                    }
                    return ServicePaymentFeeResponse::find()->where(['id' => $service_payment_fee_ids])->all();
                }
                return null;
            },
            'created_at'
        ];
    }
}
