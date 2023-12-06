<?php

namespace frontend\controllers;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use frontend\models\ServiceElectricConfigForm;
use frontend\models\ServiceElectricConfigResponse;
use Yii;
use yii\filters\AccessControl;

class ServiceElectricConfigController extends ApiController
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
     *      path="/service-electric-config/update",
     *      description="building update",
     *      operationId="building update",
     *      summary="building update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceElectricConfigForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceElectricConfigResponse"),
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
        $model = new ServiceElectricConfigForm();
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
     * @SWG\Get(
     *      path="/service-electric-config/detail",
     *      operationId="Building detail",
     *      summary="ServiceElectricConfig",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceElectricConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="service map management id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceElectricConfigResponse"),
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
    public function actionDetail($service_map_management_id)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        if(is_numeric($service_map_management_id)){ $service_map_management_id = (int)$service_map_management_id;}
        return ServiceElectricConfigResponse::findOne(['building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => $service_map_management_id]);
    }
}
