<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use backendQltt\models\LoggerUser;
use backendQltt\models\LogBehavior;

/**
 * This is the model class for table "announcement_template".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property string $content_email
 * @property string $content_app
 * @property string $content_sms
 * @property string $name
 * @property string $name_en
 * @property string $image
 * @property int $type
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property string $content_pdf
 *
 * @property BuildingCluster $buildingCluster
 */
class AnnouncementTemplate extends \yii\db\ActiveRecord
{
    const TYPE_0 = 0;
    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;
    const TYPE_5 = 5;
    const TYPE_6 = 6;
    const TYPE_POST_NEWS = -1;

    public static $type_list = [
        self::TYPE_0 => "Thông  báo thường",
        self::TYPE_1 => "Thông  báo phí",
        self::TYPE_2 => "Nhắc nợ lần 1",
        self::TYPE_3 => "Nhắc nợ lần 2",
        self::TYPE_4 => "Nhắc nợ lần 3",
        self::TYPE_5 => "Nhắc nợ lần 4",
        self::TYPE_6 => "Thông báo ngừng dịch vụ",
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'announcement_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name', 'name_en', 'content_email'], 'required'],
            [['building_cluster_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type'], 'integer'],
            [['content_email', 'content_app', 'content_sms', 'content_pdf', 'image', 'name', 'name_en'], 'string'],
            ['name', 'string', 'max' => 255],
            ['name_en', 'string', 'max' => 255],
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
            'type' => Yii::t('common', 'Type'),
            'name' => Yii::t('common', 'Name'),
            'name_en' => Yii::t('common', 'Name (En)'),
            'image' => Yii::t('common', 'Image'),
            'content_email' => Yii::t('common', 'Content Email'),
            'content_app' => Yii::t('common', 'Content App'),
            'content_sms' => Yii::t('common', 'Content Sms'),
            'content_pdf' => Yii::t('common', 'Content Pdf'),
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
            ],
            // 'log' => [
            //     'class' => LogBehavior::class,
            // ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }
}
