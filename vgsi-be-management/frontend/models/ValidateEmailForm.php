<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use Yii;
use yii\base\Model;
use yii\db\Exception;

class ValidateEmailForm extends Model
{
    public $email;
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
        ];
    }
}
