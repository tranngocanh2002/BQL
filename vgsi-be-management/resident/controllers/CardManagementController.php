<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use resident\models\CardManagementForm;
use resident\models\CardManagementResponse;
use resident\models\CardManagementSearch;
use resident\models\EparkingCardHistorySearch;
use Yii;

class CardManagementController extends ApiController
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
     *      path="/card-management/create",
     *      operationId="CardManagement create",
     *      summary="CardManagement create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"CardManagement"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/CardManagementForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="message", type="string"),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/CardManagementResponse"),
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
    public function actionCreate()
    {
        $model = new CardManagementForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->create();
    }

    /**
     * @SWG\Post(
     *      path="/card-management/update",
     *      description="CardManagement update",
     *      operationId="CardManagement update",
     *      summary="CardManagement update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"CardManagement"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/CardManagementForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/CardManagementResponse"),
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
    public function actionUpdate()
    {
        $model = new CardManagementForm();
        $model->setScenario('update');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->update();
    }

    /**
     * @SWG\Post(
     *      path="/card-management/delete",
     *      operationId="CardManagement delete",
     *      summary="CardManagement delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"CardManagement"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/CardManagementForm"),
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
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionDelete()
    {
        $model = new CardManagementForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            Yii::error($model->errors);
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        return $model->delete();
    }

    /**
     * @SWG\Get(
     *      path="/card-management/list",
     *      operationId="CardManagement list",
     *      summary="CardManagement list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"CardManagement"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default=1, description="apartment id"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/CardManagementResponse"),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
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
        $modelSearch = new CardManagementSearch();
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
     *      path="/card-management/detail",
     *      operationId="CardManagement detail",
     *      summary="CardManagement",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"CardManagement"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id CardManagement" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment_id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/CardManagementResponse"),
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
    public function actionDetail($id, $apartment_id)
    {
        if(empty($id) || empty($apartment_id)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment_id, 'resident_user_phone' => $user->phone]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        if($apartmentMapResidentUser->type !== ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD){
            $CardManagement = CardManagementResponse::findOne(['id' => (int)$id, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id, 'apartment_id' => $apartment_id, 'resident_user_id' => $user->id]);
        }else{
            $CardManagement = CardManagementResponse::findOne(['id' => (int)$id, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id, 'apartment_id' => $apartment_id]);
        }
        return $CardManagement;
    }

    /**
     * @SWG\Get(
     *      path="/card-management/list-card-history",
     *      operationId="CardManagement list card history",
     *      summary="CardManagement list card history",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"CardManagement"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default=1, description="apartment id"),
     *      @SWG\Parameter(in="query", name="service_management_vehicle_id", type="integer", default=1, description="service management vehicle id"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/EparkingCardHistoryResponse"),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
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
    public function actionListCardHistory()
    {
        $modelSearch = new EparkingCardHistorySearch();
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
}
