<?php

namespace resident\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class ApiController extends Controller
{

    const IS_ANDROID = 1;
    const IS_IOS = 2;
    const IS_WEB = 3;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    public $is_web = false;
    public $is_ios = false;
    public $is_android = false;
    public $agent = -1;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                // them header: -H "Authorization: Bearer access_token"
                HttpBearerAuth::className(),
                // them tham so 'access-token' vao query
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                // restrict access to
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Origin' => ['*'],
                'Access-Control-Max-Age' => 3600,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers' => ['content-type', 'X-Pagination-Current-Page', 'X-Luci-Api-Key', 'Authorization', 'Content-Type', 'X-Luci-Language'],
            ],
        ];
        return $behaviors;
    }


    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $HeaderKey = Yii::$app->params['HeaderKey'];
        $this->enableCsrfValidation = false;
        $language = Yii::$app->request->headers->get($HeaderKey['HEADER_LANGUAGE'], 'vi-VN');
        Yii::$app->language = $language;

        $api_key = Yii::$app->request->headers->get($HeaderKey['HEADER_API_KEY']);
        if (!$api_key) {
            throw new UnauthorizedHttpException('Missing api key');
        } else if ($api_key == $HeaderKey['API_KEY_IOS']) {
            $this->is_ios = true;
            $this->agent = static::IS_IOS;
        } else if ($api_key == $HeaderKey['API_KEY_WEB']) {
            $this->is_web = true;
            $this->agent = static::IS_WEB;
        } else if ($api_key == $HeaderKey['API_KEY_ANDROID']) {
            $this->is_android = true;
            $this->agent = static::IS_ANDROID;
        } else {
            throw new UnauthorizedHttpException('Invalid api key');
        }

        Yii::$app->params['agent'] = $this->agent;
        // goi cai nay truoc de trigger event EVENT_BEFORE_ACTION
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
        ];
    }

    /**
     * replace message
     *
     * @param $message
     * @param $params
     * @return mixed
     */
    public static function replaceParam($message, $params)
    {
        if (is_array($params)) {
            $cnt = count($params);
            for ($i = 1; $i <= $cnt; $i++) {
                $message = str_replace('{' . $i . '}', $params[$i - 1], $message);
            }
        }
        return $message;
    }

    /**
     * get value of parameter
     *
     * @param $param_name
     * @param null $default
     * @return mixed
     */
    public function getParameter($param_name, $default = null)
    {
        return \Yii::$app->request->get($param_name, $default);
    }

    /**
     * get value of parameter
     *
     * @param $param_name
     * @param null $default
     * @return mixed
     */
    public function getParameterPost($param_name, $default = null)
    {
        return \Yii::$app->request->post($param_name, $default);
    }

    /**
     * set status code response
     *
     * @param $code
     */
    public function setStatusCode($code)
    {
        Yii::$app->response->setStatusCode($code);
    }

}