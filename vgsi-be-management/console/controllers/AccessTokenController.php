<?php

namespace console\controllers;

use common\models\ManagementUserAccessToken;
use common\models\ResidentUserAccessToken;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;


class AccessTokenController extends Controller
{
    public function actionDeleteExpired()
    {
        ResidentUserAccessToken::deleteAll(['<', 'expired_at', time()]);
        ManagementUserAccessToken::deleteAll(['<', 'expired_at', time()]);
    }
}