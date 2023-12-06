<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_fee_report_date".
 *
 * @property int $id
 * @property int $date báo cáo ngày
 * @property int $status Trạng thái 0 - chưa thanh toán, 1 - đã thanh toán
 * @property int $service_map_management_id theo loai dich vụ
 * @property int $total_price Tổng tiền
 * @property int $building_cluster_id
 * @property int $created_at
 * @property int $updated_at
 */
class ServiceFeeReportDate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_fee_report_date';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'building_cluster_id'], 'required'],
            [['date', 'status', 'service_map_management_id', 'total_price', 'building_cluster_id', 'created_at', 'updated_at'], 'integer'],
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
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'total_price' => Yii::t('common', 'Total Price'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
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
            ]
        ];
    }
}
