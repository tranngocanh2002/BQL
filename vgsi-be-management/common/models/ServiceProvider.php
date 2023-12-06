<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "service_provider".
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $address
 * @property string $description
 * @property string $medias
 * @property int $status 0 - chưa kích hoạt, 1 - đã kích hoạt
 * @property int $is_deleted 0 - chưa xóa, 1 - đã xóa
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $using_bank_cluster
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property ServiceProviderBillingInfo[] serviceProviderBillingInfo
 */
class ServiceProvider extends \yii\db\ActiveRecord
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
        return 'service_provider';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'building_cluster_id'], 'required'],
            [['medias'], 'string'],
            [['using_bank_cluster', 'status', 'is_deleted', 'building_cluster_id', 'building_area_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'id'], 'integer'],
            [['name', 'address', 'name_en'], 'string', 'max' => 255],
            [['description', 'name_en'], 'string'],
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
            'name_en' => Yii::t('common', 'Name (En)'),
            'address' => Yii::t('common', 'Address'),
            'description' => Yii::t('common', 'Description'),
            'medias' => Yii::t('common', 'Medias'),
            'status' => Yii::t('common', 'Status'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'using_bank_cluster' => Yii::t('common', 'Using Bank Cluster'),
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
    public function getServiceProviderBillingInfo()
    {
        return $this->hasMany(ServiceProviderBillingInfo::className(), ['service_provider_id' => 'id']);
    }
}
