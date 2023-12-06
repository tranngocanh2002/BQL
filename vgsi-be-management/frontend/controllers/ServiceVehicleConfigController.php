<?php

namespace frontend\controllers;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use frontend\models\ServiceBuildingConfigResponse;
use frontend\models\ServiceVehicleConfigForm;
use frontend\models\ServiceVehicleConfigResponse;
use Yii;
use yii\filters\AccessControl;

class ServiceVehicleConfigController extends ApiController
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
     *      path="/service-vehicle-config/update",
     *      description="building update",
     *      operationId="building update",
     *      summary="building update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceVehicleConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceVehicleConfigForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceVehicleConfigResponse"),
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
        $model = new ServiceVehicleConfigForm();
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
     *      path="/service-vehicle-config/detail",
     *      operationId="Building detail",
     *      summary="ServiceVehicleConfig",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceVehicleConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceBuildingConfig" ),
     *      @SWG\Parameter( in="query", name="service_base_url", type="string", default="service_base_url", description="service_base_url" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceVehicleConfigResponse"),
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
//        $buildingCluster = Yii::$app->building->BuildingCluster;
//        return ServiceVehicleConfigResponse::findOne(['building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => (int)$service_map_management_id]);

        $id = Yii::$app->request->get("id", 0);
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $service_base_url = Yii::$app->request->get("service_base_url", '');
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $serviceVehicleConfig = ServiceVehicleConfigResponse::find()->where(['building_cluster_id' => $buildingCluster->id]);
        if ($id > 0) {
            $serviceVehicleConfig = $serviceVehicleConfig->andWhere(['id' => $id]);
        } else if (!empty($service_base_url)) {
            $serviceMapManagement = ServiceMapManagement::findOne(['service_base_url' => $service_base_url, 'building_cluster_id' => $buildingCluster->id]);
            if(!empty($serviceMapManagement)){
                $serviceVehicleConfig = $serviceVehicleConfig->andWhere(['service_map_management_id' => $serviceMapManagement->id]);
            }else{
                return null;
            }
        }else{
            return null;
        }
        $serviceVehicleConfig = $serviceVehicleConfig->one();
        return $serviceVehicleConfig;
    }
}
