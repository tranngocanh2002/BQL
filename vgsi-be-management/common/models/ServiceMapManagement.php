<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "service_map_management".
 *
 * @property int $id
 * @property int $service_id
 * @property string $service_name
 * @property string $service_name_en
 * @property int $service_type
 * @property string $service_description
 * @property string $service_icon_name
 * @property int $service_provider_id
 * @property string $service_base_url
 * @property string $medias
 * @property int $status 0 - chưa kích hoạt, 1 - đã kích hoạt
 * @property int $is_deleted 0 - chưa xóa, 1 - đã xóa
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property string $color
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Service $service
 * @property ServiceProvider $serviceProvider
 */
class ServiceMapManagement extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const NOT_DELETED = 0;
    const DELETED = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_map_management';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'service_provider_id', 'building_cluster_id'], 'required'],
            [['service_id', 'service_type', 'service_provider_id', 'status', 'is_deleted', 'building_cluster_id', 'building_area_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['medias', 'service_description', 'color'], 'string'],
            [['service_name', 'service_name_en', 'service_base_url', 'service_icon_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'service_id' => Yii::t('common', 'Service ID'),
            'service_name' => Yii::t('common', 'Service Name'),
            'service_name_en' => Yii::t('common', 'Service Name En'),
            'service_type' => Yii::t('common', 'Service Type'),
            'service_base_url' => Yii::t('common', 'Service Base Url'),
            'service_description' => Yii::t('common', 'Service Description'),
            'service_icon_name' => Yii::t('common', 'Service Icon Name'),
            'service_provider_id' => Yii::t('common', 'Service Provider ID'),
            'medias' => Yii::t('common', 'Medias'),
            'status' => Yii::t('common', 'Status'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'color' => Yii::t('common', 'Color'),
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
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceProvider()
    {
        return $this->hasOne(ServiceProvider::className(), ['id' => 'service_provider_id']);
    }
}
