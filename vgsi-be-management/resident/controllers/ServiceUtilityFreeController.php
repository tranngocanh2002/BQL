<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\ServiceUtilityFree;
use resident\models\ServiceUtilityFreeResponse;
use resident\models\ServiceUtilityFreeSearch;
use resident\models\ServiceUtilityRatingForm;
use Yii;

class ServiceUtilityFreeController extends ApiController
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
     *      path="/service-utility-free/list?service_map_management_id={service_map_management_id}",
     *      operationId="ServiceUtilityFree list",
     *      summary="ServiceUtilityFree list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityFree"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="service map management id" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Parameter( in="query", name="status", type="integer", default="1", description="0 : ngừng hoạt động, 1 : hoạt động" ),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceUtilityFree <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceUtilityFreeResponse"),
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
        $modelSearch = new ServiceUtilityFreeSearch();
        $dataProvider = $modelSearch->search(Yii::$app->request->queryParams);
        return [
            'items' => $dataProvider->getModels(),
        ];
    }

    /**
     * @SWG\Get(
     *      path="/service-utility-free/detail?id={id}",
     *      operationId="ServiceUtilityFree detail",
     *      summary="ServiceUtilityFree",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityFree"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceUtilityFree" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceUtilityFreeResponse"),
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
    public function actionDetail($id)
    {
        $post = ServiceUtilityFreeResponse::findOne(['id' => (int)$id]);
        return $post;
    }

    /**
     * @SWG\Post(
     *      path="/service-utility-free/rating",
     *      operationId="ServiceUtilityFree rating",
     *      summary="ServiceUtilityFree rating",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityFree"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceUtilityRatingForm"),
     *      ),
     *      @SWG\Response(response=200, description="Đánh giá dịch vụ",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="message", type="string"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
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
    public function actionRating()
    {
        $model = new ServiceUtilityRatingForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->rating();
    }
}
