<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "card_management_map_service".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $card_management_id
 * @property int $status 0 - chưa xác thực, 1 - đã xác thực, 2 - hủy
 * @property int $type 0 - thẻ cư dân, 1- thẻ xe ...
 * @property int $service_management_id 0 - id cư dân, 1- id xe ...
 * @property int $expiry_time Hạn sử dụng
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property CardManagement $cardManagement
 */
class CardManagementMapService extends \yii\db\ActiveRecord
{
    const STATUS_CREATE = 0;
    const STATUS_ACTIVE = 1;

    const TYPE_RESIDENT_USER = 0;
    const TYPE_PARKING = 1;

    public static $type_lst = [
        self::TYPE_RESIDENT_USER => "Thẻ Cư Dân",
        self::TYPE_PARKING => "Gửi Xe",
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'card_management_map_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'card_management_id', 'service_management_id'], 'required'],
            [['building_cluster_id', 'card_management_id', 'status', 'type', 'service_management_id', 'expiry_time', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
            'card_management_id' => Yii::t('common', 'Card Management ID'),
            'status' => Yii::t('common', 'Status'),
            'type' => Yii::t('common', 'Type'),
            'service_management_id' => Yii::t('common', 'Service Management ID'),
            'expiry_time' => Yii::t('common', 'Expiry Time'),
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
    public function getCardManagement()
    {
        return $this->hasOne(CardManagement::className(), ['id' => 'card_management_id']);
    }
}
