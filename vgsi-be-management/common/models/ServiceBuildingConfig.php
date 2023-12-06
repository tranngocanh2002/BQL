<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_building_config".
 *
 * @property int $id
 * @property int $service_id
 * @property int $service_map_management_id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $price
 * @property int $unit
 * @property int $day ngày tạo phí : mặc định là 1 - (ngày đầu tháng)
 * @property int $month_cycle chu kỳ lặp của tháng: mặc định là 1 - (1 tháng 1 lần)
 * @property int $offset_day
 * @property string $cr_minutes
 * @property string $cr_hours
 * @property string $cr_days
 * @property string $cr_months
 * @property string $cr_days_of_week
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
 */
class ServiceBuildingConfig extends \yii\db\ActiveRecord
{
    const UNIT_M2 = 0;
    const UNIT_APARTMENT = 1;

    const AUTO_CREATE_FEE = 1;

    const IS_VAT = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_building_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'service_map_management_id', 'building_cluster_id'], 'required'],
            [['is_vat', 'auto_create_fee', 'service_id', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'price', 'unit', 'day', 'month_cycle', 'created_at', 'updated_at', 'created_by', 'updated_by', 'offset_day'], 'integer'],
            [['cr_minutes', 'cr_hours', 'cr_days', 'cr_months', 'cr_days_of_week'], 'string', 'max' => 255],
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
            'service_id' => Yii::t('common', 'Service ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'price' => Yii::t('common', 'Price'),
            'unit' => Yii::t('common', 'Unit'),
            'day' => Yii::t('common', 'Day'),
            'month_cycle' => Yii::t('common', 'Month Cycle'),
            'offset_day' => Yii::t('common', 'Offset Day'),
            'cr_minutes' => Yii::t('common', 'Cr Minutes'),
            'cr_hours' => Yii::t('common', 'Cr Hours'),
            'cr_days' => Yii::t('common', 'Cr Days'),
            'cr_months' => Yii::t('common', 'Cr Months'),
            'cr_days_of_week' => Yii::t('common', 'Cr Days Of Week'),
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
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }

    public function createCrField()
    {
        $this->cr_minutes = ',10,';
        $this->cr_hours = ',2,';
        if (!empty($this->day) && $this->day > 0) {
            $this->cr_days = ',' . $this->day . ',';
        }

        if (!empty($this->month_cycle) && $this->month_cycle > 0) {
            $cr_months = '';
            for ($i = $this->month_cycle; $i <= 12; $i += $this->month_cycle) {
                if ($i > 12) {
                    break;
                }
                $j = $i + 1;
                if ($j > 12) {
                    $j = $j - 12;
                    $cr_months = $j . ',' . $cr_months;
                } else {
                    $cr_months .= $j . ',';
                }
            }
            if (!empty($cr_months)) {
                $this->cr_months = ',' . $cr_months;
            }
        }
    }
}
