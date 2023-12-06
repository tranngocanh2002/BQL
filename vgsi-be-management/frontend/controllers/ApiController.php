<?php

namespace frontend\controllers;

use common\filters\ActionLogFilter;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\FileHelper;
use yii\rest\Controller;
use yii\web\HttpException;
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
        // remove authentication filter
        unset($behaviors['authenticator']);

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
                'Access-Control-Expose-Headers' => ['content-type', 'X-Pagination-Current-Page', 'X-Luci-Api-Key', 'Authorization', 'Content-Type', 'X-Luci-Language', 'Domain-Origin'],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                // them header: -H "Authorization: Bearer access_token"
                HttpBearerAuth::className(),
                // them tham so 'access-token' vao query
                QueryParamAuth::className(),
            ],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                        $route = "/{$action->controller->id}/{$action->id}";
                        if (Yii::$app->user->can($route)) {
                            return true;
                        }
                        return false;
                    }
                ],
            ],
        ];

        $behaviors[] = [
            'class' => ActionLogFilter::className(),
            'scope' => 'API_MANAGEMENT'
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

    /**
     *
     * @return array Danh sach controller
     */
    protected function getControllers()
    {
        $controlers = array();
        $appControllerPath = Yii::$app->getControllerPath();
        //checking existence of controllers directory
        if (is_dir($appControllerPath)) {
            $fileLists = FileHelper::findFiles($appControllerPath);
            //print_R($fileLists);die;
            foreach ($fileLists as $controllerPath) {
                //getting controller name like e.g. 'siteController.php'
                $controllerName = substr($controllerPath, strrpos($controllerPath, DIRECTORY_SEPARATOR) + 1, -4);
                //Clear controller name
                $controllerName = preg_replace("/Controller/", '', $controllerName);
                //Convert to Lower case
                $controllerName = strtolower(preg_replace('/(?<!^)([A-Z])/', '-\\1', $controllerName));
                if (!in_array($controllerName, ["api", "action-log"])) {
                    $controlers[$controllerName] = [
                        'name' => Yii::t('controller', str_replace('-', ' ', $controllerName)),
                        'actions' => $this->getActions($controllerName)
                    ];
                }
            }
        }

        return $controlers;
    }

    /**
     * Get actions of controller
     *
     * @param mixed $controller
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @internal param mixed $module
     */
    protected function getActions($controller)
    {
        $methods = get_class_methods(Yii::$app->createControllerByID($controller));

        // return $methods;
        // var_dump($cInstance->actions());
        $actions = array();
        $pattern = '/^action/';
        foreach ($methods as $method) {
            //preg match start 'action'
            if (strpos($method, 'action') === 0) {
                $actionName = strtolower(preg_replace('/(?<!^)([A-Z])/', '-\\1', str_replace('action', '', $method)));
                $actions[$actionName] = Yii::t('action', str_replace('-', ' ', $actionName));
            }
        }
        //Remover Last element actions
        foreach ($actions as $key => $item) {
            if ($item == 's') {
                unset($actions[$key]);
            }
        }

        return $actions;
    }
}
