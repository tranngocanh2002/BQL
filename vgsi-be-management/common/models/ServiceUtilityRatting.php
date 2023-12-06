<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_utility_ratting".
 *
 * @property int $id
 * @property int|null $building_cluster_id
 * @property int|null $apartment_id
 * @property int|null $service_utility_booking_id
 * @property int|null $service_utility_free_id
 * @property int|null $resident_user_id
 * @property float|null $star
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class ServiceUtilityRatting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_utility_ratting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'apartment_id', 'service_utility_booking_id', 'service_utility_free_id', 'resident_user_id', 'created_at', 'updated_at'], 'integer'],
            [['star'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'building_cluster_id' => 'Building Cluster ID',
            'apartment_id' => 'Apartment ID',
            'service_utility_booking_id' => 'Service Utility Booking ID',
            'service_utility_free_id' => 'Service Utility Free ID',
            'resident_user_id' => 'Resident User ID',
            'star' => 'Star',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
            ]
        ];
    }
}
