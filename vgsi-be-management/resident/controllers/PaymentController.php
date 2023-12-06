<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\PaymentGenCode;
use resident\models\HelpCategorySearch;
use resident\models\PaymentGenCodeForm;
use resident\models\PaymentGenCodeSearch;
use resident\models\ServiceBillForm;
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
     *      @SWG\Parameter(in="query", name="status", type="integer", description="0- chưa thanh toán, 1- đã thanh toán"),
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
     *      path="/payment/gen-code",
     *      operationId="Payment gen-code",
     *      summary="Payment gen-code",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Payment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/PaymentGenCodeForm"),
     *      ),
     *      @SWG\Response(response=200, description="Tạo mã giao dịch thanh toán",
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
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionGenCode()
    {
        $model = new PaymentGenCodeForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->gen();
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
     *              @SWG\Property(property="reason", type="string"),
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

     /**
     * @SWG\Post(
     *      path="/payment/approve-bill-vnpay",
     *      operationId="payment create",
     *      summary="ServiceBill create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceBillForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="message", type="string"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceBillResponse")
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
    public function actionApproveBillVnpay()
    {
        $model = new ServiceBillForm();
        // $model->setScenario('create');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            Yii::error($model->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->approvebillvnpay();
        // return true;
    }
}
