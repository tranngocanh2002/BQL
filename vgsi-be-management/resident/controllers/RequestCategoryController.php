<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\RequestCategory;
use resident\models\BuildingClusterResponse;
use resident\models\RequestCategoryResponse;
use Yii;

class RequestCategoryController extends ApiController
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
     * @SWG\Get(
     *      path="/request-category/list?apartment_id={apartment_id}",
     *      operationId="RequestCategory list",
     *      summary="RequestCategory list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"RequestCategory"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="id Apartment" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/RequestCategoryResponse"),
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
    public function actionList()
    {
        $user = Yii::$app->user->getIdentity();
        $apartment_id = Yii::$app->request->get("apartment_id", 0);
        $apartment_id = (int)$apartment_id;
        if(!empty($apartment_id)){
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => (int)$apartment_id, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE]);
        }else{
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE]);
        }
        $requestCategorys = [];
        if(!empty($apartmentMapResidentUser)){
            $requestCategorys = RequestCategoryResponse::find()->where(['building_cluster_id' => $apartmentMapResidentUser->building_cluster_id, 'is_deleted' => RequestCategory::NOT_DELETED])->all();
        }
        return [
            'items' => $requestCategorys,
        ];
    }

    /**
     * @SWG\Get(
     *      path="/request-category/detail?id={id}",
     *      operationId="RequestCategory detail",
     *      summary="RequestCategory",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"RequestCategory"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id RequestCategory" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/RequestCategoryResponse"),
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
    public function actionDetail()
    {
        $user = Yii::$app->user->getIdentity();
        $id = Yii::$app->request->get("id", 0);
        $requestCategory= RequestCategoryResponse::findOne(['id' => (int)$id, 'building_cluster_id' => $user->building_cluster_id]);
        return $requestCategory;
    }
}
