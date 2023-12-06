<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_provider_billing_info".
 *
 * @property int $id
 * @property string $cash_instruction Hướng dẫn thành toán tiền mặt
 * @property string $transfer_instruction Hướng dẫn thanh toán truyển khoản
 * @property string $bank_name Tên ngân hàng
 * @property string $bank_number Số tài khoản
 * @property string $bank_holders Chủ tài khoản
 * @property int $service_provider_id
 * @property int $status 0 - chưa kích hoạt, 1 - đã kích hoạt
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property ServiceProvider $serviceProvider
 */
class ServiceProviderBillingInfo extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_provider_billing_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_provider_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['cash_instruction', 'transfer_instruction'], 'string'],
            [['bank_name', 'bank_number', 'bank_holders'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'cash_instruction' => Yii::t('common', 'Cash Instruction'),
            'transfer_instruction' => Yii::t('common', 'Transfer Instruction'),
            'bank_name' => Yii::t('common', 'Bank Name'),
            'bank_number' => Yii::t('common', 'Bank Number'),
            'bank_holders' => Yii::t('common', 'Bank Holders'),
            'service_provider_id' => Yii::t('common', 'Service Provider ID'),
            'status' => Yii::t('common', 'Status'),
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
    public function getServiceProvider()
    {
        return $this->hasOne(ServiceProvider::className(), ['id' => 'service_provider_id']);
    }
}
