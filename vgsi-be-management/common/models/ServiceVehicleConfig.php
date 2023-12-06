<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_vehicle_config".
 *
 * @property int $id
 * @property int $service_map_management_id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $auto_create_fee
 * @property double $percent
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_vat
 * @property double $vat_percent // vat
 * @property double $tax_percent // thuế bql
 * @property double $environ_percent //phí bảo vệ môi trường
 *
 * @property ServiceMapManagement $serviceMapManagement
 * @property BuildingCluster $buildingCluster
 */
class ServiceVehicleConfig extends \yii\db\ActiveRecord
{
    const AUTO_CREATE_FEE = 1;

    const TYPE_APARTMENT = 0;
    const TYPE_RESIDENT = 1;

    public static $arrType = [
        self::TYPE_APARTMENT => 'Theo căn hộ',
        self::TYPE_RESIDENT => 'Theo đầu người',
    ];

    const IS_VAT = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_vehicle_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_map_management_id', 'building_cluster_id'], 'required'],
            [['is_vat', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'auto_create_fee', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['percent', 'vat_percent', 'tax_percent', 'environ_percent'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'auto_create_fee' => Yii::t('common', 'Auto Create Fee'),
            'percent' => Yii::t('common', 'Percent'),
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
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }
}
