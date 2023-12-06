<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "help".
 *
 * @property int $id
 * @property string $title
 * @property string $title_en
 * @property string $content
 * @property string $medias
 * @property int $help_category_id
 * @property int $is_deleted 0 : chưa xóa, 1 : đã xóa
 * @property int $order
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property helpCategory $helpCategory
 */
class Help extends \yii\db\ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'help';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'title_en'], 'required'],
            [['content', 'medias'], 'string'],
            [['order', 'help_category_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
            'title' => Yii::t('common', 'Title'),
            'title_en' => Yii::t('common', 'Title En'),
            'content' => Yii::t('common', 'Content'),
            'medias' => Yii::t('common', 'Medias'),
            'help_category_id' => Yii::t('common', 'Help Category ID'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'order' => Yii::t('common', 'Order'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHelpCategory()
    {
        return $this->hasOne(HelpCategory::className(), ['id' => 'help_category_id']);
    }

}
