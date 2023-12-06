<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ServiceDebt;
use common\models\ServicePaymentFee;
use frontend\models\ServiceDebtReminderResponse;
use frontend\models\ServiceDebtResponse;
use frontend\models\ServiceDebtSearch;
use Yii;

class ServiceDebtController extends ApiController
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
        ];
    }
    
    /**
     * @SWG\Get(
     *      path="/service-debt/list",
     *      operationId="ServiceDebt list",
     *      summary="ServiceDebt list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceDebt"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="month", type="integer", default="", description="Month" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="building_area_id", type="integer", default="1", description="building area id" ),
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="status" ),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceDebt <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceDebtResponse"),
     *                  ),
     *                  @SWG\Property(property="total_count", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="early_debt", type="integer", default=1),
     *                          @SWG\Property(property="end_debt", type="integer", default=1),
     *                          @SWG\Property(property="receivables", type="integer", default=1),
     *                          @SWG\Property(property="collected", type="integer", default=1),
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
    public function actionList()
    {
        $modelSearch = new ServiceDebtSearch();
        $data = $modelSearch->search(Yii::$app->request->queryParams);
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

    /**
     * @SWG\Get(
     *      path="/service-debt/detail",
     *      operationId="ServiceDebt detail",
     *      summary="ServiceDebt",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceDebt"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceDebt" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceDebtResponse"),
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
    public function actionDetail($id)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $post = ServiceDebtResponse::findOne(['id' => $id, 'building_cluster_id' => $buildingCluster->id]);
        return $post;
    }

    /**
     * @SWG\Get(
     *      path="/service-debt/list-reminder",
     *      operationId="ServiceDebt list-reminder",
     *      summary="ServiceDebt list-reminder",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceDebt"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="type", type="integer", default="1", description="Lấy danh sách nhắc nợ: 1 - thông báo phí, 2 - nhắc lần 1, 3 - nhắc lần 2, 4 - nhắc lần 3, 5 - thông báo tạm dừng dịch vụ" ),
     *      @SWG\Parameter(in="query", name="building_area_ids", type="string", default="1,2", description="building area ids"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceDebt <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceDebtReminderResponse"),
     *                  ),
     *                  @SWG\Property(property="total_count", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="total_apartment", type="integer", default=1),
     *                          @SWG\Property(property="total_email", type="integer", default=1),
     *                          @SWG\Property(property="total_app", type="integer", default=1),
     *                          @SWG\Property(property="total_sms", type="integer", default=1),
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
    public function actionListReminder()
    {
        $modelSearch = new ServiceDebtSearch();
        $data = $modelSearch->searchReminder(Yii::$app->request->queryParams);
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

    /**
     * @SWG\Get(
     *      path="/service-debt/export",
     *      operationId="ServiceDebt export",
     *      summary="ServiceDebt export",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceDebt"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="month", type="integer", default="", description="Month" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="building_area_id", type="integer", default="1", description="building area id" ),
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="status" ),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceDebt <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="file_path", type="string"),
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
    public function actionExport()
    {
        $modelSearch = new ServiceDebtSearch();
        return $modelSearch->search(Yii::$app->request->queryParams, true);
    }
}
