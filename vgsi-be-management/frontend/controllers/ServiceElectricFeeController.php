<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ServiceElectricFee;
use common\models\ServiceElectricInfo;
use common\models\ServiceElectricLevel;
use frontend\models\ServiceElectricFeeChangeStatusForm;
use frontend\models\ServiceElectricFeeForm;
use frontend\models\ServiceElectricFeeImportForm;
use frontend\models\ServiceElectricFeeResponse;
use frontend\models\ServiceElectricFeeSearch;
use Yii;

class ServiceElectricFeeController extends ApiController
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
     *      path="/service-electric-fee/create",
     *      operationId="ServiceElectricFee create",
     *      summary="ServiceElectricFee create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceElectricFeeForm"),
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
        $model = new ServiceElectricFeeForm();
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
     *      path="/service-electric-fee/update",
     *      description="ServiceElectricFee update",
     *      operationId="ServiceElectricFee update",
     *      summary="ServiceElectricFee update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceElectricFeeForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceElectricFeeResponse"),
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
        $model = new ServiceElectricFeeForm();
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
     *      path="/service-electric-fee/delete",
     *      operationId="ServiceElectricFee delete",
     *      summary="ServiceElectricFee delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceElectricFeeForm"),
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
        $model = new ServiceElectricFeeForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }
    
    /**
     * @SWG\Get(
     *      path="/service-electric-fee/list",
     *      operationId="ServiceElectricFee list",
     *      summary="ServiceElectricFee list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="status" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="service map management id" ),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceElectricFee <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceElectricFeeResponse"),
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
        $modelSearch = new ServiceElectricFeeSearch();
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
     *      path="/service-electric-fee/detail?id={id}",
     *      operationId="ServiceElectricFee detail",
     *      summary="ServiceElectricFee",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceElectricFee" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceElectricFeeResponse"),
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
        $buildingCluster = Yii::$app->building->BuildingCluster;
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $post = ServiceElectricFeeResponse::findOne(['id' => $id, 'building_cluster_id' => $buildingCluster->id]);
        return $post;
    }

    /**
     * @SWG\Get(
     *      path="/service-electric-fee/last-index",
     *      operationId="ServiceElectricFee detail",
     *      summary="ServiceElectricFee",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="service map management id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="last_index", type="integer"),
     *                  @SWG\Property(property="lock_time", type="integer"),
     *              ),
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
    public function actionLastIndex()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $params = Yii::$app->request->queryParams;
        $id = !empty($params['id']) ? (int)$params['id'] : 0;
        $apartment_id = !empty($params['apartment_id']) ? (int)$params['apartment_id'] : 0;
        $service_map_management_id = !empty($params['service_map_management_id']) ? (int)$params['service_map_management_id'] : 0;

        //lấy chỉ số cuối
//        $lastIndex = ServiceElectricFee::find()->where([
//            'building_cluster_id' => $buildingCluster->id,
//            'apartment_id' => $apartment_id,
//            'service_map_management_id' => $service_map_management_id,
//        ]);
//        if(!empty($id)){
//            $lastIndex = $lastIndex->andWhere(['not', ['id' => $id]]);
//        }
//        $lastIndex = $lastIndex->orderBy(['id' => SORT_DESC])->one();
        $ServiceElectricInfo = ServiceElectricInfo::findOne(['building_cluster_id' => $buildingCluster->id, 'apartment_id' => $apartment_id]);
        $dataRes = [
            'last_index' => 0,
            'lock_time' => 0
        ];
        if(!empty($ServiceElectricInfo)){
            $dataRes['last_index'] = $ServiceElectricInfo->end_index;
            $dataRes['lock_time'] = $ServiceElectricInfo->end_date;
        }

//        if(!empty($lastIndex)){
//            if($lastIndex->status == ServiceElectricFee::STATUS_UNACTIVE){
//                return [
//                    'success' => false,
//                    'message' => Yii::t('frontend', "The apartment has a fee not yet approved"),
//                ];
//            }
//            $dataRes['last_index'] = $lastIndex->end_index;
//            $dataRes['lock_time'] = $lastIndex->lock_time;
//        }
        return $dataRes;
    }

    /**
     * @SWG\Post(
     *      path="/service-electric-fee/gen-charge",
     *      operationId="ServiceElectricFee create",
     *      summary="ServiceElectricFee create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceElectricFeeForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="message", type="string"),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="total_index", type="integer"),
     *                  @SWG\Property(property="total_money", type="integer"),
     *                  @SWG\Property(property="description", type="string"),
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
    public function actionGenCharge()
    {
        $model = new ServiceElectricFeeForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->getCharge();
    }

    /**
     * @SWG\Post(
     *      path="/service-electric-fee/change-status",
     *      description="ServiceElectricFee change-status",
     *      operationId="ServiceElectricFee change-status",
     *      summary="ServiceElectricFee change-status",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceElectricFeeChangeStatusForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="total_all", type="integer", default=0),
     *                  @SWG\Property(property="total_change", type="integer", default=0),
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
    public function actionChangeStatus()
    {
        $model = new ServiceElectricFeeChangeStatusForm();
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
     *      path="/service-electric-fee/import",
     *      description="ServiceElectricFee import",
     *      operationId="ServiceElectricFee import",
     *      summary="ServiceElectricFee import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceElectricFeeImportForm"),
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
        $model = new ServiceElectricFeeImportForm();
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
     *      path="/service-electric-fee/gen-form",
     *      description="ServiceElectricFee import",
     *      operationId="ServiceElectricFee import",
     *      summary="ServiceElectricFee import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricFee"},
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
        $model = new ServiceElectricFeeImportForm();
        return $model->genForm();
    }
}
