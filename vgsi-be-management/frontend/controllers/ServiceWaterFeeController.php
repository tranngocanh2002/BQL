<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ServiceWaterFee;
use common\models\ServiceWaterInfo;
use common\models\ServiceWaterLevel;
use frontend\models\ServiceWaterFeeChangeStatusForm;
use frontend\models\ServiceWaterFeeForm;
use frontend\models\ServiceWaterFeeImportForm;
use frontend\models\ServiceWaterFeeResponse;
use frontend\models\ServiceWaterFeeSearch;
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
     * @SWG\Post(
     *      path="/service-water-fee/create",
     *      operationId="ServiceWaterFee create",
     *      summary="ServiceWaterFee create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceWaterFeeForm"),
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
        $model = new ServiceWaterFeeForm();
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
     *      path="/service-water-fee/update",
     *      description="ServiceWaterFee update",
     *      operationId="ServiceWaterFee update",
     *      summary="ServiceWaterFee update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceWaterFeeForm"),
     *      ),
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
     *             "domain_origin": {},
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionUpdate()
    {
        $model = new ServiceWaterFeeForm();
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
     *      path="/service-water-fee/delete",
     *      operationId="ServiceWaterFee delete",
     *      summary="ServiceWaterFee delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceWaterFeeForm"),
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
        $model = new ServiceWaterFeeForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
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
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="status" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="service map management id" ),
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
     *             "domain_origin": {},
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
        $post = ServiceWaterFeeResponse::findOne(['id' => $id, 'building_cluster_id' => $buildingCluster->id]);
        return $post;
    }

    /**
     * @SWG\Get(
     *      path="/service-water-fee/last-index",
     *      operationId="ServiceWaterFee detail",
     *      summary="ServiceWaterFee",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
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
//        $lastIndex = ServiceWaterFee::find()->where([
//            'building_cluster_id' => $buildingCluster->id,
//            'apartment_id' => $apartment_id,
//            'service_map_management_id' => $service_map_management_id,
//        ]);
//        if(!empty($id)){
//            $lastIndex = $lastIndex->andWhere(['not', ['id' => $id]]);
//        }
//        $lastIndex = $lastIndex->orderBy(['id' => SORT_DESC])->one();
        $serviceWaterInfo = ServiceWaterInfo::findOne(['building_cluster_id' => $buildingCluster->id, 'apartment_id' => $apartment_id]);
        $dataRes = [
            'last_index' => 0,
            'lock_time' => 0
        ];
        if(!empty($serviceWaterInfo)){
            $dataRes['last_index'] = $serviceWaterInfo->end_index;
            $dataRes['lock_time'] = $serviceWaterInfo->end_date;
        }

//        if(!empty($lastIndex)){
//            if($lastIndex->status == ServiceWaterFee::STATUS_UNACTIVE){
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
     *      path="/service-water-fee/gen-charge",
     *      operationId="ServiceWaterFee create",
     *      summary="ServiceWaterFee create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceWaterFeeForm"),
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
        $model = new ServiceWaterFeeForm();
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
     *      path="/service-water-fee/change-status",
     *      description="ServiceWaterFee change-status",
     *      operationId="ServiceWaterFee change-status",
     *      summary="ServiceWaterFee change-status",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceWaterFeeChangeStatusForm"),
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
        $model = new ServiceWaterFeeChangeStatusForm();
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
     *      path="/service-water-fee/import",
     *      description="ServiceWaterFee import",
     *      operationId="ServiceWaterFee import",
     *      summary="ServiceWaterFee import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceWaterFeeImportForm"),
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
        $model = new ServiceWaterFeeImportForm();
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
     *      path="/service-water-fee/gen-form",
     *      description="ServiceWaterFee import",
     *      operationId="ServiceWaterFee import",
     *      summary="ServiceWaterFee import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceWaterFee"},
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
        $model = new ServiceWaterFeeImportForm();
        return $model->genForm();
    }
}
