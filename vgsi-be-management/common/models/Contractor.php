<?php

namespace common\models;

use common\helpers\CUtils;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "contractor".
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $description
 * @property string|null $attach
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $contact_email
 * @property int $building_cluster_id
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $is_deleted
 */
class Contractor extends \yii\db\ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    public static $status_list = [
        self::STATUS_OFF => "Dừng hoạt động",
        self::STATUS_ON => "Đang hoạt động"
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contractor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address', 'description', 'contact_name', 'contact_phone', 'contact_email', 'building_cluster_id'], 'required'],
            [['attach'], 'string'],
            [['building_cluster_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['name', 'address'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1000],
            [['contact_name', 'contact_email'], 'string', 'max' => 50],
            [['contact_phone'], 'string', 'max' => 11],
            [['contact_phone'], 'validateMobile'],
            [['contact_email'], 'validateEmail'],
//            [['name', 'building_cluster_id'], 'unique', 'targetAttribute' => ['name'], 'message' => Yii::t('common', "Contractor name has exist!")],
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'invalid phone number'));
        }
    }

    public function validateEmail($attribute, $params, $validator)
    {
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if (!preg_match($pattern,$this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'invalid email'));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Name'),
            'address' => Yii::t('common', 'Address'),
            'description' => Yii::t('common', 'Description'),
            'attach' => Yii::t('common', 'Attach'),
            'contact_name' => Yii::t('common', 'Contact Name'),
            'contact_phone' => Yii::t('common', 'Contact Phone'),
            'contact_email' => Yii::t('common', 'Contact Email'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'status' => Yii::t('common', 'Status'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
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
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
    }
}
