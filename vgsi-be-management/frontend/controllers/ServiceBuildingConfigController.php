<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ServiceBuildingConfig;
use common\models\ServiceMapManagement;
use frontend\models\ServiceBuildingConfigForm;
use frontend\models\ServiceBuildingConfigResponse;
use frontend\models\ServiceBuildingConfigSearch;
use Yii;

class ServiceBuildingConfigController extends ApiController
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
     *      path="/service-building-config/create",
     *      operationId="ServiceBuildingConfig create",
     *      summary="ServiceBuildingConfig create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBuildingConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceBuildingConfigForm"),
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
        $model = new ServiceBuildingConfigForm();
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
     *      path="/service-building-config/update",
     *      description="ServiceBuildingConfig update",
     *      operationId="ServiceBuildingConfig update",
     *      summary="ServiceBuildingConfig update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBuildingConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceBuildingConfigForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceBuildingConfigResponse"),
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
        $model = new ServiceBuildingConfigForm();
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
     *      path="/service-building-config/delete",
     *      operationId="ServiceBuildingConfig delete",
     *      summary="ServiceBuildingConfig delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBuildingConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceBuildingConfigForm"),
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
        $model = new ServiceBuildingConfigForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }

    /**
     * @SWG\Get(
     *      path="/service-building-config/list?service_map_management_id={service_map_management_id}",
     *      operationId="ServiceBuildingConfig list",
     *      summary="ServiceBuildingConfig list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBuildingConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="service map management id" ),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceBuildingConfig <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceBuildingConfigResponse"),
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
    public function actionList()
    {
        $modelSearch = new ServiceBuildingConfigSearch();
        $dataProvider = $modelSearch->search(Yii::$app->request->queryParams);
        return [
            'items' => $dataProvider->getModels(),
        ];
    }

    /**
     * @SWG\Get(
     *      path="/service-building-config/detail?service_base_url={service_base_url}",
     *      operationId="ServiceBuildingConfig detail",
     *      summary="ServiceBuildingConfig",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceBuildingConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceBuildingConfig" ),
     *      @SWG\Parameter( in="query", name="service_base_url", type="string", default="service_base_url", description="service_base_url" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceBuildingConfigResponse"),
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
        $id = Yii::$app->request->get("id", 0);
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $service_base_url = Yii::$app->request->get("service_base_url", '');
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $serviceBuildingConfig = ServiceBuildingConfigResponse::find()->where(['building_cluster_id' => $buildingCluster->id]);
        if ($id > 0) {
            $serviceBuildingConfig = $serviceBuildingConfig->andWhere(['id' => $id]);
        } else if (!empty($service_base_url)) {
            $serviceMapManagement = ServiceMapManagement::findOne(['service_base_url' => $service_base_url, 'building_cluster_id' => $buildingCluster->id]);
            if(!empty($serviceMapManagement)){
                $serviceBuildingConfig = $serviceBuildingConfig->andWhere(['service_map_management_id' => $serviceMapManagement->id]);
            }else{
                return null;
            }
        }else{
            return null;
        }
        $serviceBuildingConfig = $serviceBuildingConfig->one();
        return $serviceBuildingConfig;
    }

}
