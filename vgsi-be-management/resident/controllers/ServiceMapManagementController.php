<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use resident\models\ServiceMapManagementResponse;
use resident\models\ServiceMapManagementSearch;
use Yii;

class ServiceMapManagementController extends ApiController
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
     *      path="/service-map-management/list?apartment_id={apartment_id}",
     *      operationId="ServiceMapManagement list",
     *      summary="ServiceMapManagement list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceMapManagement"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceMapManagementResponse"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionList()
    {
        $modelSearch = new ServiceMapManagementSearch();
        $dataProvider = $modelSearch->search(Yii::$app->request->queryParams);
        return [
            'items' => $dataProvider->getModels(),
        ];
    }

    /**
     * @SWG\Get(
     *      path="/service-map-management/detail?id={id}",
     *      operationId="ServiceMapManagement detail",
     *      summary="ServiceMapManagement",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceMapManagement"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceMapManagement" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceMapManagementResponse"),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *              "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionDetail($id)
    {
        return ServiceMapManagementResponse::findOne(['id' => (int)$id, 'is_deleted' => ServiceMapManagement::NOT_DELETED]);
    }
}
