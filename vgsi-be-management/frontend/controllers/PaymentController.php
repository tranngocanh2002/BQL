<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use frontend\models\PaymentGenCodeSearch;
use frontend\models\PaymentGenCodeForm;
use Yii;

class PaymentController extends ApiController
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
     *      path="/payment/list-code",
     *      operationId="Payment list code",
     *      summary="Payment list code",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Payment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default="", description="apartment id"),
     *      @SWG\Parameter(in="query", name="status", type="integer", description="-1: cư dân hủy yêu cầu, 0: chờ xác nhận, 1: đã hoàn thành, 2: bị từ chối"),
     *      @SWG\Parameter(in="query", name="type", type="integer", description="0- chuyển khoản, 1- thanh toán online"),
     *      @SWG\Parameter(in="query", name="resident_user_id", type="integer"),
     *      @SWG\Parameter(in="query", name="start_date", type="integer"),
     *      @SWG\Parameter(in="query", name="end_date", type="integer"),
     *      @SWG\Parameter(in="query", name="code", type="string", default="", description="code"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/PaymentGenCodeResponse"),
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
    public function actionListCode()
    {
        $modelSearch = new PaymentGenCodeSearch();
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
     * @SWG\Post(
     *      path="/payment/del-code",
     *      operationId="Payment del-code",
     *      summary="Payment del-code",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Payment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="apartment_id", type="integer"),
     *              @SWG\Property(property="code", type="string"),
     *              @SWG\Property(property="reason", type="string", description="lý do"),
     *          ),
     *      ),
     *      @SWG\Response(response=200, description="Hủy mã giao dịch thanh toán -> chỉ người tạo mới có thể hủy",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="message", type="string"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="payment_code", type="string"),
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
    public function actionDelCode()
    {
        $model = new PaymentGenCodeForm();
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->del();
    }
}
