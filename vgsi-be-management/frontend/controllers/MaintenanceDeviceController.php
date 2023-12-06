<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\MaintenanceDevice;
use frontend\models\MaintenanceDeviceSearch;
use frontend\models\MaintenanceDeviceCreateForm;
use frontend\models\MaintenanceDeviceDeleteForm;
use frontend\models\MaintenanceDeviceConfirmationForm;
use frontend\models\MaintenanceDeviceResponse;
use frontend\models\MaintenanceDeviceImportForm;
use Yii;

class MaintenanceDeviceController extends ApiController
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
     * @SWG\Post(
     *      path="/maintenance-device/create",
     *      operationId="MaintenanceDevice create",
     *      summary="MaintenanceDevice create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"MaintenanceDevice"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/MaintenanceDeviceCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/MaintenanceDeviceResponse"),
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
        $model = new MaintenanceDeviceCreateForm();
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
     *      path="/maintenance-device/update",
     *      description="MaintenanceDevice update",
     *      operationId="MaintenanceDevice update",
     *      summary="MaintenanceDevice update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"MaintenanceDevice"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/MaintenanceDeviceCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/MaintenanceDeviceResponse"),
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
        $model = new MaintenanceDeviceCreateForm();
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
     *      path="/maintenance-device/delete",
     *      operationId="MaintenanceDevice delete",
     *      summary="MaintenanceDevice delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"MaintenanceDevice"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/MaintenanceDeviceDeleteForm"),
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
        $model = new MaintenanceDeviceDeleteForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->delete();
    }

    /**
     * @SWG\Get(
     *      path="/maintenance-device/list",
     *      operationId="MaintenanceDevice list",
     *      summary="MaintenanceDevice list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"MaintenanceDevice"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="code", type="string", default="", description="mã thiết bị"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="tên thiết bị"),
     *      @SWG\Parameter(in="query", name="type", type="integer", default="", description="loại thiết bị: 0 - máy tính, 1 - quạt, 2 - camera, 3 - đèn, 4 - thang máy"),
     *      @SWG\Parameter(in="query", name="status", type="integer", default="", description="trạng thái: 0 - ngừng hoạt động, 1- đang hoạt động"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên MaintenanceDevice <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/MaintenanceDeviceResponse"),
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
        $modelSearch = new MaintenanceDeviceSearch();
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
     *      path="/maintenance-device/list-schedule",
     *      operationId="MaintenanceDevice list schedule",
     *      summary="MaintenanceDevice list schedule",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"MaintenanceDevice"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="code", type="string", default="", description="mã thiết bị"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="tên thiết bị"),
     *      @SWG\Parameter(in="query", name="type", type="integer", default="", description="loại thiết bị: 0 - máy tính, 1 - quạt, 2 - camera, 3 - đèn, 4 - thang máy"),
     *      @SWG\Parameter(in="query", name="start_time", type="integer", default="", description="thời gian bắt đầu"),
     *      @SWG\Parameter(in="query", name="end_time", type="integer", default="", description="thời gian kết thúc"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên MaintenanceDevice <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/MaintenanceDeviceResponse"),
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
    public function actionListSchedule()
    {
        $modelSearch = new MaintenanceDeviceSearch();
        $dataProvider = $modelSearch->searchSchedule(Yii::$app->request->queryParams);
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
     *      path="/maintenance-device/detail?id={id}",
     *      operationId="MaintenanceDevice detail",
     *      summary="MaintenanceDevice",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"MaintenanceDevice"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id MaintenanceDevice" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/MaintenanceDeviceResponse"),
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
    public function actionDetail()
    {
        $user = Yii::$app->user->getIdentity();
        $id = Yii::$app->request->get("id", 0);
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        return MaintenanceDeviceResponse::findOne(['id' => $id, 'building_cluster_id' => $user->building_cluster_id, 'is_deleted' => MaintenanceDevice::NOT_DELETED]);
    }

    /**
     * @SWG\Post(
     *      path="/maintenance-device/confirmation",
     *      description="MaintenanceDevice confirmation",
     *      operationId="MaintenanceDevice confirmation",
     *      summary="MaintenanceDevice confirmation",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"MaintenanceDevice"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/MaintenanceDeviceConfirmationForm"),
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
    public function actionConfirmation()
    {
        $model = new MaintenanceDeviceConfirmationForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->isConfirmation();
    }

    /**
     * @SWG\Get(
     *      path="/maintenance-device/export",
     *      description="MaintenanceDevice export",
     *      operationId="MaintenanceDevice export",
     *      summary="MaintenanceDevice export",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"MaintenanceDevice"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="code", type="string", default="", description="mã thiết bị"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="tên thiết bị"),
     *      @SWG\Parameter(in="query", name="type", type="integer", default="", description="loại thiết bị: 0 - máy tính, 1 - quạt, 2 - camera, 3 - đèn, 4 - thang máy"),
     *      @SWG\Parameter(in="query", name="status", type="integer", default="", description="trạng thái: 0 - ngừng hoạt động, 1- đang hoạt động"),
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
        $ManagementUserSearch = new MaintenanceDeviceSearch();
        return $ManagementUserSearch->search(Yii::$app->request->queryParams, true);
    }

    /**
     * @SWG\Get(
     *      path="/maintenance-device/export-schedule",
     *      description="MaintenanceDevice export schedule",
     *      operationId="MaintenanceDevice export schedule",
     *      summary="MaintenanceDevice export schedule",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"MaintenanceDevice"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="code", type="string", default="", description="mã thiết bị"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="tên thiết bị"),
     *      @SWG\Parameter(in="query", name="type", type="integer", default="", description="loại thiết bị: 0 - máy tính, 1 - quạt, 2 - camera, 3 - đèn, 4 - thang máy"),
     *      @SWG\Parameter(in="query", name="start_time", type="integer", default="", description="thời gian bắt đầu"),
     *      @SWG\Parameter(in="query", name="end_time", type="integer", default="", description="thời gian kết thúc"),
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
    public function actionExportSchedule()
    {
        $ManagementUserSearch = new MaintenanceDeviceSearch();
        return $ManagementUserSearch->searchSchedule(Yii::$app->request->queryParams, true);
    }

    /**
     * @SWG\Get(
     *      path="/maintenance-device/gen-form",
     *      description="Maintenance import",
     *      operationId="Maintenance import",
     *      summary="Maintenance import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Maintenance"},
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
        $model = new MaintenanceDeviceImportForm();
        return $model->genForm();
    }

     /**
     * @SWG\Post(
     *      path="/maintenance-device/import-form",
     *      description="maintenance import",
     *      operationId="maintenance import",
     *      summary="maintenance import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"maintenance"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/maintenanceImportForm"),
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
    public function actionImportForm()
    {
        // return true;
        $model = new MaintenanceDeviceImportForm();
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
}
