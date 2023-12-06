<?php

namespace common\models;

use common\helpers\QueueLib;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "card_management".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $apartment_id
 * @property int $status 0 - chưa xác thực, 1 - đã xác thực, 2 - hủy : toàn bộ dịch vụ ăn theo bị hủy
 * @property string $number Số thẻ
 * @property int $resident_user_id
 * @property int $type
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property string $code Số thẻ
 * @property string $description Số thẻ
 * @property string $description_en Số thẻ
 * @property string $reason Số thẻ
 *
 * @property Apartment $apartment
 * @property ApartmentMapResidentUser $apartmentMapResidentUser
 * @property ResidentUser $residentUser
 */
class CardManagement extends \yii\db\ActiveRecord
{
    const STATUS_CREATE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCK = 2;
    const STATUS_RECALL = 3;
    const STATUS_CANCEL = 4;

    const TYPE_TU = 1;
    const TYPE_RFID = 2;

    public static $type_list = [
        self::TYPE_TU => "Thẻ từ",
        self::TYPE_RFID => "Thẻ Rfid"
    ];
    public static $status_list = [
        self::STATUS_CREATE => "Tạo mới",
        self::STATUS_ACTIVE => "Kích hoạt",
        self::STATUS_BLOCK => "Khoá",
        self::STATUS_RECALL => "Thu hồi",
        self::STATUS_CANCEL => "Huỷ",
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'card_management';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['building_cluster_id', 'apartment_id', 'resident_user_id'], 'required'],
            [['type', 'building_cluster_id', 'apartment_id', 'resident_user_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'status' => Yii::t('common', 'Status'),
            'number' => Yii::t('common', 'Number'),
            'resident_user_id' => Yii::t('common', 'Resident User Id'),
            'type' => Yii::t('common', 'Type'),
            'code' => Yii::t('common', 'Code'),
            'description' => Yii::t('common', 'Description'),
            'description_en' => Yii::t('common', 'Description_en'),
            'reason' => Yii::t('common', 'Reason'),
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
    public function getResidentUser()
    {
        return $this->hasOne(ResidentUser::className(), ['id' => 'resident_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }

    public function getApartmentMapResidentUser()
    {
        return $this->hasOne(ApartmentMapResidentUser::className(), ['apartment_id' => 'apartment_id', 'resident_user_id' => 'resident_user_id'])->where(['is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
    }

    /*
     * gửi bản tin rabbitmq tới eparking
     */
    public function sendEparking($is_delete = false){
        $cardManagementMapServices = CardManagementMapService::find()->where(['card_management_id' => $this->id, 'type' => CardManagementMapService::TYPE_PARKING])->all();
        foreach ($cardManagementMapServices as $cardManagementMapService){
            if($cardManagementMapService->expiry_time > time()){
                $cardManagementMapService->status = CardManagementMapService::STATUS_ACTIVE;
            }
            if($cardManagementMapService->status !== CardManagementMapService::STATUS_ACTIVE){
                $cardManagementMapService->status = CardManagementMapService::STATUS_CREATE;
            }
            if($this->status !== CardManagement::STATUS_ACTIVE){
                $cardManagementMapService->status = CardManagement::STATUS_CREATE;
            }
            if($is_delete === true){
                $cardManagementMapService->status = CardManagement::STATUS_CREATE;
            }
            $plate = '';
            $vehicle_type = 0;
            $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $cardManagementMapService->service_management_id]);
            if(!empty($serviceManagementVehicle)){
                $plate = $serviceManagementVehicle->number;
                if(!empty($serviceManagementVehicle->serviceParkingLevel)){
                    if($serviceManagementVehicle->serviceParkingLevel->code == 'MX1'){
                        $vehicle_type = 2;
                    }else if($serviceManagementVehicle->serviceParkingLevel->code == 'MX2'){
                        $vehicle_type = 1;
                    }
                }
            }
            if(!$cardManagementMapService->save()){
                Yii::error($cardManagementMapService->errors);
            };
            $payload = [
                'type' => 'card',
                'data' => [
                    'serial' => $this->number,
                    'card_type' => $this->type,
                    'customer_id' => $this->resident_user_id,
                    'start_datetime' => '2019/10/01 00:00:00',
                    'end_datetime' => date('Y/m/d H:i:s', $cardManagementMapService->expiry_time),
                    'status' => $cardManagementMapService->status,
                    'price' => 0,
                    'plate' => $plate,
                    'vehicle_type' => $vehicle_type,
                ]
            ];
            QueueLib::channelEparking(json_encode($payload), $this->building_cluster_id);
        }
    }
}
