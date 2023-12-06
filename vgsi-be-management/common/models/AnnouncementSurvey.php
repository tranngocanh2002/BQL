<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "announcement_survey".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property float|null $apartment_capacity
 * @property int $announcement_campaign_id
 * @property int $resident_user_id
 * @property int|null $status 0: chưa làm khảo sát, 1: đồng ý, 2: không đồng ý
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ResidentUser $residentUser
 * @property Apartment $apartment
 */
class AnnouncementSurvey extends \yii\db\ActiveRecord
{
    const STATUS_DEFAULT = 0;
    const STATUS_AGREE = 1; // đồng ý
    const STATUS_DISAGREE = 2; // không đồng ý

    public $total_answer;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'announcement_survey';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'building_area_id', 'apartment_id', 'announcement_campaign_id', 'resident_user_id'], 'required'],
            [['building_cluster_id', 'building_area_id', 'apartment_id', 'announcement_campaign_id', 'resident_user_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['apartment_capacity'], 'number'],
            [['total_answer'], 'safe'],
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
            'apartment_capacity' => Yii::t('common', 'Apartment Capacity'),
            'announcement_campaign_id' => Yii::t('common', 'Announcement Campaign ID'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'status' => Yii::t('common', 'Status'),
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
    public function getResidentUser()
    {
        return $this->hasOne(ResidentUser::className(), ['id' => 'resident_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }
}
