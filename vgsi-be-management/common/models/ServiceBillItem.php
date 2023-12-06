<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_bill_item".
 *
 * @property int $id
 * @property int $service_bill_id
 * @property int $service_payment_fee_id
 * @property int $service_map_management_id
 * @property string $description
 * @property int $price
 * @property int $fee_of_month Thanh toán phí tháng ? 
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property ServiceMapManagement $serviceMapManagement
 * @property ServiceBill $serviceBill
 * @property ServicePaymentFee $servicePaymentFee
 */
class ServiceBillItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_bill_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_bill_id', 'service_payment_fee_id', 'service_map_management_id', 'price'], 'required'],
            [['service_bill_id', 'service_payment_fee_id', 'service_map_management_id', 'price', 'fee_of_month', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'service_bill_id' => Yii::t('common', 'Service Bill ID'),
            'service_payment_fee_id' => Yii::t('common', 'Service Payment Fee ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'description' => Yii::t('common', 'Description'),
            'price' => Yii::t('common', 'Price'),
            'fee_of_month' => Yii::t('common', 'Fee Of Month'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
        ];
    }

    /**
     * @inheritdoc
     */
    function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'time',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ]
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => ['updated_by'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ],
            ]
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceBill()
    {
        return $this->hasOne(ServiceBill::className(), ['id' => 'service_bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicePaymentFee()
    {
        return $this->hasOne(ServicePaymentFee::className(), ['id' => 'service_payment_fee_id']);
    }
}
