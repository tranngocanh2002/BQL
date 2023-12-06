<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "notify_send_config".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $type 0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc
 * @property int $send_email 0 - ko gửi, 1 - có gửi
 * @property int $send_sms 0 - ko gửi, 1 - có gửi
 * @property int $send_notify_app 0 - ko gửi, 1 - có gửi
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class NotifySendConfig extends \yii\db\ActiveRecord
{
    const TYPE_NOTIFY = 0;
    const TYPE_NOTIFY_EVENT = 1;
    const TYPE_FEE = 2;
    const TYPE_REQUEST = 3;
    const TYPE_BOOKING = 4;
    const TYPE_JOB = 5;

    public static $type_list = [
        self::TYPE_NOTIFY => "Thông báo thường",
        self::TYPE_NOTIFY_EVENT => "Thông báo sự kiện",
        self::TYPE_FEE => "Thông báo tài chính/công nợ",
        self::TYPE_REQUEST => "Thông báo yêu cầu/phản ánh",
        self::TYPE_BOOKING => "Thông báo đặt dịch vụ",
        self::TYPE_JOB => "Thông báo công việc",
    ];

    const NOT_SEND = 0;
    const SEND = 1;

    public static $action_list = [
        self::NOT_SEND => 'Không gửi',
        self::SEND => 'Có gửi',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notify_send_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id'], 'required'],
            [['building_cluster_id', 'type', 'send_email', 'send_sms', 'send_notify_app', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
            'type' => Yii::t('common', 'Type'),
            'send_email' => Yii::t('common', 'Send Email'),
            'send_sms' => Yii::t('common', 'Send Sms'),
            'send_notify_app' => Yii::t('common', 'Send Notify App'),
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
            ],
        ];
    }

}
