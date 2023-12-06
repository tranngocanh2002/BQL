<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\rbac\AssignAuthGroupForm;
use common\models\rbac\AuthGroupResponse;
use common\models\rbac\AuthItem;
use common\models\rbac\AuthItemResponse;
use common\models\rbac\AuthItemUpdateForm;
use common\models\rbac\AuthItemWeb;
use common\models\rbac\AuthGroupCreateForm;
use common\models\rbac\AuthItemWebCreateForm;
use common\models\rbac\AuthGroup;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;

class RbacController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'permissions',
            'auth-item-webs',
            'role-detail',
            'role-create',
            'role-update',
            'create-auth-item-web'
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => [
                'roles',
                'auth-item-webs',
                'create-auth-group',
                'update-auth-group',
                'auth-groups',
                'auth-group-detail',
                'assign-auth-group',
            ],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'roles',
                        'auth-item-webs',
                        'create-auth-group',
                        'update-auth-group',
                        'auth-groups',
                        'auth-group-detail',
                        'assign-auth-group',
                    ],
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @SWG\Get(
     *      path="/rbac/permissions",
     *      operationId="rbac permissions",
     *      summary="rbac permissions",
     *      description="Api List All Permission",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="All Permission Controler/Action",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *         }
     *      },
     * )
     */
    public function actionPermissions()
    {
        $allACL = $this->getControllers();
        if (empty($allACL)) {
            echo "empty";
            die();
        }
        $auth = Yii::$app->authManager;
        $data = [];
        foreach ($allACL as $key => $vals) {
            foreach ($vals as $val) {
                $router = '/' . $key . '/' . $val;
                $permissionExist = $auth->getPermission($router);
                if (!empty($permissionExist)) {
                    $data[] = $permissionExist;
                    continue;
                }
                $permission = $auth->createPermission($router);
                $permission->description = "Permission route {$router}";
                $auth->add($permission);
                $data[] = $auth->getPermission($router);
            }
        }
        $fullPermissionRole = $auth->getRole('FullPermissionRole');
        if(!$fullPermissionRole){
            $fullPermissionRole = $auth->createRole('FullPermissionRole');
            $auth->add($fullPermissionRole);
        }
        $auth->removeChildren($fullPermissionRole);
        $allPermissions = $auth->getPermissions();
        foreach ($allPermissions as $permission){
            $auth->addChild($fullPermissionRole, $permission);
        }
        return $data;
    }

    /**
     * @SWG\Get(
     *      path="/rbac/roles",
     *      operationId="rbac roles",
     *      summary="rbac roles",
     *      description="Api List All Role",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="All Role",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="array",
     *                  @SWG\Items(type="object", ref="#/definitions/AuthItemResponse"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionRoles()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        if(!empty($buildingCluster)){
            $authItemTags = !empty($buildingCluster->auth_item_tags) ? json_decode($buildingCluster->auth_item_tags) : [];
            $data = AuthItemResponse::find()->where(['type' => AuthItem::TYPE_ROLE, 'tag' => $authItemTags])->all();
            return $data;
        }
        return [];
    }

    /**
     * @SWG\Get(
     *      path="/rbac/role-detail?name={name}",
     *      operationId="rbac roles",
     *      summary="rbac roles",
     *      description="Api List All Role",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="name", type="string", default="name", description="name" ),
     *      @SWG\Response(response=200, description="All Role",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object", ref="#/definitions/AuthItemResponse"),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {}
     *         }
     *      },
     * )
     */
    public function actionRoleDetail($name)
    {
        $data = AuthItemResponse::findOne(['name' => $name]);
        return $data;
    }

    /**
     * @SWG\Post(
     *      path="/rbac/role-create",
     *      description="Item check permission in web",
     *      operationId="rbac create-auth-item-web",
     *      summary="rbac create-auth-item-web",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AuthItemUpdateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *         }
     *      },
     * )
     */
    public function actionRoleCreate()
    {
        $model = new AuthItemUpdateForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->create();
    }

    /**
     * @SWG\Post(
     *      path="/rbac/role-update",
     *      description="Item check permission in web",
     *      operationId="rbac create-auth-item-web",
     *      summary="rbac create-auth-item-web",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AuthItemUpdateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *         }
     *      },
     * )
     */
    public function actionRoleUpdate()
    {
        $model = new AuthItemUpdateForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->update();
    }

    protected function getControllers()
    {
        $controllers = array();
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
                if (!in_array($controllerName, ['api', 'swagger', 'error'])) {
                    $controllers[$controllerName] = $this->getActions($controllerName);
                }
            }
        }

        return $controllers;
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

    /**
     * @SWG\Post(
     *      path="/rbac/create-auth-item-web",
     *      description="Item check permission in web",
     *      operationId="rbac create-auth-item-web",
     *      summary="rbac create-auth-item-web",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AuthItemWebCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *         }
     *      },
     * )
     */
    public function actionCreateAuthItemWeb()
    {
        $model = new AuthItemWebCreateForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->create();
    }

    /**
     * @SWG\Get(
     *      path="/rbac/auth-item-webs",
     *      operationId="rbac uth-item-webs",
     *      summary="rbac uth-item-webs",
     *      description="Api List All Auth Item Web",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="All Role",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionAuthItemWebs()
    {
        $data = AuthItemWeb::find()->all();
        return $data;
    }

    /**
     * @SWG\Post(
     *      path="/rbac/create-auth-group",
     *      description="Item check permission in web",
     *      operationId="rbac create-auth-group",
     *      summary="rbac create-auth-group",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AuthGroupCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionCreateAuthGroup()
    {
        $model = new AuthGroupCreateForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->create();
    }

    /**
     * @SWG\Post(
     *      path="/rbac/update-auth-group",
     *      description="Item check permission in web",
     *      operationId="rbac update-auth-group",
     *      summary="rbac update-auth-group",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AuthGroupCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionUpdateAuthGroup()
    {
        $model = new AuthGroupCreateForm();
        $model->setScenario('update');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->update();
    }

    /**
     * @SWG\Get(
     *      path="/rbac/auth-groups",
     *      operationId="rbac auth-groups",
     *      summary="rbac auth-groups",
     *      description="Api List All Auth Group",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="All Auth Group",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="array",
     *                  @SWG\Items(type="object", ref="#/definitions/AuthGroupResponse"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionAuthGroups()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $authGroups = null;
        if(!empty($buildingCluster)){
            $authGroups = AuthGroupResponse::find()->where(['building_cluster_id' => $buildingCluster->id, 'type' => AuthGroup::TYPE_BQL])->orderBy(['updated_at' => SORT_DESC])->all();
        }
        return $authGroups;
    }

    /**
     * @SWG\Post(
     *      path="/rbac/auth-group-delete",
     *      operationId="rbac auth-groups",
     *      summary="rbac auth-groups",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AuthGroupCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionAuthGroupDelete()
    {
        $model = new AuthGroupCreateForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }

    /**
     * @SWG\Get(
     *      path="/rbac/auth-group-detail?id={id}&code={code}",
     *      operationId="rbac auth-groups",
     *      summary="rbac auth-groups",
     *      description="Detail Auth Group",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id" ),
     *      @SWG\Parameter( in="query", name="code", type="string", default="code", description="Code" ),
     *      @SWG\Response(response=200, description="All Auth Group",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="array",
     *                  @SWG\Items(type="object", ref="#/definitions/AuthGroupResponse"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionAuthGroupDetail()
    {
        $id = Yii::$app->request->get("id", 0);
        $code = Yii::$app->request->get("code", '');
        $authGroups = AuthGroupResponse::find();
        if(!empty($id)){
            $authGroups = $authGroups->where(['id' => (int)$id]);
        }
        if(!empty($code)){
            $authGroups = $authGroups->where(['code' => $code]);
        }
        $authGroups = $authGroups->one();
        return $authGroups;
    }

    /**
     * @SWG\Post(
     *      path="/rbac/assign-auth-group",
     *      description="Item check permission in web",
     *      operationId="rbac update-auth-group",
     *      summary="rbac update-auth-group",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Rbac"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AssignAuthGroupForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionAssignAuthGroup()
    {
        $model = new AssignAuthGroupForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->assign();
    }
}
