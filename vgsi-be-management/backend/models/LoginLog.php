<?php

namespace backend\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\helpers\MyDatetime;

/**
 * This is the model class for collection "login_log".
 * Kiểm tra nếu trong 2 phút đăng nhập quá 5 lần => bị chặn đăng nhập 5 phút
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property string $ip
 * @property integer $time
 * @property integer $type 1: Backend; 0 api
 */
class LoginLog extends \yii\db\ActiveRecord
{
    const TYPE_BACKEND = 1;
    const TYPE_API = 0;

    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'login_log';
    }

    function rules()
    {
        return [
            [['ip', 'type'], 'required'],
            [['time', 'type'], 'integer'],
            ['type', 'in', 'range' => [self::TYPE_BACKEND, self::TYPE_API]],
            ['ip', 'ip']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'ip',
            'time',
            'type',
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
                    self::EVENT_BEFORE_INSERT => ['time'],
                ]
            ],
        ];
    }

    function validateCountLogin($type)
    {
        $validateLogin = Yii::$app->params['validateLogin'];
        $lastLogin = self::find()
            ->where(['ip' => $this->getRealIpAddr(), 'type' => (int)$type])
            ->orderBy(['time' => SORT_DESC])
            ->one();
        if ($lastLogin == null) {
            return true;
        }
        if ((time() - ($lastLogin->time + $validateLogin['expire'])) >= 0) {
            $this->deleteAll(['ip' => $this->getRealIpAddr(), 'type' => (int)$type]);
            return true;
        }
        $count = self::find()
            ->where(['ip' => (string)$this->getRealIpAddr(), 'type' => (int)$type])
            ->count();

        if ($count >= ($validateLogin['limit'] - 1)) {
            $exp = MyDatetime::numberElapsedString(($lastLogin->time + $validateLogin['expire']) - time());
            return Yii::t('backend', "You are locked out of failed login attempts more than {limit} times. Please try again in {exp}", ['limit' => $validateLogin['limit'], 'exp' => $exp]);
        }
        return true;
    }

    function insertLogFailed($type)
    {
        $this->load(['ip' => $this->getRealIpAddr(), 'type' => $type], '');
        return $this->save();
    }

    function getRealIpAddr()
    {
        if (YII_ENV_TEST) {
            return '127.0.0.1';
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}
