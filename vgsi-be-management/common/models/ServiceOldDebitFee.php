<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_old_debit_fee".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $service_map_management_id
 * @property int $total_money tổng tiền nợ còn lại > 0 là phải trả, < 0 được hoàn
 * @property string $description
 * @property string $description_en
 * @property string $json_desc
 * @property int $status 0 - chưa duyệt, 1 - đã duyệt
 * @property int $is_created_fee 0 - chưa tạo phí thanh toán, 1 - đã tạo phí thanh toán => không được sửa
 * @property int $fee_of_month
 * @property int $service_payment_fee_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Apartment $apartment
 * @property ServiceMapManagement $serviceMapManagement
 * @property ServicePaymentFee $servicePaymentFee
 */
class ServiceOldDebitFee extends \yii\db\ActiveRecord
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const IS_UNCREATED_FEE = 0;
    const IS_CREATED_FEE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_old_debit_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'building_area_id', 'apartment_id', 'service_map_management_id', 'total_money', 'status', 'is_created_fee', 'fee_of_month', 'service_payment_fee_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description', 'description_en', 'json_desc'], 'string'],
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
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'total_money' => Yii::t('common', 'Total Money'),
            'description' => Yii::t('common', 'Description'),
            'json_desc' => Yii::t('common', 'Json Desc'),
            'status' => Yii::t('common', 'Status'),
            'is_created_fee' => Yii::t('common', 'Is Created Fee'),
            'fee_of_month' => Yii::t('common', 'Fee Of Month'),
            'service_payment_fee_id' => Yii::t('common', 'Service Payment Fee ID'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicePaymentFee()
    {
        return $this->hasOne(ServicePaymentFee::className(), ['id' => 'service_payment_fee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }

    public function resetInfo()
    {
        if(!$this->delete()){
            Yii::error($this->errors);
            return false;
        }
        return true;
    }
}
