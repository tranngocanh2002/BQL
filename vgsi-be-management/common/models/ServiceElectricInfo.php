<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_electric_info".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $service_map_management_id
 * @property int $start_date
 * @property int $end_date
 * @property int $tmp_end_date
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $end_index
 *
 * @property Apartment $apartment
 * @property BuildingCluster $buildingCluster
 * @property ServiceMapManagement $serviceMapManagement
 */
class ServiceElectricInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_electric_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'apartment_id', 'service_map_management_id'], 'required'],
            [['end_index', 'tmp_end_date', 'building_cluster_id', 'building_area_id', 'apartment_id', 'service_map_management_id', 'start_date', 'end_date', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'start_date' => Yii::t('common', 'Start Date'),
            'end_date' => Yii::t('common', 'End Date'),
            'tmp_end_date' => Yii::t('common', 'Tmp End Date'),
            'end_index' => Yii::t('common', 'End Index'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }
}
