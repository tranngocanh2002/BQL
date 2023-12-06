<?php

namespace resident\controllers;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use resident\models\BuildingClusterResponse;
use Yii;
use yii\filters\AccessControl;

class BuildingClusterController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
//            'detail'
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['update'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['@'],
                ],
            ],
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
     *      path="/building-cluster/detail?apartment_id={apartment_id}",
     *      operationId="Building detail",
     *      summary="BuildingCluster",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"BuildingCluster"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="id Apartment" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/BuildingClusterResponse"),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {}
     *         }
     *      },
     * )
     */
    public function actionDetail()
    {
        $user = Yii::$app->user->getIdentity();
        $apartment_id = Yii::$app->request->get("apartment_id", 0);
        $apartment_id = (int)$apartment_id;
        if(!empty($apartment_id)){
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => (int)$apartment_id, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE]);
        }else{
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE]);
        }
        $buildingCluster = null;
        if(!empty($apartmentMapResidentUser)){
            $buildingCluster = BuildingClusterResponse::findOne(['id' => $apartmentMapResidentUser->building_cluster_id]);
        }
        return $buildingCluster;
    }
}
