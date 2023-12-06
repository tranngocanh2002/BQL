<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notify_device_token".
 *
 * @property int $id
 * @property int $resident_user_id
 * @property string $device_toke
 * @property int $created_at
 * @property int $updated_at
 */
class NotifyDeviceToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notify_device_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_user_id', 'created_at', 'updated_at'], 'integer'],
            [['device_toke'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'device_toke' => Yii::t('common', 'Device Toke'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }
}
