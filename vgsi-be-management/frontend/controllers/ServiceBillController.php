<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ServiceBill;
use common\models\ServiceBillItem;
use common\models\ServicePaymentFee;
use frontend\models\ServiceBillChangeStatusForm;
use frontend\models\ServiceBillForm;
use frontend\models\ServiceBillInvoiceForm;
use frontend\models\ServiceBillItemResponse;
use frontend\models\ServiceBillPrint;
use frontend\models\ServiceBillResponse;
use frontend\models\ServiceBillSearch;
use Yii;

class ServiceBillController extends ApiController
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
     *      path="/service-bill/create",
     *      operationId="ServiceBill create",
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
    public function actionCreate()
    {
        $model = new ServiceBillForm();
        $model->setScenario('create');
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
        return $model->create();
    }

    /**
     * @SWG\Post(
     *      path="/service-bill/create-invoice",
     *      operationId="ServiceBill create invoice",
     *      summary="ServiceBill create invoice",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceBillInvoiceForm"),
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
    public function actionCreateInvoice()
    {
        $model = new ServiceBillInvoiceForm();
        $model->setScenario('create');
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
        return $model->create();
    }

    /**
     * @SWG\Post(
     *      path="/service-bill/update",
     *      description="ServiceBill update",
     *      operationId="ServiceBill update",
     *      summary="ServiceBill update",
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
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceBillResponse"),
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
        $model = new ServiceBillForm();
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
     *      path="/service-bill/update-invoice",
     *      description="ServiceBill update invoice",
     *      operationId="ServiceBill update invoice",
     *      summary="ServiceBill update invoice",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceBillInvoiceForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceBillResponse"),
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
    public function actionUpdateInvoice()
    {
        $model = new ServiceBillInvoiceForm();
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
     *      path="/service-bill/change-status",
     *      operationId="ServiceBill change-status",
     *      summary="ServiceBill change-status",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceBillChangeStatusForm"),
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
        $model = new ServiceBillChangeStatusForm();
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
     * @SWG\Post(
     *      path="/service-bill/block",
     *      operationId="ServiceBill block",
     *      summary="ServiceBill block",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="ids", type="array",
     *                  @SWG\Items(type="integer")
     *              ),
     *          ),
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
    public function actionBlock()
    {
        $model = new ServiceBillChangeStatusForm();
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->block();
    }

    /**
     * @SWG\Post(
     *      path="/service-bill/cancel",
     *      operationId="ServiceBill cancel",
     *      summary="ServiceBill cancel",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceBillChangeStatusForm"),
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
    public function actionCancel()
    {
        $model = new ServiceBillChangeStatusForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->cancel();
    }

    /**
     * @SWG\Post(
     *      path="/service-bill/delete",
     *      operationId="ServiceBill delete",
     *      summary="ServiceBill delete",
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
    public function actionDelete()
    {
        $model = new ServiceBillForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }

    /**
     * @SWG\Post(
     *      path="/service-bill/delete-invoice",
     *      operationId="ServiceBill delete invoice",
     *      summary="ServiceBill delete invoice",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceBillInvoiceForm"),
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
    public function actionDeleteInvoice()
    {
        $model = new ServiceBillInvoiceForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }

    /**
     * @SWG\Get(
     *      path="/service-bill/list",
     *      operationId="ServiceBill list",
     *      summary="ServiceBill list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="management_user_name", type="string", default="", description="management user name" ),
     *      @SWG\Parameter( in="query", name="resident_user_name", type="string", default="", description="resident user name" ),
     *      @SWG\Parameter( in="query", name="payer_name", type="string", default="", description="payer name" ),
     *      @SWG\Parameter( in="query", name="number", type="string", default="", description="number" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="building_area_id", type="integer", default="1", description="building area id" ),
     *      @SWG\Parameter( in="query", name="start_time", type="integer", default="1", description="start time" ),
     *      @SWG\Parameter( in="query", name="end_time", type="integer", default="1", description="end time" ),
     *      @SWG\Parameter( in="query", name="type_payment", type="integer", default="1", description="type payment" ),
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="status" ),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter( in="query", name="type", type="integer", default="0", description="0 - Phiếu thu,  1 - Phiếu chi" ),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceBill <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceBillResponse"),
     *                  ),
     *                  @SWG\Property(property="total_count", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="type_payment", type="integer", default=1),
     *                          @SWG\Property(property="total_price", type="integer", default=1),
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
        $modelSearch = new ServiceBillSearch();
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
     *      path="/service-bill/list-by-receptionist",
     *      operationId="ServiceBill list",
     *      summary="ServiceBill list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="management_user_name", type="string", default="", description="management user name" ),
     *      @SWG\Parameter( in="query", name="resident_user_name", type="string", default="", description="resident user name" ),
     *      @SWG\Parameter( in="query", name="payer_name", type="string", default="", description="payer name" ),
     *      @SWG\Parameter( in="query", name="number", type="string", default="", description="number" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="building_area_id", type="integer", default="1", description="building area id" ),
     *      @SWG\Parameter( in="query", name="start_time", type="integer", default="1", description="start time" ),
     *      @SWG\Parameter( in="query", name="end_time", type="integer", default="1", description="end time" ),
     *      @SWG\Parameter( in="query", name="type_payment", type="integer", default="1", description="type payment" ),
     *      @SWG\Parameter( in="query", name="type", type="integer", default="0", description="0 - Phiếu thu,  1 - Phiếu chi" ),
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="status" ),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceBill <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceBillResponse"),
     *                  ),
     *                  @SWG\Property(property="total_count", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="type_payment", type="integer", default=1),
     *                          @SWG\Property(property="total_price", type="integer", default=1),
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
    public function actionListByReceptionist()
    {
        $user = Yii::$app->user->getIdentity();
        $modelSearch = new ServiceBillSearch();
        $modelSearch->management_user_id = $user->id;
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
     *      path="/service-bill/detail?id={id}",
     *      operationId="ServiceBill detail",
     *      summary="ServiceBill",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceBill" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceBillResponse"),
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
        $post = ServiceBillResponse::findOne(['id' => $id, 'building_cluster_id' => $user->building_cluster_id]);
        return $post;
    }

    /**
     * @SWG\Get(
     *      path="/service-bill/print?id={id}",
     *      operationId="ServiceBill detail",
     *      summary="ServiceBill",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceBill" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="content_html", type="string"),
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
    public function actionPrint($id)
    {
        $this->layout = 'print';
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $serviceBill = ServiceBill::findOne(['id' => (int)$id, 'building_cluster_id' => $buildingCluster->id]);
        if(empty($serviceBill)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $serviceBillItems = ServiceBillItem::find()->where(['service_bill_id' => $serviceBill->id])->all();
        $content_html = $this->render('print', [
            'buildingCluster' => $buildingCluster,
            'serviceBill' => $serviceBill,
            'serviceBillItems' => $serviceBillItems,
        ]);
        return [
            'success' => true,
            'content_html' => $content_html,
        ];
    }

    /**
     * @SWG\Get(
     *      path="/service-bill/export",
     *      operationId="ServiceBill export",
     *      summary="ServiceBill export",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBill"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="management_user_name", type="string", default="", description="management user name" ),
     *      @SWG\Parameter( in="query", name="resident_user_name", type="string", default="", description="resident user name" ),
     *      @SWG\Parameter( in="query", name="payer_name", type="string", default="", description="payer name" ),
     *      @SWG\Parameter( in="query", name="number", type="string", default="", description="number" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="building_area_id", type="integer", default="1", description="building area id" ),
     *      @SWG\Parameter( in="query", name="start_time", type="integer", default="1", description="start time" ),
     *      @SWG\Parameter( in="query", name="end_time", type="integer", default="1", description="end time" ),
     *      @SWG\Parameter( in="query", name="type_payment", type="integer", default="1", description="type payment" ),
     *      @SWG\Parameter( in="query", name="type", type="integer", default="0", description="0 - Phiếu thu,  1 - Phiếu chi" ),
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="status" ),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceBill <br/><b>+-status</b>: Trạng thái"),
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
        $modelSearch = new ServiceBillSearch();
        return $modelSearch->search(Yii::$app->request->queryParams, true);
    }
}
