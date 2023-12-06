<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "management_user_notify".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $management_user_id
 * @property string $title
 * @property string $description
 * @property string $title_en
 * @property string $description_en
 * @property int $type 0 - request, 1 - request answer, 2 - request answer internal, 3 - service bill
 * @property int $is_read 0 - chưa đọc, 1 - đã đọc
 * @property int $is_hidden 0 - chưa ẩn, 1 - đã ẩn
 * @property int $request_id
 * @property int $request_answer_id
 * @property int $request_answer_internal_id
 * @property int $service_bill_id
 * @property int $service_booking_id
 * @property string $code
 * @property int $service_utility_form_id
 * @property int $created_at
 * @property int $updated_at
 */
class ManagementUserNotify extends \yii\db\ActiveRecord
{
    const TYPE_REQUEST = 0;
    const TYPE_REQUEST_ANSWER = 1;
    const TYPE_REQUEST_ANSWER_INTERNAL = 2;
    const TYPE_SERVICE_BILL = 3;
    const TYPE_SERVICE_PAYMENT_FEE = 4;
    const TYPE_APARTMENT_CREATE_BILL = 5;
    const TYPE_SERVICE_BOOKING = 6;
    const TYPE_SECURITY_MODE = 7;
    const TYPE_PAYMENT_GEN_CODE = 8;
    const TYPE_JOB = 9;
    const TYPE_FORM = 10;
    const CHANGE_PHONE = 11;

    const IS_UNREAD = 0;
    const IS_READ = 1;

    const IS_NOT_HIDDEN = 0;
    const IS_HIDDEN = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'management_user_notify';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id'], 'required'],
            [['service_booking_id', 'building_cluster_id', 'building_area_id', 'management_user_id', 'type', 'is_read', 'is_hidden', 'request_id', 'request_answer_id', 'request_answer_internal_id', 'service_bill_id','service_utility_form_id', 'created_at', 'updated_at'], 'integer'],
            [['description', 'description_en','code'], 'string'],
            [['title', 'title_en'], 'string', 'max' => 255],
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
            'management_user_id' => Yii::t('common', 'Management User ID'),
            'title' => Yii::t('common', 'Title'),
            'description' => Yii::t('common', 'Description'),
            'title_en' => Yii::t('common', 'Title En'),
            'description_en' => Yii::t('common', 'Description En'),
            'type' => Yii::t('common', 'Type'),
            'is_read' => Yii::t('common', 'Is Read'),
            'is_hidden' => Yii::t('common', 'Is Hidden'),
            'request_id' => Yii::t('common', 'Request ID'),
            'request_answer_id' => Yii::t('common', 'Request Answer ID'),
            'request_answer_internal_id' => Yii::t('common', 'Request Answer Internal ID'),
            'service_bill_id' => Yii::t('common', 'Service Bill ID'),
            'service_booking_id' => Yii::t('common', 'Service Booking ID'),
            'code' => Yii::t('common', 'code'),
            'service_utility_form_id' => Yii::t('common', 'Service Utility Form Id'),
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
            ],
        ];
    }
}
