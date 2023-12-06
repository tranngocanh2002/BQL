<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "resident_user_access_token".
 *
 * @property int $id
 * @property int $resident_user_id
 * @property string $token
 * @property string $token_hash
 * @property int $type 0 - access token, 1 - refresh token
 * @property int $expired_at Thời gian hết hạn
 * @property int $created_at
 * @property int $updated_at
 */
class ResidentUserAccessToken extends \yii\db\ActiveRecord
{
    const TYPE_ACCESS_TOKEN = 0;
    const TYPE_REFRESH_TOKEN = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resident_user_access_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_user_id', 'type', 'expired_at', 'created_at', 'updated_at'], 'integer'],
            [['token', 'token_hash'], 'string', 'max' => 1000],
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
            'token' => Yii::t('common', 'Token'),
            'token_hash' => Yii::t('common', 'Token Hash'),
            'type' => Yii::t('common', 'Type'),
            'expired_at' => Yii::t('common', 'Expired At'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
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
        ];
    }

    public function setTokenHash()
    {
        $this->token_hash = md5($this->token);
    }
}
