<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use frontend\models\ApartmentMapResidentUserReceiveNotifyFeeForm;
use frontend\models\ApartmentMapResidentUserReceiveNotifyFeeResponse;
use frontend\models\ApartmentMapResidentUserReceiveNotifyFeeSearch;
use Yii;

class ApartmentMapResidentUserReceiveNotifyFeeController extends ApiController
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
     *      path="/apartment-map-resident-user-receive-notify-fee/create",
     *      operationId="ApartmentMapResidentUserReceiveNotifyFee create",
     *      summary="ApartmentMapResidentUserReceiveNotifyFee create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ApartmentMapResidentUserReceiveNotifyFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentMapResidentUserReceiveNotifyFeeForm"),
     *      ),
     *      @SWG\Response(response=200, description="Thêm thông tin nhận thông báo cho căn hộ",
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
        $model = new ApartmentMapResidentUserReceiveNotifyFeeForm();
        $model->setScenario('create');
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
     *      path="/apartment-map-resident-user-receive-notify-fee/update",
     *      description="ApartmentMapResidentUserReceiveNotifyFee update",
     *      operationId="ApartmentMapResidentUserReceiveNotifyFee update",
     *      summary="ApartmentMapResidentUserReceiveNotifyFee update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ApartmentMapResidentUserReceiveNotifyFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentMapResidentUserReceiveNotifyFeeForm"),
     *      ),
     *      @SWG\Response(response=200, description="Update thông tin nhận thông báo cho căn hộ",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ApartmentMapResidentUserReceiveNotifyFeeResponse"),
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
        $model = new ApartmentMapResidentUserReceiveNotifyFeeForm();
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
     *      path="/apartment-map-resident-user-receive-notify-fee/delete",
     *      operationId="ApartmentMapResidentUserReceiveNotifyFee delete",
     *      summary="ApartmentMapResidentUserReceiveNotifyFee delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ApartmentMapResidentUserReceiveNotifyFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentMapResidentUserReceiveNotifyFeeForm"),
     *      ),
     *      @SWG\Response(response=200, description="Xóa thông tin nhận thông báo",
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
        $model = new ApartmentMapResidentUserReceiveNotifyFeeForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }
    
    /**
     * @SWG\Get(
     *      path="/apartment-map-resident-user-receive-notify-fee/list",
     *      operationId="ApartmentMapResidentUserReceiveNotifyFee list",
     *      summary="ApartmentMapResidentUserReceiveNotifyFee list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ApartmentMapResidentUserReceiveNotifyFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ApartmentMapResidentUserReceiveNotifyFee <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Danh sách thông tin nhận thông báo",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ApartmentMapResidentUserReceiveNotifyFeeResponse"),
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
        $modelSearch = new ApartmentMapResidentUserReceiveNotifyFeeSearch();
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
     *      path="/apartment-map-resident-user-receive-notify-fee/detail",
     *      operationId="ApartmentMapResidentUserReceiveNotifyFee detail",
     *      summary="ApartmentMapResidentUserReceiveNotifyFee",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ApartmentMapResidentUserReceiveNotifyFee"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ApartmentMapResidentUserReceiveNotifyFee" ),
     *      @SWG\Response(response=200, description="Chi tiết, nếu tồn tại thì sẽ không gửi thông báo phí vào tài khoản chủ hộ nữa",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ApartmentMapResidentUserReceiveNotifyFeeResponse"),
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
    public function actionDetail($id)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $post = ApartmentMapResidentUserReceiveNotifyFeeResponse::findOne(['id' => $id, 'building_cluster_id' => $buildingCluster->id]);
        return $post;
    }

}
