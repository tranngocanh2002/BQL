<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use resident\models\ServiceWaterFeeResponse;
use resident\models\ServiceWaterFeeSearch;
use Yii;

class ServiceWaterFeeController extends ApiController
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
     *      path="/service-water-fee/list",
     *      operationId="ServiceWaterFee list",
     *      summary="ServiceWaterFee list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="service map management id" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceWaterFee <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceWaterFeeResponse"),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
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
        $modelSearch = new ServiceWaterFeeSearch();
        $dataProvider = $modelSearch->search(Yii::$app->request->queryParams);
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
     *      path="/service-water-fee/detail?id={id}",
     *      operationId="ServiceWaterFee detail",
     *      summary="ServiceWaterFee",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceWaterFee" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceWaterFeeResponse"),
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
    public function actionDetail($id)
    {
        return ServiceWaterFeeResponse::findOne(['id' => (int)$id]);
    }

}
