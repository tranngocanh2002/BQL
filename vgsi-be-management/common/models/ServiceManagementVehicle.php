<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "service_management_vehicle".
 *
 * @property int $id
 * @property string $number biển số xe
 * @property string $description
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $service_parking_level_id
 * @property int $start_date
 * @property int $end_date
 * @property int $tmp_end_date
 * @property int $status
 * @property int $type
 * @property int $cancel_date
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Apartment $apartment
 * @property BuildingCluster $buildingCluster
 * @property ServiceParkingLevel $serviceParkingLevel
 */
class ServiceManagementVehicle extends \yii\db\ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    const STATUS_DEFAULT = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_UNACTIVE = 2;

    public static $status_list = [
        self::STATUS_DEFAULT => "Khởi tạo",
        self::STATUS_ACTIVE => "Đang hoạt động",
        self::STATUS_UNACTIVE => "Đã hủy"
    ];

    const TYPE_MOTO = 1;
    const TYPE_OTO = 2;

    public static $type_list = [
        self::TYPE_MOTO => "Xe máy",
        self::TYPE_OTO => "Ô Tô"
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_management_vehicle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'building_cluster_id', 'apartment_id', 'service_parking_level_id'], 'required'],
            [['description'], 'string'],
            [['type', 'tmp_end_date', 'status', 'is_deleted', 'building_cluster_id', 'building_area_id', 'apartment_id', 'service_parking_level_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'start_date', 'end_date', 'cancel_date'], 'integer'],
            [['number'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'number' => Yii::t('common', 'Number'),
            'description' => Yii::t('common', 'Description'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'service_parking_level_id' => Yii::t('common', 'Service Parking Level ID'),
            'start_date' => Yii::t('common', 'Start Date'),
            'end_date' => Yii::t('common', 'End Date'),
            'tmp_end_date' => Yii::t('common', 'Tmp End Date'),
            'status' => Yii::t('common', 'Status'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'type' => Yii::t('common', 'Type'),
            'cancel_date' => Yii::t('common', 'Cancel Date'),
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
    public function getServiceParkingLevel()
    {
        return $this->hasOne(ServiceParkingLevel::className(), ['id' => 'service_parking_level_id']);
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

//        $cardManagementMapService = CardManagementMapService::findOne(['service_management_id' => $this->id]);
//        if(!empty($cardManagementMapService)){
//            if(!empty($cardManagementMapService->cardManagement)){
//                /*
//                 * TODO: bổ xung sendEparking
//                 */
//                $cardManagementMapService->cardManagement->sendEparking();
//            }
//        }

    }
}
