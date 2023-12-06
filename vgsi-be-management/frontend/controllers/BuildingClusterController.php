<?php

namespace frontend\controllers;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use frontend\models\AnnouncementItemSearch;
use frontend\models\BuildingClusterCreateForm;
use frontend\models\BuildingClusterResponse;
use Yii;
use yii\filters\AccessControl;

class BuildingClusterController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'detail'
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['update', 'list-send'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['update', 'list-send'],
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
     * @SWG\Post(
     *      path="/building-cluster/update",
     *      description="building update",
     *      operationId="building update",
     *      summary="building update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"BuildingCluster"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/BuildingClusterCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/BuildingClusterResponse"),
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
    public function actionUpdate()
    {
        $model = new BuildingClusterCreateForm();
        $model->setScenario('update');
        $model->load(Yii::$app->request->bodyParams, '');
        $model->name = trim($model->name);
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
     * @SWG\Post(
     *      path="/building-cluster/detail",
     *      operationId="Building detail",
     *      summary="BuildingCluster",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"BuildingCluster"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/BuildingClusterResponse"),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {}
     *         }
     *      },
     * )
     */
    public function actionDetail()
    {
        $domain = ApiHelper::getDomainOrigin();
        $post = BuildingClusterResponse::findOne(['domain' => $domain]);
        return $post;
    }

    /**
     * @SWG\Get(
     *      path="/building-cluster/list-send",
     *      operationId="BuildingCluster list",
     *      summary="BuildingCluster list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"BuildingCluster"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="text_search", type="string"),
     *      @SWG\Parameter(in="query", name="start_time", type="integer"),
     *      @SWG\Parameter(in="query", name="end_time", type="integer"),
     *      @SWG\Parameter(in="query", name="type_send", type="integer", default="", description="1 - email, 2 - sms, 3 - notify"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/AnnouncementItemTotalSendResponse"),
     *                  ),
     *                  @SWG\Property(property="total_count", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="total_limit", type="integer", default=1),
     *                          @SWG\Property(property="total_send", type="integer", default=1),
     *                      ),
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
    public function actionListSend()
    {
        $modelSearch = new AnnouncementItemSearch();
        $data = $modelSearch->searchListSend(Yii::$app->request->queryParams);
        $dataCount = $data['dataCount'];
        $dataProvider = $data['dataProvider'];
        return [
            'items' => $dataProvider->getModels(),
            'total_count' => $dataCount,
            'pagination' => [
                "totalCount" => $dataProvider->getTotalCount(),
                "pageCount" => $dataProvider->pagination->pageCount,
                "currentPage" => $dataProvider->pagination->page + 1,
                "pageSize" => $dataProvider->pagination->pageSize,
            ]
        ];
    }
}
