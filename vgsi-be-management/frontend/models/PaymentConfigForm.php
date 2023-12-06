<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\PaymentConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentConfigForm")
 * )
 */
class PaymentConfigForm extends Model
{

    public $id;

    public $building_cluster_id;

    /**
     * @SWG\Property(description="receiver account")
     * @var string
     */
    public $receiver_account;

    /**
     * @SWG\Property(description="merchant id")
     * @var string
     */
    public $merchant_id;

    /**
     * @SWG\Property(description="merchant pass")
     * @var string
     */
    public $merchant_pass;

    /**
     * @SWG\Property(description="status: 0- chưa kích hoạt, 1 - đã kích hoạt")
     * @var integer
     */
    public $status;

    public $service_provider_id;

    public $gate;

    public $checkout_url;

    public $checkout_url_old;

    public $return_url;

    public $cancel_url;

    public $notify_url;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_area_id'], 'required'],
            [['status', 'gate', 'building_area_id', 'service_provider_id'], 'integer'],
            [['receiver_account', 'merchant_id', 'merchant_pass', 'checkout_url', 'checkout_url_old', 'return_url', 'cancel_url', 'notify_url'], 'string'],
            [['id'], 'required', "on" => ['update']],
            [['id'], 'integer', "on" => ['update']],
        ];
    }

    public function main(){
        if(!empty($this->service_provider_id)){
            $payConfig = PaymentConfig::findOne(['building_cluster_id' => $this->building_cluster_id, 'gate' => PaymentConfig::GATE_NGANLUONG, 'service_provider_id' => $this->service_provider_id]);
        }else{
            $payConfig = PaymentConfig::findOne(['building_cluster_id' => $this->building_cluster_id, 'gate' => PaymentConfig::GATE_NGANLUONG, 'service_provider_id' => null]);
        }
        if(empty($payConfig)){
            $this->gate = PaymentConfig::GATE_NGANLUONG;
            return $this->create();
        }else{
            $this->id = $payConfig->id;
            return $this->update();
        }
    }
    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $paymentConfig = New PaymentConfig();
            $paymentConfig->load(CUtils::arrLoad($this->attributes), '');
            $link_payment_config = Yii::$app->params['PaymentConfig'];
            if ($this->gate == PaymentConfig::GATE_NGANLUONG) {
                $nganLuong = Yii::$app->params['NganLuong'];
                $this->checkout_url = $nganLuong['checkout_url'];
                $this->checkout_url_old = $nganLuong['checkout_url_old'];
                $this->return_url = $link_payment_config['return_url'];
                $this->cancel_url = $link_payment_config['cancel_url'];
                $this->notify_url = $link_payment_config['notify_url'];
            }
            if (!$paymentConfig->save()) {
                Yii::error($paymentConfig->getErrors());
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
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

    public function update()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $paymentConfig = PaymentConfig::findOne(['id' => $this->id]);
            $paymentConfig->load(CUtils::arrLoad($this->attributes), '');
            if (!$paymentConfig->save()) {
                Yii::error($paymentConfig->getErrors());
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
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
