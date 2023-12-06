<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ServicePaymentFee;
use frontend\models\ServicePaymentFeeForm;
use frontend\models\ServicePaymentFeeImportForm;
use frontend\models\ServicePaymentFeeIsDraftForm;
use frontend\models\ServicePaymentFeeResponse;
use frontend\models\ServicePaymentFeeSearch;
use Yii;

class ServicePaymentFeeController extends ApiController
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
     * @SWG\Post(
     *      path="/service-payment-fee/create",
     *      operationId="ServicePaymentFee create",
     *      summary="ServicePaymentFee create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServicePaymentFeeForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="message", type="string"),
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
    public function actionCreate()
    {
        //không cho phép tạo phí trung, phải tạo phí của từng dịch vụ và duyệt lên mới có phí thanh toán
        Yii::warning('không cho phép tạo phí trung, phải tạo phí của từng dịch vụ và duyệt lên mới có phí thanh toán');
        return [
            'success' => false,
            'message' => Yii::t('frontend', "Invalid data"),
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
        ];

        $model = new ServicePaymentFeeForm();
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
     *      path="/service-payment-fee/import",
     *      description="ServicePaymentFee import",
     *      operationId="ServicePaymentFee import",
     *      summary="ServicePaymentFee import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServicePaymentFeeImportForm"),
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
    public function actionImport()
    {
        //không cho phép tạo phí trung, phải tạo phí của từng dịch vụ và duyệt lên mới có phí thanh toán
        Yii::warning('không cho phép tạo phí trung, phải tạo phí của từng dịch vụ và duyệt lên mới có phí thanh toán');
        return [
            'success' => false,
            'message' => Yii::t('frontend', "Invalid data"),
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
        ];

        $model = new ServicePaymentFeeImportForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->import();
    }

    /**
     * @SWG\Get(
     *      path="/service-payment-fee/gen-form",
     *      description="ServicePaymentFee import",
     *      operationId="ServicePaymentFee import",
     *      summary="ServicePaymentFee import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
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
    public function actionGenForm()
    {
        //không cho phép tạo phí trung, phải tạo phí của từng dịch vụ và duyệt lên mới có phí thanh toán
        Yii::warning('không cho phép tạo phí trung, phải tạo phí của từng dịch vụ và duyệt lên mới có phí thanh toán');
        return [
            'success' => false,
            'message' => Yii::t('frontend', "Invalid data"),
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
        ];

        $model = new ServicePaymentFeeImportForm();
        return $model->genForm();
    }

    /**
     * @SWG\Post(
     *      path="/service-payment-fee/update",
     *      description="ServicePaymentFee update",
     *      operationId="ServicePaymentFee update",
     *      summary="ServicePaymentFee update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServicePaymentFeeForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServicePaymentFeeResponse"),
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
        //không cho phép tạo phí trung, phải tạo phí của từng dịch vụ và duyệt lên mới có phí thanh toán
//        Yii::warning('không cho phép tạo phí trung, phải tạo phí của từng dịch vụ và duyệt lên mới có phí thanh toán');
//        return [
//            'success' => false,
//            'message' => Yii::t('frontend', "Invalid data"),
//            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//        ];

        $model = new ServicePaymentFeeForm();
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
     * @SWG\Post(
     *      path="/service-payment-fee/delete",
     *      operationId="ServicePaymentFee delete",
     *      summary="ServicePaymentFee delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServicePaymentFeeForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="total_all", type="integer", default=0),
     *                  @SWG\Property(property="total_del", type="integer", default=0),
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
    public function actionDelete()
    {
        $model = new ServicePaymentFeeForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }
    
    /**
     * @SWG\Get(
     *      path="/service-payment-fee/list",
     *      operationId="ServicePaymentFee list",
     *      summary="ServicePaymentFee list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="service map management id" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="building_area_id", type="integer", default="1", description="building area id" ),
     *      @SWG\Parameter( in="query", name="is_draft", type="integer", default="0", description="is draft : 1 - là nháp, 0 - không phải nháp" ),
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="status" ),
     *      @SWG\Parameter( in="query", name="month", type="integer", default="2019", description="month" ),
     *      @SWG\Parameter( in="query", name="year", type="integer", default="07", description="year" ),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter( in="query", name="from_month", type="integer", default="1", description="from_month" ),
     *      @SWG\Parameter( in="query", name="to_month", type="integer", default="1", description="to_month" ),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServicePaymentFee <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServicePaymentFeeResponse"),
     *                  ),
     *                  @SWG\Property(property="total_count", type="object",
     *                      @SWG\Property(property="total_price", type="integer", default=0),
     *                      @SWG\Property(property="total_money_collected", type="integer", default=0),
     *                      @SWG\Property(property="total_more_money_collecte", type="integer", default=0),
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
        $modelSearch = new ServicePaymentFeeSearch();
        $data = $modelSearch->search(Yii::$app->request->queryParams);
        $dataCount = $data['dataCount'];
        $dataProvider = $data['dataProvider'];
        return [
            'items' => $dataProvider->getModels(),
            'total_count' => [
                'total_price' => (int)$dataCount->price,
                'total_money_collected' => (int)$dataCount->money_collected,
                'total_more_money_collecte' => (int)$dataCount->more_money_collecte,
            ],
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
     *      path="/service-payment-fee/detail?id={id}",
     *      operationId="ServicePaymentFee detail",
     *      summary="ServicePaymentFee",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServicePaymentFee" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServicePaymentFeeResponse"),
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
    public function actionDetail($id)
    {
        $user = Yii::$app->user->getIdentity();
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $post = ServicePaymentFeeResponse::findOne(['id' => $id, 'building_cluster_id' => $user->building_cluster_id]);
        return $post;
    }

    /**
     * @SWG\Post(
     *      path="/service-payment-fee/is-draft",
     *      description="ServicePaymentFee is-draft",
     *      operationId="ServicePaymentFee is-draft",
     *      summary="ServicePaymentFee is-draft",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServicePaymentFeeIsDraftForm"),
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
    public function actionIsDraft()
    {
        $model = new ServicePaymentFeeIsDraftForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->isDraft();
    }

    /**
     * @SWG\Get(
     *      path="/service-payment-fee/debt",
     *      operationId="ServicePaymentFee debt",
     *      summary="ServicePaymentFee debt",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="building_area_id", type="integer", default="1", description="building area id" ),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>-building_area_id</b>: tầng"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="array",
     *                  @SWG\Items(type="object",
     *                      @SWG\Property(property="apartment_id", type="integer"),
     *                      @SWG\Property(property="apartment_name", type="string"),
     *                      @SWG\Property(property="apartment_parent_path", type="string"),
     *                      @SWG\Property(property="apartment_building_area_id", type="integer"),
     *                      @SWG\Property(property="total_debt", type="integer"),
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
    public function actionDebt()
    {
        $modelSearch = new ServicePaymentFeeSearch();
        return $modelSearch->searchDebt(Yii::$app->request->queryParams);
    }

    /**
     * @SWG\Get(
     *      path="/service-payment-fee/export",
     *      operationId="ServicePaymentFee export",
     *      summary="ServicePaymentFee export",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServicePaymentFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="service map management id" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="building_area_id", type="integer", default="1", description="building area id" ),
     *      @SWG\Parameter( in="query", name="is_draft", type="integer", default="0", description="is draft : 1 - là nháp, 0 - không phải nháp" ),
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="status" ),
     *      @SWG\Parameter( in="query", name="month", type="integer", default="2019", description="month" ),
     *      @SWG\Parameter( in="query", name="year", type="integer", default="07", description="year" ),
     *      @SWG\Parameter( in="query", name="from_month", type="integer", default="1", description="from_month" ),
     *      @SWG\Parameter( in="query", name="to_month", type="integer", default="1", description="to_month" ),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServicePaymentFee <br/><b>+-status</b>: Trạng thái"),
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
        $modelSearch = new ServicePaymentFeeSearch();
        return $modelSearch->search(Yii::$app->request->queryParams, true);
    }

}
