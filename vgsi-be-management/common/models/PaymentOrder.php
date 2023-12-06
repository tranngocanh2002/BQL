<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "payment_order".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $apartment_id
 * @property int $service_bill_id
 * @property int $total_amount
 * @property string $txh_name
 * @property string $txt_email
 * @property string $txt_phone
 * @property string $description
 * @property string $code Mã đơn hàng
 * @property string $transaction_status
 * @property string $error_code
 * @property string $error_text
 * @property int $status 0 - khởi tạo, 1 - thành công, 2 - thất bại
 * @property int $pay_gate
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Apartment $apartment
 * @property ServiceBill $serviceBill
 */
class PaymentOrder extends \yii\db\ActiveRecord
{
    const STATUS_CREATE = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'total_amount', 'code'], 'required'],
            [['building_cluster_id', 'apartment_id', 'service_bill_id', 'total_amount', 'status', 'pay_gate', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description', 'transaction_status', 'error_code', 'error_text'], 'string'],
            [['txh_name', 'txt_email', 'txt_phone', 'code'], 'string', 'max' => 255],
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
            'service_bill_id' => Yii::t('common', 'Service Bill ID'),
            'total_amount' => Yii::t('common', 'Total Amount'),
            'txh_name' => Yii::t('common', 'Txh Name'),
            'txt_email' => Yii::t('common', 'Txt Email'),
            'txt_phone' => Yii::t('common', 'Txt Phone'),
            'description' => Yii::t('common', 'Description'),
            'code' => Yii::t('common', 'Code'),
            'status' => Yii::t('common', 'Status'),
            'pay_gate' => Yii::t('common', 'Pay Gate'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
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
    public function getServiceBill()
    {
        return $this->hasOne(ServiceBill::className(), ['id' => 'service_bill_id']);
    }

    //gửi email xác nhận thanh toán thành công
    public function successSendEmail()
    {
        if(!empty($this->apartment) && !empty($this->serviceBill)){
            $buildingCluster = BuildingCluster::findOne(['id' => $this->building_cluster_id]);
            if(!empty($this->apartment->residentUser)){
                if(!empty($this->apartment->residentUser->email)){
                    $email = $this->apartment->residentUser->email;
                    Yii::$app
                        ->mailer
                        ->compose(
                            ['html' => 'paymentSuccess-html'],
                            ['apartment' => $this->apartment, 'serviceBill' => $this->serviceBill]
                        )
                        ->setFrom([Yii::$app->params['supportEmail'] => $buildingCluster->name])
                        ->setTo($email)
                        ->setSubject('Xác nhận thanh toán thành công')
                        ->send();
                }else{
                    Yii::error('Sent email xác thực thanh toán lỗi: email empty');
                }
            }else{
                Yii::error('Sent email xác thực thanh toán lỗi: residentUser empty');
            }
        }else{
            Yii::error('Sent email xác thực thanh toán lỗi');
        }
    }
}
