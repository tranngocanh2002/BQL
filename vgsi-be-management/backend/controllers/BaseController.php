<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use common\models\User;
use common\models\UserRole;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\filters\AccessControl;

class BaseController extends \yii\web\Controller
{

    public $menuLeftItems = array();

    /**
     * @return array
     */
    public function behaviors()
    {
        Yii::$app->language = (isset($_COOKIE['language']) && $_COOKIE['language'] != 'en') ? 'vi-VN' : 'en-US';

        $user_id = Yii::$app->user->id;

        $is_access = false;

        $controller_id = Yii::$app->controller->id; //test
        $action_id = Yii::$app->controller->action->id; //index
        //Check if user is login
        if (isset($user_id)) {

            //Get data
            $role_id = User::findOne($user_id)->role_id;
            $permission = Json::decode(UserRole::findOne($role_id)->permission);

            if (ArrayHelper::keyExists($controller_id, $permission)) {
                $is_access = in_array($action_id, $permission[$controller_id]);
            }

            $this->menuLeftItems = [
                [
                    'label' => Yii::t('backend', 'Building Cluster'),
                    'icon' => 'fas fa-cloud',
                    'url' => '/building-cluster',
                    'visible' => isset($permission['building-cluster']) && in_array('index', $permission['building-cluster']) == true,
                    'active' => strpos(Yii::$app->request->url, 'building-cluster') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Payment Config'),
                    'icon' => 'fas fa-cloud',
                    'url' => '/payment-config',
                    'visible' => isset($permission['payment-config']) && in_array('index', $permission['payment-config']) == true,
                    'active' => strpos(Yii::$app->request->url, 'payment-config') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Help Category'),
                    'icon' => 'fas fa-cogs',
                    'url' => '/help-category',
                    'visible' => isset($permission['help-category']) && in_array('index', $permission['help-category']) == true,
                    'active' => strpos(Yii::$app->request->url, 'help-category') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Help'),
                    'icon' => 'fas fa-cogs',
                    'url' => '/help',
                    'visible' => isset($permission['help']) && in_array('index', $permission['help']) == true,
                    'active' => strpos(Yii::$app->request->url, 'help') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Service'),
                    'icon' => 'fas fa-cogs',
                    'url' => '/service',
                    'visible' => isset($permission['service']) && in_array('index', $permission['service']) == true,
                    'active' => strpos(Yii::$app->request->url, 'service') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Service Bill Template'),
                    'icon' => 'fas fa-cogs',
                    'url' => '/service-bill-template',
                    'visible' => isset($permission['service-bill-template']) && in_array('index', $permission['service-bill-template']) == true,
                    'active' => strpos(Yii::$app->request->url, 'service-bill-template') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Announcement Template'),
                    'icon' => 'fas fa-cogs',
                    'url' => '/announcement-template',
                    'visible' => isset($permission['announcement-template']) && in_array('index', $permission['announcement-template']) == true,
                    'active' => strpos(Yii::$app->request->url, 'announcement-template') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Management User'),
                    'icon' => 'far fa-id-card',
                    'url' => '/management-user',
                    'visible' => isset($permission['management-user']) && in_array('index', $permission['management-user']) == true,
                    'active' => strpos(Yii::$app->request->url, 'management-user') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Management Auth Group'),
                    'icon' => 'key',
                    'url' => '/auth-group',
                    'visible' => isset($permission['auth-group']) && in_array('index', $permission['auth-group']) == true,
                    'active' => strpos(Yii::$app->request->url, 'auth-group') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Management User Role'),
                    'icon' => 'key',
                    'url' => '/auth-item',
                    'visible' => isset($permission['auth-item']) && in_array('index', $permission['auth-item']) == true,
                    'active' => strpos(Yii::$app->request->url, 'auth-item') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'User manager'),
                    'icon' => 'users',
                    'url' => '/user',
                    'visible' => isset($permission['user']) && in_array('index', $permission['user']) == true,
                    'active' => strpos(Yii::$app->request->url, 'user') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Permission'),
                    'icon' => 'key',
                    'url' => '/user-role',
                    'visible' => isset($permission['user-role']) && in_array('index', $permission['user-role']) == true,
                    'active' => strpos(Yii::$app->request->url, 'user-role') ? true : false,
                ],
                [
                    'label' => Yii::t('language', 'Language'),
                    'items' => [
                        ['label' => Yii::t('language', 'List of languages'), 'url' => ['/translatemanager/language/list']],
                        ['label' => Yii::t('language', 'Create'), 'url' => ['/translatemanager/language/create']],
                        ['label' => Yii::t('language', 'Scan'), 'url' => ['/translatemanager/language/scan']],
                        ['label' => Yii::t('language', 'Optimize'), 'url' => ['/translatemanager/language/optimizer']],
                        ['label' => Yii::t('language', 'Im-/Export'),
                            'items' => [
                                ['label' => Yii::t('language', 'Import'), 'url' => ['/translatemanager/language/import']],
                                ['label' => Yii::t('language', 'Export'), 'url' => ['/translatemanager/language/export']],
                            ]
                        ]
                    ]
                ],
            ];
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => $is_access,
                            'denyCallback' => function ($rule, $action) {
                                Yii::$app->session->setFlash('error', '403: You are not allowed access this function');
                                if (Yii::$app->request->referrer) {
                                    return $this->redirect(Yii::$app->request->referrer);
                                } else {
                                    return $this->goHome();
                                }
                            },
                            'roles' => ['@'],
                        ]
                    ],
                ]
            ];
        } else {
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['login', 'logout', 'error'],
                            'allow' => true,
                            'roles' => ['?'],
                        ],
                        [
                            'allow' => $is_access,
                            'roles' => ['@'],
                        ],
                    ],
                ]
            ];
        }
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
                if ($controllerName != "base") {
                    $controlers[$controllerName] = $this->getActions($controllerName);
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
                $actions[$actionName] = $actionName;
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
