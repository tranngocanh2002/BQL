<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_order_item".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $payment_order_id
 * @property int $service_payment_fee_id
 * @property int $amount
 *
 * @property PaymentOrder $paymentOrder
 */
class PaymentOrderItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_order_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'payment_order_id', 'service_payment_fee_id', 'amount'], 'integer'],
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
            'payment_order_id' => Yii::t('common', 'Payment Order ID'),
            'service_payment_fee_id' => Yii::t('common', 'Service Payment Fee ID'),
            'amount' => Yii::t('common', 'Amount'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentOrder()
    {
        return $this->hasOne(PaymentOrder::className(), ['id' => 'payment_order_id']);
    }
}
