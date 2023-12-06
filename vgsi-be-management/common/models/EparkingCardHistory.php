<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "eparking_card_history".
 *
 * @property int $id
 * @property string $serial
 * @property int $vehicle_type
 * @property int $card_type
 * @property int $ticket_type
 * @property int $datetime_in
 * @property string $plate_in
 * @property string $image1_in
 * @property string $image2_in
 * @property int $datetime_out
 * @property string $plate_out
 * @property string $image1_out
 * @property string $image2_out
 * @property int $status
 * @property int $apartment_id
 * @property int $building_cluster_id
 * @property int $service_management_vehicle_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Apartment $apartment
 */
class EparkingCardHistory extends \yii\db\ActiveRecord
{
    const STATUS_P = 1;
    const STATUS_C = 2;

    public static $status_list = [
        self::STATUS_P => "Xe đang trong bãi",
        self::STATUS_C => "Xe ra ngoài bãi"
    ];

    const TYPE_TU = 1;
    const TYPE_RFID = 2;

    public static $type_list = [
        self::TYPE_TU => "Thẻ từ",
        self::TYPE_RFID => "Thẻ Rfid"
    ];

    const VEHICLE_TYPE_MOTO = 1;
    const VEHICLE_TYPE_OTO = 2;

    public static $vehicle_type_list = [
        self::VEHICLE_TYPE_MOTO => "Xe máy",
        self::VEHICLE_TYPE_OTO => "Ô Tô"
    ];

    const TICKET_TYPE_PAY = 1;
    const TICKET_TYPE_MONTHLY = 2;

    public static $ticket_type_list = [
        self::TICKET_TYPE_PAY => "Vé lượt",
        self::TICKET_TYPE_MONTHLY => "Vé tháng"
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eparking_card_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id', 'building_cluster_id', 'service_management_vehicle_id', 'vehicle_type', 'card_type', 'ticket_type', 'datetime_in', 'datetime_out', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['serial', 'plate_in', 'image1_in', 'image2_in', 'plate_out', 'image1_out', 'image2_out'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster Id'),
            'apartment_id' => Yii::t('common', 'Apartment Id'),
            'service_management_vehicle_id' => Yii::t('common', 'Service Management Vehicle Id'),
            'serial' => Yii::t('common', 'Serial'),
            'vehicle_type' => Yii::t('common', 'Vehicle Type'),
            'card_type' => Yii::t('common', 'Card Type'),
            'ticket_type' => Yii::t('common', 'Ticket Type'),
            'datetime_in' => Yii::t('common', 'Datetime In'),
            'plate_in' => Yii::t('common', 'Plate In'),
            'image1_in' => Yii::t('common', 'Image1 In'),
            'image2_in' => Yii::t('common', 'Image2 In'),
            'datetime_out' => Yii::t('common', 'Datetime Out'),
            'plate_out' => Yii::t('common', 'Plate Out'),
            'image1_out' => Yii::t('common', 'Image1 Out'),
            'image2_out' => Yii::t('common', 'Image2 Out'),
            'status' => Yii::t('common', 'Status'),
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
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }
}
