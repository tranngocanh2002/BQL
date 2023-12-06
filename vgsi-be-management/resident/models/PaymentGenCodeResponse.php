<?php

namespace resident\models;

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
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="description", type="string", description="mo ta"),
     * @SWG\Property(property="image", type="string", description="hinh anh"),
     * @SWG\Property(property="reason", type="string", description="lý do"),
     * @SWG\Property(property="status", type="integer", description="-1: cư dân hủy yêu cầu, 0: chờ xác nhận, 1: đã hoàn thành, 2: bị từ chối"),
     * @SWG\Property(property="is_auto", type="integer", description="0- tạo từ app, 1- tạo tự động"),
     * @SWG\Property(property="items", type="array",
     *      @SWG\Items(type="object", ref="#/definitions/PaymentGenCodeItemResponse")
     * ),
     */
    public function fields()
    {
        return [
            'id',
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
            'code',
            'description',
            'image',
            'reason',
            'status',
            'is_auto',
            'items' => function($model){
                return PaymentGenCodeItemResponse::find()->where(['payment_gen_code_id' => $model->id])->all();
            },
        ];
    }
}
