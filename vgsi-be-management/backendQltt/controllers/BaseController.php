<?php

namespace backendQltt\controllers;

use Yii;
use yii\helpers\Json;
use common\models\User;
use common\models\UserRole;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;

class BaseController extends \yii\web\Controller
{

    public $menuLeftItems = array();

    public function init()
    {
        parent::init();

    }

    /**
     * @return array
     */
    public function behaviors()
    {
        Yii::$app->language = (isset($_COOKIE['language']) && $_COOKIE['language'] != 'en') ? 'vi-VN' : 'en-US';

        $user_id = Yii::$app->user->id;
        //$user_id = 1;

        $is_access = false;

        $controller_id = Yii::$app->controller->id; //test
        $action_id = Yii::$app->controller->action->id; //index
        //Check if user is login
        if (isset($user_id)) {
            if (Yii::$app->user->identity->logged == User::NOT_LOGGED) {
                Yii::$app->user->logout();

                return $this->goHome();
            }

            //Get data
            $role_id = User::findOne($user_id)->role_id;
            $permission = Json::decode(UserRole::findOne($role_id)->permission);

            if (ArrayHelper::keyExists($controller_id, $permission)) {
                $is_access = in_array($action_id, $permission[$controller_id]);
            }

            $this->menuLeftItems = [
                [
                    'label' => Yii::t('backendQltt', 'Project'),
                    'icon' => 'fas fa-cloud',
                    'url' => '/building-cluster',
                    'visible' => isset($permission['building-cluster']) && in_array('index', $permission['building-cluster']) == true,
                    'active' => strpos(Yii::$app->request->url, 'building-cluster') ? true : false,
                ],
                [
                    'label' => Yii::t('backendQltt', 'Tin tức'),
                    'icon' => 'fw fa-newspaper-o',
                    'url' => '/announcement-campaign',
                    'visible' => isset($permission['announcement-campaign']) && in_array('index', $permission['announcement-campaign']) == true,
                    'active' => strpos(Yii::$app->request->url, 'announcement-campaign') ? true : false,
                ],
                [
                    'label' => Yii::t('backendQltt', 'User manager'),
                    'icon' => 'users',
                    'url' => '/user',
                    'visible' => isset($permission['user']) && in_array('index', $permission['user']) == true,
                    'active' => !strpos(Yii::$app->request->url, 'user-role') && !strpos(Yii::$app->request->url, 'user/profile') && !strpos(Yii::$app->request->url, 'apartment-map-resident-user') && !strpos(Yii::$app->request->url, 'logger-user') && strpos(Yii::$app->request->url, 'user') ? true : false,

                ],
                [
                    'label' => Yii::t('backendQltt', 'App cư dân'),
                    'icon' => 'far fa-id-card',
                    'url' => '/apartment-map-resident-user',
                    'visible' => isset($permission['apartment-map-resident-user']) && in_array('index', $permission['apartment-map-resident-user']) == true,
                    'active' => strpos(Yii::$app->request->url, 'apartment-map-resident-user') ? true : false,
                ],
                [
                    'label' => Yii::t('backend', 'Cấu hình'),
                    'icon' => 'far fa-gear',
                    'items' => [
                        [
                            'label' => Yii::t('backendQltt', 'Announcement Template'),
                            'icon' => 'fw fa-file-word-o',
                            'url' => '/announcement-template',
                            'visible' => isset($permission['announcement-template']) && in_array('index', $permission['announcement-template']) == true,
                            'active' => strpos(Yii::$app->request->url, 'announcement-template') ? true : false,
                        ],
                        [
                            'label' => Yii::t('backendQltt', 'Nhóm quyền'),
                            'icon' => 'fw fa-th-list',
                            'url' => '/user-role',
                            'visible' => isset($permission['user-role']) && in_array('index', $permission['user-role']) == true,
                            'active' => strpos(Yii::$app->request->url, 'user-role') ? true : false,

                        ],
                        [
                            'label' => Yii::t('backendQltt', 'User history'),
                            'icon' => 'history',
                            'url' => '/logger-user',
                            'visible' => isset($permission['logger-user']) && in_array('index', $permission['logger-user']) == true,
                            'active' => strpos(Yii::$app->request->url, 'logger-user') ? true : false,
                        ],
                    ]
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
                    'label' => Yii::t('backend', 'Announcement Category'),
                    'icon' => 'fas fa-cloud',
                    'url' => '/announcement-category',
                    'visible' => isset($permission['announcement-category']) && in_array('index', $permission['announcement-category']) == true,
                    'active' => strpos(Yii::$app->request->url, 'announcement-category') ? true : false,
                ],

                [
                    'label' => Yii::t('backend', 'Management User'),
                    'icon' => 'far fa-id-card',
                    'url' => '/management-user',
                    'visible' => isset($permission['management-user']) && in_array('index', $permission['management-user']) == true,
                    'active' => strpos(Yii::$app->request->url, 'management-user') ? true : false,
                ],

                [
                    'label' => Yii::t('backend', 'Management User Role'),
                    'icon' => 'key',
                    'url' => '/auth-item',
                    'visible' => isset($permission['auth-item']) && in_array('index', $permission['auth-item']) == true,
                    'active' => strpos(Yii::$app->request->url, 'auth-item') ? true : false,
                ],
                // [
                //     'label' => Yii::t('backend', 'Permission'),
                //     'icon' => 'key',
                //     'url' => '/user-role',
                //     'visible' => isset($permission['user-role']) && in_array('index', $permission['user-role']) == true,
                //     'active' => strpos(Yii::$app->request->url, 'user-role') ? true : false,
                // ],
                // [
                //     'label' => Yii::t('language', 'Language'),
                //     'items' => [
                //         ['label' => Yii::t('language', 'List of languages'), 'url' => ['/translatemanager/language/list']],
                //         ['label' => Yii::t('language', 'Create'), 'url' => ['/translatemanager/language/create']],
                //         ['label' => Yii::t('language', 'Scan'), 'url' => ['/translatemanager/language/scan']],
                //         ['label' => Yii::t('language', 'Optimize'), 'url' => ['/translatemanager/language/optimizer']],
                //         [
                //             'label' => Yii::t('language', 'Im-/Export'),
                //             'items' => [
                //                 ['label' => Yii::t('language', 'Import'), 'url' => ['/translatemanager/language/import']],
                //                 ['label' => Yii::t('language', 'Export'), 'url' => ['/translatemanager/language/export']],
                //             ]
                //         ]
                //     ]
                // ],
            ];
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => $is_access,
                            'denyCallback' => function ($rule, $action) {
                                Yii::$app->session->setFlash('error', Yii::t('common', '403: You are not allowed access this function'));
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

    protected function getControllersQltt()
    {
        Yii::t('backendQltt', 'building-cluster');
        Yii::t('backendQltt', 'announcement-campaign');
        Yii::t('backendQltt', 'user');
        Yii::t('backendQltt', 'apartment-map-resident-user');
        Yii::t('backendQltt', 'user-role');
        Yii::t('backendQltt', 'announcement-template');

        return [
            'building-cluster' => [
                'index' => Yii::t('backendQltt', 'building-cluster-index'),
                'view' => Yii::t('backendQltt', 'building-cluster-view'),
                'create' => Yii::t('backendQltt', 'building-cluster-create'),
                'update' => Yii::t('backendQltt', 'building-cluster-update'),
                'delete' => Yii::t('backendQltt', 'building-cluster-delete'),
            ],
            'announcement-campaign' => [
                'index' => Yii::t('backendQltt', 'announcement-campaign-index'),
                'view' => Yii::t('backendQltt', 'announcement-campaign-view'),
                'create' => Yii::t('backendQltt', 'announcement-campaign-create'),
                'update' => Yii::t('backendQltt', 'announcement-campaign-update'),
                'delete' => Yii::t('backendQltt', 'announcement-campaign-delete'),
            ],
            'user' => [
                'index' => Yii::t('backendQltt', 'user-index'),
                'view' => Yii::t('backendQltt', 'user-view'),
                'create' => Yii::t('backendQltt', 'user-create'),
                'reset-password' => Yii::t('backendQltt', 'user-reset-password'),
                'delete' => Yii::t('backendQltt', 'user-delete'),
                'inactive' => Yii::t('backendQltt', 'user-inactive'),
                'update' => Yii::t('backendQltt', 'user-update'),
                'import-file' => Yii::t('backendQltt', 'user-import-file'),
                'export-file' => Yii::t('backendQltt', 'user-export-file'),
            ],
            'apartment-map-resident-user' => [
                'index' => Yii::t('backendQltt', 'apartment-map-resident-user-index'),
            ],
            'user-role' => [
                'index' => Yii::t('backendQltt', 'user-role-index'),
                'create' => Yii::t('backendQltt', 'user-role-create'),
                'update' => Yii::t('backendQltt', 'user-role-update'),
                'delete' => Yii::t('backendQltt', 'user-role-delete'),
            ],
            'announcement-template' => [
                'index' => Yii::t('backendQltt', 'announcement-template-index'),
                'view' => Yii::t('backendQltt', 'announcement-template-view'),
                'create' => Yii::t('backendQltt', 'announcement-template-create'),
                'update' => Yii::t('backendQltt', 'announcement-template-update'),
                'delete' => Yii::t('backendQltt', 'announcement-template-delete'),
            ],
            'logger-user' => [
                'index' => Yii::t('backendQltt', 'logger-user-view'),
                // 'view' => Yii::t('backendQltt', 'logger-user-view'),
                // 'create' => Yii::t('backendQltt', 'logger-user-create'),
                // 'update' => Yii::t('backendQltt', 'logger-user-update'),
                // 'delete' => Yii::t('backendQltt', 'logger-user-delete'),
            ],
        ];
    }

    /**
     * Check permission
     *
     * @param $controller
     * @param $action
     *
     * @return boolen
     */
    public function checkPermission($controller, $action)
    {
        $userId = Yii::$app->user->id;
        if (isset($userId)) {
            //Get data
            $roleId = User::findOne($userId)->role_id;
            $permission = Json::decode(UserRole::findOne($roleId)->permission);
            return isset($permission[$controller]) && in_array($action, $permission[$controller]);
        } else {
            return false;
        }
    }
}