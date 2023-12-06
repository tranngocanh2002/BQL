<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "payment_config".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $gate Cổng thanh toán : 0 - ngân lượng, 1 - vnpay , 2 - momo ...
 * @property string $receiver_account Tài khoản nhận
 * @property string $merchant_name
 * @property string $partner_code
 * @property string $access_key
 * @property string $secret_key
 * @property string $merchant_id
 * @property string $merchant_pass
 * @property string $checkout_url link check out
 * @property string $checkout_url_old link check out cu
 * @property string $return_url
 * @property string $cancel_url
 * @property string $notify_url
 * @property string $return_web_url
 * @property string $note
 * @property int $status 0 - chưa kích hoạt, 1 - đã kích hoạt
 * @property int $service_provider_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property BuildingCluster $buildingCluster
 */
class PaymentConfig extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $status_lst = [
        self::STATUS_INACTIVE => 'Chưa kích hoạt',
        self::STATUS_ACTIVE => 'Đã kích hoạt',
    ];

    const GATE_NGANLUONG = 0;
    const GATE_VNPAY = 1;
    const GATE_MOMO = 2;

    public static $gate_lst = [
        self::GATE_NGANLUONG => 'Ngân Lượng',
        self::GATE_VNPAY => 'VNPay',
        self::GATE_MOMO => 'MoMo'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'gate'], 'required'],
            [['building_cluster_id', 'gate', 'status', 'service_provider_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [[
                'merchant_name', 'partner_code', 'access_key', 'secret_key',
                'receiver_account', 'merchant_id', 'merchant_pass', 'checkout_url',
                'checkout_url_old', 'return_url', 'cancel_url', 'notify_url', 'return_web_url', 'note'
            ], 'string', 'max' => 255],
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
            'gate' => Yii::t('common', 'Gate'),
            'receiver_account' => Yii::t('common', 'Receiver Account'),
            'merchant_name' => Yii::t('common', 'Merchant Name'),
            'partner_code' => Yii::t('common', 'Partner Code'),
            'access_key' => Yii::t('common', 'Access Key'),
            'secret_key' => Yii::t('common', 'Secret Key'),
            'merchant_id' => Yii::t('common', 'Merchant ID'),
            'merchant_pass' => Yii::t('common', 'Merchant Pass'),
            'checkout_url' => Yii::t('common', 'Checkout Url'),
            'checkout_url_old' => Yii::t('common', 'Checkout Url Old'),
            'return_url' => Yii::t('common', 'Return Url'),
            'cancel_url' => Yii::t('common', 'Cancel Url'),
            'notify_url' => Yii::t('common', 'Notify Url'),
            'return_web_url' => Yii::t('common', 'Return Web Url'),
            'note' => Yii::t('common', 'Note'),
            'status' => Yii::t('common', 'Status'),
            'service_provider_id' => Yii::t('common', 'Service Provider Id'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
        ];
    }

    /**
     * @inheritdoc
     */
    function behaviors() {
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
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }
}
