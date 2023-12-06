<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\Request;
use common\models\RequestMapAuthGroup;
use frontend\models\RequestAddOrRemoveAuthGroupForm;
use frontend\models\RequestChangeStatusForm;
use frontend\models\RequestMapAuthGroupResponse;
use frontend\models\RequestResponse;
use frontend\models\RequestSearch;
use Yii;

class RequestController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
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
     *      path="/request/list",
     *      operationId="Request list",
     *      summary="Request list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Request"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="title", type="string", default="", description="Tên"),
     *      @SWG\Parameter(in="query", name="status", type="integer", default="", description="Trạng thái"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default="", description="apartment id"),
     *      @SWG\Parameter(in="query", name="request_category_id", type="integer", default="", description="request category id"),
     *      @SWG\Parameter(in="query", name="resident_user_id", type="integer", default="", description="resident user id"),
     *      @SWG\Parameter(in="query", name="start_time_from", type="integer", default="", description="Ngày tạo"),
     *      @SWG\Parameter(in="query", name="start_time_to", type="integer", default="", description="Ngày tạo"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên Request <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/RequestResponse"),
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
        $requestSearch = new RequestSearch();
        $dataProvider = $requestSearch->search(Yii::$app->request->queryParams);
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
     *      path="/request/detail?id={id}",
     *      operationId="Request detail",
     *      summary="Request",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Request"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id Request" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/RequestResponse"),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *              "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionDetail()
    {
        $user = Yii::$app->user->getIdentity();
        $id = Yii::$app->request->get("id", 0);
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        //check quyen xem chi tiet
        $requestMapAuthGroup = RequestMapAuthGroup::findOne(['request_id' => $id, 'auth_group_id' => $user->auth_group_id]);
        if(empty($requestMapAuthGroup)){
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Bạn không có quyền truy cập chức năng này"),
                'statusCode' => ErrorCode::ERROR_PERMISSION_DENIED,
            ];
        }
        $request= RequestResponse::findOne(['id' => $id, 'building_cluster_id' => $user->building_cluster_id]);
        return $request;
    }

    /**
     * @SWG\Post(
     *      path="/request/add-or-remove-auth-group",
     *      operationId="Request add-or-remove-auth-group",
     *      description="Thêm hoạc loại bỏ nhóm quyền xử lý yêu cầu",
     *      summary="Request add-remove-auth-group",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Request"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/RequestAddOrRemoveAuthGroupForm"),
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
    public function actionAddOrRemoveAuthGroup()
    {
        $model = new RequestAddOrRemoveAuthGroupForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->addOrRemove();
    }

    /**
     * @SWG\Post(
     *      path="/request/change-status",
     *      operationId="Request change-status",
     *      summary="Request change-status",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Request"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/RequestChangeStatusForm"),
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
    public function actionChangeStatus()
    {
        $model = new RequestChangeStatusForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->changeStatus();
    }

    /**
     * @SWG\Get(
     *      path="/request/list-request-map-auth-group?request_id={request_id}",
     *      operationId="Request map-auth-group",
     *      summary="Request map-auth-group",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Request"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="request_id", type="integer", default="", description="request id"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/RequestMapAuthGroupResponse"),
     *                  ),
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
    public function actionListRequestMapAuthGroup()
    {
        $request_id = Yii::$app->request->get("request_id", 0);
        return [
            'items' => RequestMapAuthGroupResponse::find()->where(['request_id' => (int)$request_id])->all(),
        ];
    }
}
