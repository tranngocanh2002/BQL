<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "resident_notify_receive_config".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $resident_user_id
 * @property int $channel 0 - notify app, 1 - email, 2 - sms
 * @property int $type 0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc
 * @property int $action_create tạo mới: 0- ko nhận, 1 - có nhận
 * @property int $action_update cập nhật: 0- ko nhận, 1 - có nhận
 * @property int $action_cancel hủy: 0- ko nhận, 1 - có nhận
 * @property int $action_delete xóa: 0- ko nhận, 1 - có nhận
 * @property int $action_approved phê duyệt: 0- ko nhận, 1 - có nhận
 * @property int $action_comment bình luận: 0- ko nhận, 1 - có nhận
 * @property int $action_rate đánh giá: 0- ko nhận, 1 - có nhận
 * @property int $created_at
 * @property int $updated_at
 */
class ResidentNotifyReceiveConfig extends \yii\db\ActiveRecord
{

    const CHANNEL_NOTIFY_APP = 0;
    const CHANNEL_EMAIL = 1;
    const CHANNEL_SMS = 2;

    public static $channel_list = [
        self::CHANNEL_NOTIFY_APP => "Notify App",
        self::CHANNEL_EMAIL => "Email",
        self::CHANNEL_SMS => "Sms",
    ];

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

    const ACTION_KEY_CREATE = 'action_create';
    const ACTION_KEY_UPDATE = 'action_update';
    const ACTION_KEY_CANCEL = 'action_cancel';
    const ACTION_KEY_DELETE = 'action_delete';
    const ACTION_KEY_APPROVED = 'action_approved';
    const ACTION_KEY_COMMENT = 'action_comment';
    const ACTION_KEY_RATE = 'action_rate';

    const NOT_RECEIVED = 0;
    const RECEIVED = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resident_notify_receive_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'resident_user_id'], 'required'],
            [['building_cluster_id', 'resident_user_id', 'channel', 'type', 'action_create', 'action_update', 'action_cancel', 'action_delete', 'action_approved', 'action_comment', 'action_rate', 'created_at', 'updated_at'], 'integer'],
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
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'channel' => Yii::t('common', 'Channel'),
            'type' => Yii::t('common', 'Type'),
            'action_create' => Yii::t('common', 'Action Create'),
            'action_update' => Yii::t('common', 'Action Update'),
            'action_cancel' => Yii::t('common', 'Action Cancel'),
            'action_delete' => Yii::t('common', 'Action Delete'),
            'action_approved' => Yii::t('common', 'Action Approved'),
            'action_comment' => Yii::t('common', 'Action Comment'),
            'action_rate' => Yii::t('common', 'Action Rate'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
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
        ];
    }
}
