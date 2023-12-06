<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "building_area".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $medias
 * @property int $status  0 - chưa active, 1 - đã active
 * @property int $is_deleted 0 : chưa xóa, 1 : đã xóa
 * @property int $building_cluster_id
 * @property int $parent_id
 * @property string $parent_path
 * @property string $short_name
 * @property int $type
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class BuildingArea extends \yii\db\ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    const TYPE_FLOOR = 0;
    const TYPE_BUILDING = 1;
    const TYPE_AREA = 2;

    public static $arrType = [
        self::TYPE_FLOOR => "Tầng",
        self::TYPE_BUILDING => "Tòa nhà",
        self::TYPE_AREA => "Khu",
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'building_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_path', 'description', 'medias'], 'string'],
            [['type', 'status', 'is_deleted', 'building_cluster_id', 'parent_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['short_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Name'),
            'description' => Yii::t('common', 'Description'),
            'medias' => Yii::t('common', 'Medias'),
            'status' => Yii::t('common', 'Status'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'parent_id' => Yii::t('common', 'Parent ID'),
            'parent_path' => Yii::t('common', 'Parent Path'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'short_name' => Yii::t('common', 'Short Name'),
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
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->name = trim($this->name);
        $this->short_name = trim($this->short_name);
        return true;
    }

    public function setParentPath()
    {
        if (empty($this->parent_id)) {
            $this->parent_path = null;
        } else {
            $this->parent_path = self::getParentPath($this->parent_id, '');
        }
    }

    private function getParentPath($parent_id, $path)
    {
        $parent = self::findOne(['id' => $parent_id]);
        $path_new = $parent->name . '/' . $path;
        if (empty($parent->parent_id)) {
            return trim($path_new);
        } else {
            return self::getParentPath($parent->parent_id, $path_new);
        }
    }
}
