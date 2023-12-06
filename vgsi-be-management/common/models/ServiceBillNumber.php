<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_bill_number".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $year
 * @property int $index_number số thứ tự phiếu thu
 * @property int $service_bill_id
 * @property string $service_bill_number
 * @property int $service_bill_type_payment
 * @property int $type 0 - phiếu thu, 1 - phiếu chi
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class ServiceBillNumber extends \yii\db\ActiveRecord
{
    const TYPE_0 = 0;
    const TYPE_1 = 1;
    public static $type_lst = [
        self::TYPE_0 => "Phiếu thu",
        self::TYPE_1 => "Phiếu chi",
    ];

    const TYPE_PAYMENT_CASH = 0;
    const TYPE_PAYMENT_INTERNET_BANKING = 1;

    public static $type_payment_lst = [
        self::TYPE_PAYMENT_CASH => "Tiền mặt",
        self::TYPE_PAYMENT_INTERNET_BANKING => "Chuyển khoản"
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_bill_number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id'], 'required'],
            [['building_cluster_id', 'year', 'index_number', 'service_bill_id', 'service_bill_type_payment', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type'], 'integer'],
            [['service_bill_number'], 'string', 'max' => 255],
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
            'year' => Yii::t('common', 'Year'),
            'index_number' => Yii::t('common', 'Index Number'),
            'service_bill_id' => Yii::t('common', 'Service Bill ID'),
            'service_bill_number' => Yii::t('common', 'Service Bill Number'),
            'service_bill_type_payment' => Yii::t('common', 'Service Bill Type Payment'),
            'type' => Yii::t('common', 'Type'),
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
}
