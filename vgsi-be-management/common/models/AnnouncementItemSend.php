<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "announcement_item_send".
 *
 * @property int $id
 * @property int $announcement_campaign_id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property string $tags
 * @property int $status  0 - đã gửi, 1 - thành công, 2 - thất bại
 * @property int $created_at
 * @property int $updated_at
 * @property int $type
 * @property int $end_debt
 *
 * @property Apartment $apartment
 */
class AnnouncementItemSend extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'announcement_item_send';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'end_debt', 'announcement_campaign_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['tags'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'announcement_campaign_id' => Yii::t('common', 'Announcement Campaign ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'tags' => Yii::t('common', 'Tags'),
            'status' => Yii::t('common', 'Status'),
            'type' => Yii::t('common', 'Type'),
            'end_debt' => Yii::t('common', 'End Debt'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
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
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }
}
