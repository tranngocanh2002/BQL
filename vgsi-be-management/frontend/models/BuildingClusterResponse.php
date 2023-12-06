<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\BuildingCluster;
use common\models\City;
use common\models\rbac\AuthGroupResponse;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="BuildingClusterResponse")
 * )
 */
class BuildingClusterResponse extends BuildingCluster
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="domain", type="string"),
     * @SWG\Property(property="email", type="string"),
     * @SWG\Property(property="hotline", type="string"),
     * @SWG\Property(property="address", type="string"),
     * @SWG\Property(property="one_signal_app_id", type="string"),
     * @SWG\Property(property="bank_account", type="string"),
     * @SWG\Property(property="bank_name", type="string"),
     * @SWG\Property(property="bank_holders", type="string"),
     * @SWG\Property(property="cash_instruction", type="string", description="Thông tin thanh toán chuyển khoản"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="tax_code", type="string"),
     * @SWG\Property(property="tax_info", type="string"),
     * @SWG\Property(property="city_id", type="integer"),
     * @SWG\Property(property="city_name", type="string"),
     * @SWG\Property(property="setting_group_receives_notices_financial", type="array",
     *      @SWG\Items(type="object", ref="#/definitions/AuthGroupResponse"),
     * ),
     * @SWG\Property(property="payment_config", type="object", ref="#/definitions/PaymentConfigResponse"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="limit_sms", type="integer", description="giới hạn gửi tin trong 1 tháng"),
     * @SWG\Property(property="limit_email", type="integer", description="giới hạn gửi email trong 1 tháng"),
     * @SWG\Property(property="limit_notify", type="integer", description="giới hạn gửi thông báo trong 1 tháng"),
     * @SWG\Property(property="sms_price", type="integer", description="giá tiền cho 1 tin nhắn"),
     * @SWG\Property(property="link_whether", type="string"),
     * @SWG\Property(property="email_account_push", type="string"),
     * @SWG\Property(property="email_password_push", type="string"),
     * @SWG\Property(property="sms_brandname_push", type="string"),
     * @SWG\Property(property="sms_account_push", type="string"),
     * @SWG\Property(property="sms_password_push", type="string"),
     * @SWG\Property(property="message_request_default", type="string"),
     * @SWG\Property(property="alias", type="string"),
     * @SWG\Property(property="service_bill_template", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="service_bill_invoice_template", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'description',
            'domain',
            'email',
            'hotline',
            'address',
            'bank_account',
            'bank_name',
            'bank_holders',
            'cash_instruction',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
            'tax_code',
            'tax_info',
            'city_id',
            'city_name' => function ($model) {
                if (!empty($model->city_id)) {
                    $city = City::findOne(['id' => $model->city_id]);
                    return $city->name;
                }
                return '';
            },
            'one_signal_app_id',
            'setting_group_receives_notices_financial' => function ($model) {
                if(!empty($model->setting_group_receives_notices_financial)){
                    $ids = json_decode($model->setting_group_receives_notices_financial);
                    return AuthGroupResponse::find()->where(['id' => $ids])->all();
                }
                return null;
            },
            'payment_config' => function($model){
                return PaymentConfigResponse::findOne(['building_cluster_id' => $model->id, 'service_provider_id' => null]);
            },
            'created_at',
            'updated_at',
            'limit_sms',
            'limit_email',
            'limit_notify',
            'sms_price',
            'link_whether',
            'email_account_push',
            'email_password_push',
            'sms_brandname_push',
            'sms_account_push',
            'sms_password_push',
            'message_request_default',
            'alias',
            'service_bill_template' => function ($model) {
                return (!empty($model->service_bill_template)) ? json_decode($model->service_bill_template) : null;
            },
            'service_bill_invoice_template' => function ($model) {
                return (!empty($model->service_bill_invoice_template)) ? json_decode($model->service_bill_invoice_template) : null;
            },
        ];
    }
}
