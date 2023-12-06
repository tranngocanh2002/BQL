<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "management_user_device_token".
 *
 * @property int $id
 * @property int $management_user_id id user management
 * @property string $device_token token nhận notify push theo từng device
 * @property int $management_user_access_token_id
 * @property int $type
 * @property int $created_at
 * @property int $updated_at
 */
class ManagementUserDeviceToken extends \yii\db\ActiveRecord
{
    const TYPE_WEB = 0;
    const TYPE_APP = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'management_user_device_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['management_user_id'], 'required'],
            [['management_user_id', 'management_user_access_token_id', 'created_at', 'updated_at', 'type'], 'integer'],
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
            'management_user_id' => Yii::t('common', 'Management User ID'),
            'management_user_access_token_id' => Yii::t('common', 'Management User Access Token Id'),
            'device_token' => Yii::t('common', 'Device Token'),
            'type' => Yii::t('common', 'Type'),
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
