<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "resident_user_device_token".
 *
 * @property int $id
 * @property int $resident_user_id id user resident
 * @property string $device_token token nhận notify push theo từng device
 * @property int $resident_user_access_token_id
 * @property int $created_at
 * @property int $updated_at
 */
class ResidentUserDeviceToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resident_user_device_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_user_id'], 'required'],
            [['resident_user_id', 'resident_user_access_token_id', 'created_at', 'updated_at'], 'integer'],
            [['device_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'resident_user_access_token_id' => Yii::t('common', 'Resident User Access Token Id'),
            'device_token' => Yii::t('common', 'Device Token'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
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
        ];
    }
}
