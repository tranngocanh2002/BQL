<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "puri_trak_history".
 *
 * @property int $id
 * @property int $puri_trak_id
 * @property double $aqi
 * @property double $h
 * @property double $t
 * @property int $time
 * @property string $device_id
 * @property string $name
 * @property double $lat
 * @property double $long
 * @property int $hours
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class PuriTrakHistory extends \yii\db\ActiveRecord
{
    const DEVICE_ID = 'd000000000005';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'puri_trak_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['puri_trak_id', 'time', 'hours', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['aqi', 'h', 't', 'lat', 'long'], 'number'],
            [['device_id', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'puri_trak_id' => Yii::t('common', 'Puri Trak ID'),
            'aqi' => Yii::t('common', 'Aqi'),
            'h' => Yii::t('common', 'H'),
            't' => Yii::t('common', 'T'),
            'time' => Yii::t('common', 'Time'),
            'device_id' => Yii::t('common', 'Device ID'),
            'name' => Yii::t('common', 'Name'),
            'lat' => Yii::t('common', 'Lat'),
            'long' => Yii::t('common', 'Long'),
            'hours' => Yii::t('common', 'Hours'),
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
}
