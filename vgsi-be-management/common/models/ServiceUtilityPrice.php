<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_utility_price".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $service_utility_free_id
 * @property int $service_utility_config_id
 * @property string $start_time
 * @property string $end_time
 * @property int $price_hourly Giá theo 1 giờ
 * @property int $price_adult Giá 1 người lớn
 * @property int $price_child Giá 1 trẻ em
 *
 * @property ServiceUtilityConfig $serviceUtilityConfig
 */
class ServiceUtilityPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_utility_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'service_utility_free_id', 'service_utility_config_id', 'price_hourly', 'price_adult', 'price_child'], 'integer'],
            [['start_time', 'end_time'], 'string'],
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
            'service_utility_free_id' => Yii::t('common', 'Service Utility Free ID'),
            'service_utility_config_id' => Yii::t('common', 'Service Utility Config ID'),
            'start_time' => Yii::t('common', 'Start Time'),
            'end_time' => Yii::t('common', 'End Time'),
            'price_hourly' => Yii::t('common', 'Price Hourly'),
            'price_adult' => Yii::t('common', 'Price Adult'),
            'price_child' => Yii::t('common', 'Price Child'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceUtilityConfig()
    {
        return $this->hasOne(ServiceUtilityConfig::className(), ['id' => 'service_utility_config_id']);
    }
}
