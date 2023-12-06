<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "request_report_date".
 *
 * @property int $id
 * @property int $date báo cáo ngày
 * @property int $status Trạng thái request
 * @property int $request_category_id Trạng thái request
 * @property int $total Tổng request
 * @property int $total_answer Tổng trả lời
 * @property int $building_cluster_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property RequestCategory $requestCategory
 */
class RequestReportDate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_report_date';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'building_cluster_id'], 'required'],
            [['date', 'status', 'request_category_id', 'total', 'total_answer', 'building_cluster_id', 'created_at', 'updated_at'], 'integer'],
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
            'request_category_id' => Yii::t('common', 'Request Category ID'),
            'total' => Yii::t('common', 'Total'),
            'total_answer' => Yii::t('common', 'Total Answer'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
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
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestCategory()
    {
        return $this->hasOne(RequestCategory::className(), ['id' => 'request_category_id']);
    }
}
