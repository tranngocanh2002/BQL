<?php

namespace common\models;

use common\helpers\ErrorCode;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\HttpException;

/**
 * This is the model class for table "service_utility_config".
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $address
 * @property string $address_en
 * @property int $building_cluster_id
 * @property int $service_utility_free_id
 * @property int $type 0- không thu phí, 1 - có thu phí
 * @property int $booking_type 0 - đặt theo lượt, 1 - đặt theo slot, 2 - đặt theo khung giờ
 * @property int $total_slot Tổng số chỗ sử dụng
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property ServiceUtilityFree $serviceUtilityFree
 */
class ServiceUtilityConfig extends \yii\db\ActiveRecord
{
    const TYPE_FREE = 0;
    const TYPE_NOT_FREE = 1;

    const BOOKING_TYPE_TURN = 0;
    const BOOKING_TYPE_SLOT = 1;
    const BOOKING_TYPE_TIME = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_utility_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'service_utility_free_id', 'type', 'booking_type', 'total_slot', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'address', 'name_en', 'address_en'], 'string', 'max' => 255],
            // [['name', 'building_cluster_id'], 'unique', 'targetAttribute' => ['name'], 'message' => Yii::t('common', "Tên chỗ đã tồn tại trên hệ thống")],
            // [['name_en', 'building_cluster_id'], 'unique', 'targetAttribute' => ['name_en'], 'message' => Yii::t('common', "Tên chỗ (EN) đã tồn tại trên hệ thống")],
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
            'address' => Yii::t('common', 'Address'),
            'address_en' => Yii::t('common', 'Address En'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'service_utility_free_id' => Yii::t('common', 'Service Utility Free ID'),
            'type' => Yii::t('common', 'Type'),
            'booking_type' => Yii::t('common', 'Booking Type'),
            'total_slot' => Yii::t('common', 'Total Slot'),
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
    public function getServiceUtilityFree()
    {
        return $this->hasOne(ServiceUtilityFree::className(), ['id' => 'service_utility_free_id']);
    }

    public function checkTimeBook($book_time)
    {
        $is_check = false;
        if(empty($book_time)){
           return $is_check;
        }
        $book_time = json_decode($book_time, true);
        $i = 0;
        $j = 0;
        foreach ($book_time as $item){
            $i++;
            $start_time = $item['start_time'];
            $end_time = $item['end_time'];
            $serviceUtilityPrices = ServiceUtilityPrice::find()->where(['service_utility_config_id' => $this->id])->all();
            foreach ($serviceUtilityPrices as $serviceUtilityPrice){
                $start_time_in = strtotime(date('Y-m-d', $start_time).' '.$serviceUtilityPrice->start_time.':00');
                $end_time_in = strtotime(date('Y-m-d', $end_time).' '.$serviceUtilityPrice->end_time.':00');
                if($start_time_in <= $start_time && $end_time_in >= $end_time){
                    $j++;
                }
            }
        }
        if($i == $j){
            $is_check = true;
        }
        return $is_check;
    }
    public function getSlotNull($start_time, $end_time, $service_utility_booking_id = null)
    {
        $serviceUtilityBookings = ServiceUtilityBooking::find()
            ->where(['>=', 'status', ServiceUtilityBooking::STATUS_CREATE])
            ->andWhere(['service_utility_config_id' => $this->id])
            ->andWhere(['>=', 'start_time', $start_time])
            ->andWhere(['not', ['id' => $service_utility_booking_id]])
            ->andWhere(['<=', 'end_time', $end_time])
            ->all();
        $slot_using = 0;
        foreach ($serviceUtilityBookings as $serviceUtilityBooking) {
            $slot_using += $serviceUtilityBooking->total_slot;
        }
        return $this->total_slot - $slot_using;
    }

    public function getTimeNull($start_time, $end_time, $service_utility_config_id)
    {
        $serviceUtilityBookingTime = 0;
        $bookTimes = ServiceUtilityBooking::find()
        ->select('book_time')
        ->where(['=', 'status', ServiceUtilityBooking::STATUS_ACTIVE])
        ->andWhere(['service_utility_config_id' => $service_utility_config_id])
        ->all();
        foreach ($bookTimes as $booking) {
            $jsonTime = json_decode($booking->book_time, true);
            foreach ($jsonTime as $jsonTimes) {
                // $serviceUtilityBookingTime[] = $jsonTimes;
                if ($jsonTimes['start_time'] == $start_time && $jsonTimes['end_time'] == $end_time) {
                    $serviceUtilityBookingTime++;
                }
            }
        }
        return $serviceUtilityBookingTime;
    }

    public function getPrice($start_time, $end_time, $total_adult = 0, $total_child = 0)
    {
        $price = 0;
        $serviceUtilityPrices = ServiceUtilityPrice::find()->where(['service_utility_config_id' => $this->id])->all();
        $check_time = false;
        foreach ($serviceUtilityPrices as $serviceUtilityPrice){
            $start_time_in = strtotime(date('Y-m-d', $start_time).' '.$serviceUtilityPrice->start_time.':00');
            $end_time_in = strtotime(date('Y-m-d', $end_time).' '.$serviceUtilityPrice->end_time.':00');
            if($start_time_in == $start_time && $end_time_in == $end_time){
                $check_time = true;
                if ($this->booking_type == self::BOOKING_TYPE_TURN) {
                    $price = $serviceUtilityPrice->price_adult + $serviceUtilityPrice->price_child + $serviceUtilityPrice->price_hourly;
                } else if ($this->booking_type == self::BOOKING_TYPE_SLOT) {
                    $price = $total_adult * $serviceUtilityPrice->price_adult + $total_child * $serviceUtilityPrice->price_child;
                } else {
                    $hours = intval(($end_time - $start_time) / (60 * 60));
                    if($hours <= 0){ $hours = 1; } //chưa đủ 1 giờ sẽ làm tròn thành 1 giờ sử dụng
                    $price = $hours * $serviceUtilityPrice->price_hourly;
                }
            }
        }
        if($check_time == false){
            throw new HttpException(ErrorCode::ERROR_INVALID_PARAM, Yii::t('frontend', 'Thời gian không hợp lệ'));
        }
        return $price;
    }
}
