<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_debt".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $early_debt Nợ đầu kỳ
 * @property int $end_debt Nợ cuối kỳ
 * @property int $receivables Phát sinh phải thu
 * @property int $collected Phát sinh đã thu
 * @property int $month công nợ của tháng
 * @property int $status 0 - không nợ, 1 - còn nợ, 2 - thông báo phí, 3 - nhắc nợ lần 1, 4 - nhắc nợ lần 2, 5 - nhắc nợ lần 3, 6 - thông báo tạm dừng dịch vụ
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $type 0 - tháng cũ, 1 - tháng hiện tại
 *
 * @property Apartment $apartment
 */
class ServiceDebt extends \yii\db\ActiveRecord
{
    const STATUS_PREPAID = -1; // Trả trước
    const STATUS_PAID = 0;
    const STATUS_UNPAID = 1;
    const STATUS_UNPAID_1 = 2;
    const STATUS_UNPAID_2 = 3;
    const STATUS_UNPAID_3 = 4;
    const STATUS_UNPAID_4 = 5;
    const STATUS_UNPAID_5 = 6;

    public static $status_lst = [
        self::STATUS_PREPAID => "Trả trước",
        self::STATUS_PAID => "Không nợ",
        self::STATUS_UNPAID => "Còn nợ",
        self::STATUS_UNPAID_1 => "Thông báo phí",
        self::STATUS_UNPAID_2 => "Nhắc nợ lần 1",
        self::STATUS_UNPAID_3 => "Nhắc nợ lần 2",
        self::STATUS_UNPAID_4 => "Nhắc nợ lần 3",
        self::STATUS_UNPAID_5 => "Thông báo tạm dừng dịch vụ",
    ];

    public static $status_color = [
        self::STATUS_PREPAID => '#050ED3',
        self::STATUS_PAID => '#159C1F',
        self::STATUS_UNPAID => '#FF9900',
        self::STATUS_UNPAID_1 => '#FF3333',
        self::STATUS_UNPAID_2 => '#BC0409',
        self::STATUS_UNPAID_3 => '#97040B',
        self::STATUS_UNPAID_4 => '#650205',
        self::STATUS_UNPAID_5 => '#650205',
    ];

    const TYPE_OLD_MONTH = 0;
    const TYPE_CURRENT_MONTH = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_debt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'apartment_id'], 'required'],
            [['type', 'building_cluster_id', 'building_area_id', 'apartment_id', 'early_debt', 'end_debt', 'receivables', 'collected', 'month', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
            'early_debt' => Yii::t('common', 'Early Debt'),
            'end_debt' => Yii::t('common', 'End Debt'),
            'receivables' => Yii::t('common', 'Receivables'),
            'collected' => Yii::t('common', 'Collected'),
            'month' => Yii::t('common', 'Month'),
            'status' => Yii::t('common', 'Status'),
            'type' => Yii::t('common', 'Type'),
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

    public static function countTotalSend($building_cluster_id, $status){
        $total_apartment = Apartment::find()->where(['building_cluster_id' => $building_cluster_id, 'reminder_debt' => $status, 'is_deleted' => Apartment::NOT_DELETED])
            ->andWhere(['not', ['resident_user_id' => null]])->count();

        $count_email = ResidentUser::find()->select(["COUNT(resident_user.email) as email"])
            ->join('LEFT JOIN','apartment', 'resident_user.id = apartment.resident_user_id')
            ->where(['apartment.building_cluster_id' => $building_cluster_id, 'apartment.reminder_debt' => $status, 'resident_user.is_deleted' => ResidentUser::NOT_DELETED, 'apartment.is_deleted' => Apartment::NOT_DELETED])
            ->andWhere(['not', ['resident_user.email' => null]])
            ->andWhere(['<>', 'resident_user.email', ''])->one();

        $count_app = ResidentUser::find()->select(["COUNT(resident_user.active_app) as active_app"])
            ->join('LEFT JOIN','apartment', 'resident_user.id = apartment.resident_user_id')
            ->where(['apartment.building_cluster_id' => $building_cluster_id, 'resident_user.active_app' => ResidentUser::ACTIVE_APP, 'apartment.reminder_debt' => $status, 'resident_user.is_deleted' => ResidentUser::NOT_DELETED, 'apartment.is_deleted' => Apartment::NOT_DELETED])->one();

        $count_phone = ResidentUser::find()->select(["COUNT(resident_user.phone) as phone"])
            ->join('LEFT JOIN','apartment', 'resident_user.id = apartment.resident_user_id')
            ->where(['apartment.building_cluster_id' => $building_cluster_id, 'apartment.reminder_debt' => $status, 'resident_user.is_deleted' => ResidentUser::NOT_DELETED, 'apartment.is_deleted' => Apartment::NOT_DELETED])
            ->andWhere(['not', ['resident_user.phone' => null]])
            ->andWhere(['<>', 'resident_user.phone', ''])->one();

        return [
            'total_apartment' => (int)$total_apartment,
            'total_email' => (int)$count_email->email,
            'total_app' => (int)$count_app->active_app,
            'total_sms' => (int)$count_phone->phone,
        ];
    }
}
