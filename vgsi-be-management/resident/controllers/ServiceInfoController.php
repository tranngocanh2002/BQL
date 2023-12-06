<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\ServiceManagementVehicle;
use resident\models\ServiceBuildingInfoResponse;
use resident\models\ServiceElectricInfoResponse;
use resident\models\ServiceManagementVehicleResponse;
use resident\models\ServiceWaterInfoResponse;
use Yii;

class ServiceInfoController extends ApiController
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
     *      path="/service-info/building",
     *      operationId="ServiceInfo building",
     *      summary="ServiceInfo building",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceInfo"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", required=true, type="integer", default="1", description="apartment id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object", ref="#/definitions/ServiceBuildingInfoResponse"),
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
    public function actionBuilding($apartment_id = null)
    {
        if(empty($apartment_id)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        return ServiceBuildingInfoResponse::findOne(['apartment_id' => $apartment_id]);
    }

    /**
     * @SWG\Get(
     *      path="/service-info/water",
     *      operationId="ServiceInfo water",
     *      summary="ServiceInfo water",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceInfo"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", required=true, type="integer", default="1", description="apartment id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object", ref="#/definitions/ServiceWaterInfoResponse"),
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
    public function actionWater($apartment_id = null)
    {

        if(empty($apartment_id)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        return ServiceWaterInfoResponse::findOne(['apartment_id' => $apartment_id]);
    }

    /**
     * @SWG\Get(
     *      path="/service-info/electric",
     *      operationId="ServiceInfo electric",
     *      summary="ServiceInfo water",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceInfo"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", required=true, type="integer", default="1", description="apartment id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object", ref="#/definitions/ServiceElectricInfoResponse"),
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
    public function actionElectric($apartment_id = null)
    {

        if(empty($apartment_id)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        return ServiceElectricInfoResponse::findOne(['apartment_id' => $apartment_id]);
    }

    /**
     * @SWG\Get(
     *      path="/service-info/vehicle",
     *      operationId="ServiceInfo vehicle",
     *      summary="ServiceInfo vehicle",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceInfo"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", required=true, type="integer", default="1", description="apartment id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceManagementVehicleResponse"),
     *                  ),
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
    public function actionVehicle($apartment_id = null)
    {
        if(empty($apartment_id)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        return [
            'items' => ServiceManagementVehicleResponse::find()->where(['apartment_id' => $apartment_id, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED])->all(),
        ];
    }
}
