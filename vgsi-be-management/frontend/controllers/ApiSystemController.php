<?php

namespace frontend\controllers;

use yii\rest\Controller;
use yii\web\UnauthorizedHttpException;
use Yii;

class ApiSystemController extends Controller
{
    const HEADER_API_KEY = "system-api-key";

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $api_key = Yii::$app->request->headers->get(static::HEADER_API_KEY);
        if (!$api_key) {
            throw new UnauthorizedHttpException('Missing api key');
        }

        if ($api_key != Yii::$app->params['system-api-key']) {
            throw new UnauthorizedHttpException('Invalid api key');
        }

        // goi cai nay truoc de trigger event EVENT_BEFORE_ACTION
        $res = parent::beforeAction($action);
        return $res;
    }
}