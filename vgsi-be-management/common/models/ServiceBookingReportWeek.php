<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_booking_report_week".
 *
 * @property int $id
 * @property int $date Ngày đầu tuần
 * @property int $status 0 - Chưa thanh toán, 1- đã thanh toán
 * @property int $building_cluster_id
 * @property int $service_map_management_id
 * @property int $service_utility_config_id
 * @property int $service_utility_free_id
 * @property int $total_price
 * @property int $created_at
 * @property int $updated_at
 */
class ServiceBookingReportWeek extends \yii\db\ActiveRecord
{
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;

    public static $arrStatus = [
        self::STATUS_UNPAID => 'Chưa thanh toán',
        self::STATUS_PAID => 'Đã thanh toán',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_booking_report_week';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'status', 'building_cluster_id', 'service_map_management_id', 'service_utility_config_id', 'service_utility_free_id', 'total_price', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'date' => Yii::t('common', 'Date'),
            'status' => Yii::t('common', 'Status'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'service_utility_config_id' => Yii::t('common', 'Service Utility Config ID'),
            'service_utility_free_id' => Yii::t('common', 'Service Utility Free ID'),
            'total_price' => Yii::t('common', 'Total Price'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    function behaviors() {
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
}
