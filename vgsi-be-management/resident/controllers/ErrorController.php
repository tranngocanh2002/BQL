<?php

namespace resident\controllers;

use Exception;
use Firebase\JWT\ExpiredException;
use http\Url;
use Mpdf\Tag\Header;
use Yii;
use yii\base\UserException;
use yii\web\HttpException;
use yii\web\Response;
use yii\rest\Controller;
use common\models\City;

class ErrorController extends Controller
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                // restrict access to
//                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Origin' => ['*'],
                // Allow only POST and PUT methods
//                'Access-Control-Allow-Credentials' => true,
                // Allow OPTIONS caching
                'Access-Control-Max-Age' => 3600,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page', 'X-Luci-Api-Key', 'Authorization', 'Content-Type', 'X-Luci-Language'],
            ],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        Yii::info('actionIndex');
        // Yii::$app->response->statusCode = 404;
        $res = [
            'name' => "Not Found",
            'message' => "Path not found!",
            'code' => 0,
            'status' => 404,
            'type' => "yii\base\Exception"
        ];

        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            return [];
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
            $res['status'] = $code;
        } else {
            $code = $exception->getCode();
        }
        $res['code'] = 0;

        if ($exception instanceof Exception) {
            $name = $exception->getName();
        } else {
            $name = isset($this->defaultName) ?: Yii::t('yii', 'Error');
        }
        if ($code) {
            $name .= " (#$code)";
        }

        $res['name'] = $name;

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = isset($this->defaultMessage) ?: Yii::t('yii', 'An internal server error occurred.');
        }
        $res['message'] = $message;
        return $res;

    }
}
