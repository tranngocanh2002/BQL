<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_gen_code_item".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $payment_gen_code_id
 * @property int $service_payment_fee_id
 * @property int $amount
 * @property int $type 0- offline, 1 - online
 * @property int $status
 *
 * @property PaymentGenCode $paymentGenCode
 * @property ServicePaymentFee $servicePaymentFee
 */
class PaymentGenCodeItem extends \yii\db\ActiveRecord
{
    const STATUS_CANCEL = -1;
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_gen_code_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'payment_gen_code_id', 'service_payment_fee_id', 'amount', 'type', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'payment_gen_code_id' => Yii::t('common', 'Payment Gen Code ID'),
            'service_payment_fee_id' => Yii::t('common', 'Service Payment Fee ID'),
            'amount' => Yii::t('common', 'Amount'),
            'type' => Yii::t('common', 'Type'),
            'status' => Yii::t('common', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicePaymentFee()
    {
        return $this->hasOne(ServicePaymentFee::className(), ['id' => 'service_payment_fee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentGenCode()
    {
        return $this->hasOne(PaymentGenCode::className(), ['id' => 'payment_gen_code_id']);
    }
}
