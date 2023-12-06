<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_electric_level".
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $description
 * @property int $from_level
 * @property int $to_level
 * @property int $price
 * @property int $service_id
 * @property int $service_map_management_id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class ServiceElectricLevel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_electric_level';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'service_id', 'service_map_management_id', 'building_cluster_id'], 'required'],
            [['from_level', 'to_level', 'price', 'service_id', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'name_en', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Name'),
            'name_en' => Yii::t('common', 'Name En'),
            'description' => Yii::t('common', 'Description'),
            'from_level' => Yii::t('common', 'From Level'),
            'to_level' => Yii::t('common', 'To Level'),
            'price' => Yii::t('common', 'Price'),
            'service_id' => Yii::t('common', 'Service ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
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
