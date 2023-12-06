<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ServiceUtilityBooking;
use frontend\models\ServiceBookingFeeResponse;
use frontend\models\ServiceBookingFeeSearch;
use frontend\models\ServiceUtilityBookingChangeStatusForm;
use frontend\models\ServiceUtilityBookingForm;
use frontend\models\ServiceUtilityBookingReportByDateResponse;
use frontend\models\ServiceUtilityBookingResponse;
use frontend\models\ServiceUtilityBookingSearch;
use Yii;

class ServiceBookingFeeController extends ApiController
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
     *      path="/service-booking-fee/list",
     *      operationId="ServiceBookingFee list",
     *      summary="ServiceBookingFee list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBookingFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="start_time_to", type="integer", default=0, description="Thời gian đến"),
     *      @SWG\Parameter(in="query", name="start_time_from", type="integer", default=0, description="Thời gian từ"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default=0, description="apartment_id"),
     *      @SWG\Parameter(in="query", name="is_paid", type="integer", default=0, description="0 - chưa thanh toán, 1 - đã thanh toán"),
     *      @SWG\Parameter(in="query", name="service_utility_config_id", type="integer", default=0, description="service utility config id"),
     *      @SWG\Parameter(in="query", name="service_utility_free_id", type="integer", default=0, description="service utility free id"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceUtilityBooking <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Danh sách yêu cầu đặt chỗ",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceBookingFeeResponse"),
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
        $modelSearch = new ServiceBookingFeeSearch();
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
     *      path="/service-booking-fee/detail",
     *      operationId="ServiceBookingFee detail",
     *      summary="ServiceBookingFee",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBookingFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceUtilityBooking" ),
     *      @SWG\Response(response=200, description="Chi tiết phí đặt chỗ",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceBookingFeeResponse"),
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
        $post = ServiceBookingFeeResponse::findOne(['id' => $id, 'building_cluster_id' => $buildingCluster->id]);
        return $post;
    }
}
