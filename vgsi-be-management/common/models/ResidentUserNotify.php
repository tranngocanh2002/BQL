<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "resident_user_notify".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $resident_user_id
 * @property string $title
 * @property string $description
 * @property string $title_en
 * @property string $description_en
 * @property int $type 0 - request, 1 - request answer, 2 - request answer internal, 3 - service bill, 4 - announcement
 * @property int $is_read 0 - chưa đọc, 1 - đã đọc
 * @property int $is_hidden 0 - chưa ẩn, 1 - đã ẩn
 * @property int $request_id
 * @property int $request_answer_id
 * @property int $request_answer_internal_id
 * @property int $service_bill_id
 * @property int $announcement_item_id
 * @property int $service_payment_fee_id
 * @property int $service_booking_id
 * @property int $service_utility_form_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Apartment $apartment
 */
class ResidentUserNotify extends \yii\db\ActiveRecord
{
    const TYPE_REQUEST = 0;
    const TYPE_REQUEST_ANSWER = 1;
    const TYPE_REQUEST_ANSWER_INTERNAL = 2;
    const TYPE_SERVICE_BILL = 3;
    const TYPE_ANNOUNCEMENT = 4;
    const TYPE_MANAGEMENT_CREATE_PAYMENT_FEE = 5;
    const TYPE_SERVICE_BOOKING = 6;

    const TYPE_SERVICE_FORM = 7;
    const TYPE_SERVICE_GEN_CODE = 8;

    const IS_UNREAD = 0;
    const IS_READ = 1;

    const IS_NOT_HIDDEN = 0;
    const IS_HIDDEN = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resident_user_notify';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id'], 'required'],
            [['service_booking_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'resident_user_id', 'type', 'is_read', 'is_hidden', 'request_id', 'request_answer_id', 'request_answer_internal_id', 'service_bill_id', 'announcement_item_id', 'service_payment_fee_id','service_utility_form_id', 'created_at', 'updated_at'], 'integer'],
            [['description', 'description_en'], 'string'],
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
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
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
            'announcement_item_id' => Yii::t('common', 'Announcement Item ID'),
            'service_payment_fee_id' => Yii::t('common', 'Service Payment Fee ID'),
            'service_booking_id' => Yii::t('common', 'Service Booking ID'),
            'service_utility_form_id' => Yii::t('common', 'Service Utility Form ID'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if("announcement-campaign" == Yii::$app->controller->id)
        {
            return true;
        }
        //lấy ra apartment_id từ các id khác
        if(!empty($this->request_id)){
            $item = Request::findOne(['id' => $this->request_id]);
        }else if(!empty($this->service_bill_id)){
            $item = ServiceBill::findOne(['id' => $this->service_bill_id]);
        }else if(!empty($this->announcement_item_id)){
            if(-1 != $this->apartment_id)
            {
                $item = AnnouncementItem::findOne(['id' => $this->announcement_item_id]);   
            }
        }else if(!empty($this->service_payment_fee_id)){
            $item = ServicePaymentFee::findOne(['id' => $this->service_payment_fee_id]);
        }else if(!empty($this->service_booking_id)){
            $item = ServiceUtilityBooking::findOne(['id' => $this->service_booking_id]);
        }
        if(!empty($item)){
            $this->apartment_id = $item->apartment_id;
        }
        return true;
    }
}
