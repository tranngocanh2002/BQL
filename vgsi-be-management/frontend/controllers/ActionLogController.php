<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use frontend\models\ActionLogResponse;
use frontend\models\ActionLogSearch;
use Yii;
use yii\filters\AccessControl;

class ActionLogController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [];
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
     *      path="/action-log/action-list",
     *      operationId="Action Log action list",
     *      summary="Action Log action list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ActionLog"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="controller", type="object",
     *                      @SWG\Property(property="name", type="string"),
     *                      @SWG\Property(property="actions", type="object",
     *                          @SWG\Property(property="name", type="string"),
     *                      ),
     *                  )
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
    public function actionActionList()
    {
//        return Yii::$app->params['ConfigActionShowLog'];
        return [
            'request' => [
                'name' => Yii::t('action-log', 'request'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'change-status' => Yii::t('action-log', 'change-status'),
                    'add-or-remove-auth-group' => Yii::t('action-log', 'add-or-remove-auth-group'),
                ]
            ],
            'request-category' => [
                'name' => Yii::t('action-log', 'request-category'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'announcement-campaign' => [
                'name' => Yii::t('action-log', 'announcement-campaign'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'active-status' => Yii::t('action-log', 'active-status'),
                ]
            ],
            'announcement-category' => [
                'name' => Yii::t('action-log', 'announcement-category'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service' => [
                'name' => Yii::t('action-log', 'service'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-building-config' => [
                'name' => Yii::t('action-log', 'service-building-config'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-building-fee' => [
                'name' => Yii::t('action-log', 'service-building-fee'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'import' => Yii::t('action-log', 'import'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'change-status' => Yii::t('action-log', 'change-status'),
                ]
            ],
            'service-building-info' => [
                'name' => Yii::t('action-log', 'service-building-info'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'import' => Yii::t('action-log', 'import'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-electric-config' => [
                'name' => Yii::t('action-log', 'service-electric-config'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-electric-fee' => [
                'name' => Yii::t('action-log', 'service-electric-fee'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'import' => Yii::t('action-log', 'import'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'change-status' => Yii::t('action-log', 'change-status'),
                ]
            ],
            'service-electric-info' => [
                'name' => Yii::t('action-log', 'service-electric-info'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'import' => Yii::t('action-log', 'import'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'cancel' => Yii::t('action-log', 'cancel'),
                ]
            ],
            'service-electric-level' => [
                'name' => Yii::t('action-log', 'service-electric-level'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-management-vehicle' => [
                'name' => Yii::t('action-log', 'service-management-vehicle'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'import' => Yii::t('action-log', 'import'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'cancel' => Yii::t('action-log', 'cancel'),
                    'active' => Yii::t('action-log', 'active'),
                ]
            ],
            'service-vehicle-config' => [
                'name' => Yii::t('action-log', 'service-vehicle-config'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-parking-fee' => [
                'name' => Yii::t('action-log', 'service-parking-fee'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'import' => Yii::t('action-log', 'import'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'change-status' => Yii::t('action-log', 'change-status'),
                ]
            ],
            'service-parking-level' => [
                'name' => Yii::t('action-log', 'service-parking-level'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-water-config' => [
                'name' => Yii::t('action-log', 'service-water-config'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-water-fee' => [
                'name' => Yii::t('action-log', 'service-water-fee'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'import' => Yii::t('action-log', 'import'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'change-status' => Yii::t('action-log', 'change-status'),
                ]
            ],
            'service-water-info' => [
                'name' => Yii::t('action-log', 'service-water-info'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'import' => Yii::t('action-log', 'import'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-water-level' => [
                'name' => Yii::t('action-log', 'service-water-level'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'service-bill' => [
                'name' => Yii::t('action-log', 'service-bill'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'change-status' => Yii::t('action-log', 'change-status'),
                    'block' => Yii::t('action-log', 'block'),
                    'cancel' => Yii::t('action-log', 'cancel'),
                    'print' => Yii::t('action-log', 'print'),
                ]
            ],
            'resident-user' => [
                'name' => Yii::t('action-log', 'resident-user'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'import' => Yii::t('action-log', 'import'),
                ]
            ],
            'apartment' => [
                'name' => Yii::t('action-log', 'apartment'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                    'import' => Yii::t('action-log', 'import'),
                    'add-resident-user' => Yii::t('action-log', 'add-resident-user'),
                    'remove-resident-user' => Yii::t('action-log', 'remove-resident-user'),
                ]
            ],
            'building-area' => [
                'name' => Yii::t('action-log', 'building-area'),
                'actions' => [
                    'create' => Yii::t('action-log', 'create'),
                    'update' => Yii::t('action-log', 'update'),
                    'delete' => Yii::t('action-log', 'delete'),
                ]
            ],
            'rbac' => [
                'name' => Yii::t('action-log', 'rbac'),
                'actions' => [
                    'create-auth-group' => Yii::t('action-log', 'create-auth-group'),
                    'update-auth-group' => Yii::t('action-log', 'update-auth-group'),
                    'auth-group-delete' => Yii::t('action-log', 'auth-group-delete'),
                    'create-auth-item-web' => Yii::t('action-log', 'create-auth-item-web'),
                ]
            ],
            'auth' => [
                'name' => Yii::t('action-log', 'auth'),
                'actions' => [
                    'login' => Yii::t('action-log', 'login'),
                    'logout' => Yii::t('action-log', 'logout'),
                    'forgot-password' => Yii::t('action-log', 'forgot-password'),
                    'reset-password' => Yii::t('action-log', 'reset-password'),
                ]
            ],
        ];
    }

    /**
     * @SWG\Get(
     *      path="/action-log/list",
     *      operationId="Action Log list",
     *      summary="Action Log list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ActionLog"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="Tên"),
     *      @SWG\Parameter(in="query", name="status", type="integer", default="", description="Trạng thái"),
     *      @SWG\Parameter(in="query", name="management_user_name", type="string", default="", description="management user name"),
     *      @SWG\Parameter(in="query", name="start_date", type="integer", default=""),
     *      @SWG\Parameter(in="query", name="end_date", type="integer", default=""),
     *      @SWG\Parameter(in="query", name="controller", type="string", default="", description="Đối tượng"),
     *      @SWG\Parameter(in="query", name="action", type="string", default="", description="Thao tác"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ActionLogResponse"),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
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
    public function actionList()
    {
        $buildingSearch = new ActionLogSearch();
        $dataProvider = $buildingSearch->search(Yii::$app->request->queryParams);
        return [
            'items' => $dataProvider->getModels(),
            'pagination' => [
                "totalCount" => $dataProvider->getTotalCount(),
                "pageCount" => $dataProvider->pagination->pageCount,
                "currentPage" => $dataProvider->pagination->page + 1,
                "pageSize" => $dataProvider->pagination->pageSize,
            ]
        ];
    }

    /**
     * @SWG\Get(
     *      path="/action-log/detail",
     *      operationId="Action Log detail",
     *      summary="ActionLog",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ActionLog"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="string", default="5da541bb15336d29b0002c95", description="id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ActionLogResponse"),
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
    public function actionDetail()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $id = Yii::$app->request->get("id", '');
        $post = ActionLogResponse::findOne(['_id' => $id, 'building_cluster_id' => $buildingCluster->id]);
        return $post;
    }
}
