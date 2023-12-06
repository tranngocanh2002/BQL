<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "verify_code".
 *
 * @property int $id
 * @property int $status  0 - chưa verify , 1 - dã verify , 2 - bị hủy
 * @property int $type cấc loại verify khác nhau
 * @property int $expired_at Thời gian hết hạn
 * @property string $code mã verify
 * @property string $payload các thông tin bổ xung
 * @property int $created_at
 * @property int $updated_at
 */
class VerifyCode extends \yii\db\ActiveRecord
{
    const STATUS_NOT_VERIFY = 0;
    const STATUS_VERIFY = 1;
    const STATUS_VERIFY_ERROR = 2;

    const TYPE_LOGIN_RESIDENT_USER = 0;
    const TYPE_FORGOT_PASSWORD_MANAGEMENT_USER = 1;
    const TYPE_FORGOT_PASSWORD_ADMIN = 2;
    const TYPE_CHANGE_PHONE = 3;

    const TIME_RESEND_OTP = 60;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'verify_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'type', 'expired_at', 'created_at', 'updated_at'], 'integer'],
            [['expired_at'], 'required'],
            [['code', 'payload'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'type' => 'Type',
            'expired_at' => 'Expired At',
            'code' => 'Code',
            'payload' => 'Payload',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Generates new code
     */
    public static function generateCode() {
        return Yii::$app->security->generateRandomString() . '_' . time();
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
            ]
        ];
    }
}
