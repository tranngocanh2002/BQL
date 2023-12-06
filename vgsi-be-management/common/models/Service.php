<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $logo
 * @property string $icon_name
 * @property string $color
 * @property int $status 0 - chưa kích hoạt, 1 - đã kích hoạt
 * @property int $created_at
 * @property int $updated_at
 * @property int $service_type 0 - Điện, 1 - Nước, 2 - Dịch vụ , ...
 * @property int $type 0 - dich vu he thong, 1 - dich vu phat sinh
 * @property int $type_target 0 - theo phòng, 1 theo resident
 * @property string $base_url
 * @property string $name_en
 * @property string $description_en
 */
class Service extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    public static $status_list = [
        self::STATUS_INACTIVE => "Chưa kích hoạt",
        self::STATUS_ACTIVE => "Đã kích hoạt",
    ];

    const TYPE_SYSTEM = 0;
    const TYPE_ARISING = 1;
    public static $type_list = [
        self::TYPE_SYSTEM => "Hệ thống",
        self::TYPE_ARISING => "Phát sinh",
    ];

    const TYPE_TARGET_APARTMENT = 0;
    const TYPE_TARGET_RESIDENT = 1;
    public static $type_target_list = [
        self::TYPE_TARGET_APARTMENT => "Căn hộ",
        self::TYPE_TARGET_RESIDENT => "Cư dân",
    ];

    const SERVICE_TYPE_ELECTRIC = 0;
    const SERVICE_TYPE_WATER = 1;
    const SERVICE_TYPE_CLEANING = 2;
    const SERVICE_TYPE_UTILITY = 3;
    const SERVICE_TYPE_PACKING = 4;
    const SERVICE_TYPE_OLD_DEBIT = 5;
    public static $service_type_list = [
        self::SERVICE_TYPE_ELECTRIC => "Điện",  // base url => /electric
        self::SERVICE_TYPE_WATER => "Nước", // base url => /water
        self::SERVICE_TYPE_CLEANING => "Vệ sinh",   // base url => /apartment-fee
        self::SERVICE_TYPE_UTILITY => "Tiện ích", // base url => /utility-free
        self::SERVICE_TYPE_PACKING => "Gửi xe", // base url => /moto-packing
        self::SERVICE_TYPE_OLD_DEBIT => "Nợ cũ chuyển giao",    // base url => /old-debit
    ];

    public static $service_base_url_list = [
        self::SERVICE_TYPE_ELECTRIC => "/electric",  // base url => /electric
        self::SERVICE_TYPE_WATER => "/water", // base url => /water
        self::SERVICE_TYPE_CLEANING => "/apartment-fee",   // base url => /apartment-fee
        self::SERVICE_TYPE_UTILITY => "/utility-free", // base url => /utility-free
        self::SERVICE_TYPE_PACKING => "/moto-packing", // base url => /moto-packing
        self::SERVICE_TYPE_OLD_DEBIT => "/old_debit",    // base url => /old_debit
    ];

    public static $service_icon_name_list = [
        self::SERVICE_TYPE_ELECTRIC => "icPower", // base url => /electric
        self::SERVICE_TYPE_WATER => "icWater", // base url => /water
        self::SERVICE_TYPE_CLEANING => "icManagement", // base url => /apartment-fee
        self::SERVICE_TYPE_UTILITY => "icManagement", // base url => /utility-free
        self::SERVICE_TYPE_PACKING => "icRideParking", // base url => /moto-packing
        self::SERVICE_TYPE_OLD_DEBIT => "icOldDebit", // base url => /old-debit
    ];

    public static $icon_list = [
        "icPower" => "icPower",  // base url => /electric
        "icWater" => "icWater", // base url => /water
        "icManagement" => "icManagement",   // base url => /apartment-fee
        "icRideParking" => "icRideParking", // base url => /moto-packing
        "icOldDebit" => "icOldDebit",    // base url => /old-debit
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status', 'created_at', 'updated_at', 'service_type', 'type', 'type_target'], 'integer'],
            [['name','name_en', 'logo', 'icon_name', 'base_url', 'color'], 'string', 'max' => 255],
            [['description','description_en'], 'string'],
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
            'description' => Yii::t('common', 'Description'),
            'description_en' => Yii::t('common', 'Description (En)'),
            'logo' => Yii::t('common', 'Logo'),
            'icon_name' => Yii::t('common', 'Icon Name'),
            'status' => Yii::t('common', 'Status'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'service_type' => Yii::t('common', 'Service Type'),
            'type' => Yii::t('common', 'Type'),
            'type_target' => Yii::t('common', 'Type Target'),
            'base_url' => Yii::t('common', 'Base Url'),
            'color' => Yii::t('common', 'Color'),
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
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if(isset($this->service_type)){
            $this->base_url = self::$service_base_url_list[$this->service_type];
        }

        if(isset($this->service_type)){
            $this->icon_name = self::$service_icon_name_list[$this->service_type];
        }

        return true;
    }
}
