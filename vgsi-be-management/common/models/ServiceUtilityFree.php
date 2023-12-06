<?php

namespace common\models;

use common\helpers\StringUtils;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_utility_free".
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $code
 * @property string $hours_open giờ mở cửa 08:20 ..
 * @property string $hours_close giờ đóng cửa 23:20 ..
 * @property string $description
 * @property string $regulation
 * @property string $json_desc
 * @property string $medias
 * @property string $hotline
 * @property int $status
 * @property int $service_id
 * @property int $service_map_management_id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $timeout_pay_request
 * @property int $timeout_cancel_book
 * @property int $limit_book_apartment
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $booking_type
 * @property int $deposit_money
 *
 * @property ServiceMapManagement $serviceMapManagement
 * @property ServiceUtilityBooking $serviceUtilityBooking
 */
class ServiceUtilityFree extends \yii\db\ActiveRecord
{

    const STATUS_CANCEL = -1;
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $status_list = [
        self::STATUS_CANCEL => "Dừng hoạt động",
        self::STATUS_UNACTIVE => "Tạm dừng hoạt động",
        self::STATUS_ACTIVE => "Đang hoạt động"
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_utility_free';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'service_id', 'service_map_management_id', 'building_cluster_id', 'status'], 'required'],
            [['description', 'medias', 'hotline', 'json_desc', 'regulation'], 'string'],
            [['timeout_pay_request', 'timeout_cancel_book', 'limit_book_apartment', 'booking_type', 'service_id', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deposit_money'], 'integer'],
            [['name', 'name_en', 'code', 'hours_open', 'hours_close'], 'string', 'max' => 255],
            [['name', 'building_cluster_id'], 'unique', 'targetAttribute' => ['name'], 'message' => Yii::t('common', "Tên đã tồn tại trên hệ thống!")],
            [['name_en', 'building_cluster_id'], 'unique', 'targetAttribute' => ['name_en'], 'message' => Yii::t('common', "Tên (EN) đã tồn tại trên hệ thống!")],
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
            'code' => Yii::t('common', 'Code'),
            'status' => Yii::t('common', 'Status'),
            'hours_open' => Yii::t('common', 'Hours Open'),
            'hours_close' => Yii::t('common', 'Hours Close'),
            'description' => Yii::t('common', 'Description'),
            'regulation' => Yii::t('common', 'Regulation'),
            'json_desc' => Yii::t('common', 'Json Desc'),
            'medias' => Yii::t('common', 'Medias'),
            'hotline' => Yii::t('common', 'Hotline'),
            'service_id' => Yii::t('common', 'Service ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'booking_type' => Yii::t('common', 'Booking Type'),
            'timeout_pay_request' => Yii::t('common', 'Timeout Pay Request'),
            'timeout_cancel_book' => Yii::t('common', 'Timeout Cancel Book'),
            'limit_book_apartment' => Yii::t('common', 'Limit Book Apartment'),
            'deposit_money' => Yii::t('common', 'Deposit Money'),
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

    private function generateCodeNew($code, $length, $building_cluster_id = null)
    {
        if(empty($building_cluster_id)){
            $buildingCluster = Yii::$app->building->BuildingCluster;
            if (!empty($buildingCluster)) {
                $building_cluster_id = $buildingCluster->id;
            }
        }
        if(empty($building_cluster_id)){ return null;}
        $apartment = ServiceUtilityFree::findOne(['code' => $code, 'building_cluster_id' => $building_cluster_id]);
        if (!empty($apartment)) {
            $code_new = StringUtils::randomStr($length);
            return self::generateCodeNew($code_new, $length);
        }
        return $code;
    }

    /**
     * Generates new code
     */
    public function generateCode($building_cluster_id = null)
    {
        $this->code = strtoupper(self::generateCodeNew(StringUtils::randomStr(6), 6, $building_cluster_id));
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
    public function getServiceUtilityBooking()
    {
        return $this->hasOne(ServiceUtilityBooking::className(), ['id' => 'service_utility_free_id']);
    }
}
