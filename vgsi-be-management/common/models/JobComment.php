<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "job_comment".
 *
 * @property int $id
 * @property string|null $content
 * @property string|null $content_en
 * @property string|null $medias
 * @property int $job_id
 * @property int $building_cluster_id
 * @property int $type
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class JobComment extends \yii\db\ActiveRecord
{
    const TYPE_COMMENT = 0;
    const TYPE_HISTORY = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'job_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'content_en', 'medias'], 'string'],
            [['job_id', 'building_cluster_id'], 'required'],
            [['job_id', 'building_cluster_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'content' => Yii::t('common', 'Content'),
            'content_en' => Yii::t('common', 'Content En'),
            'medias' => Yii::t('common', 'Medias'),
            'job_id' => Yii::t('common', 'Job ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
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
        ];
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $time = time();
        $sql = Yii::$app->db;
        $sql->createCommand("UPDATE job set updated_at = $time where id = $this->job_id")->execute();
    }
}
