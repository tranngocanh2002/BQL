<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use resident\models\ServiceUtilityBookingForm;
use resident\models\ServiceUtilityBookingResponse;
use resident\models\ServiceUtilityBookingSearch;
use Yii;

class ServiceUtilityBookingController extends ApiController
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
     *      path="/service-utility-booking/check-slot",
     *      description="ServiceUtilityBooking check-slot => bặt bộc service_utility_config_id, start_time, end_time, apartment_id",
     *      operationId="ServiceUtilityBooking check-slot",
     *      summary="ServiceUtilityBooking check-slot",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityBooking"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceUtilityBookingForm"),
     *      ),
     *      @SWG\Response(response=200, description="Kiểm tra số chỗ trống còn lại",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="slot_null", type="integer", default=0),
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
    public function actionCheckSlot()
    {
        $model = new ServiceUtilityBookingForm();
        $model->setScenario('checkSlot');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->checkSlot();
    }

    public function actionCheckTime()
    {
        $model = new ServiceUtilityBookingForm();
        $model->setScenario('checkTime');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->checkTime();
    }
    /**
     * @SWG\Post(
     *      path="/service-utility-booking/check-price",
     *      description="ServiceUtilityBooking check-price => bặt bộc service_utility_config_id, start_time, end_time, total_adult, total_child, apartment_id",
     *      operationId="ServiceUtilityBooking check-price",
     *      summary="ServiceUtilityBooking check-price",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityBooking"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceUtilityBookingForm"),
     *      ),
     *      @SWG\Response(response=200, description="Kiểm tra gia tiền",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="price", type="integer", default=0),
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
    public function actionCheckPrice()
    {
        $model = new ServiceUtilityBookingForm();
        $model->setScenario('checkPrice');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->checkPrice();
    }

    /**
     * @SWG\Post(
     *      path="/service-utility-booking/create",
     *      operationId="ServiceUtilityBooking create",
     *      summary="ServiceUtilityBooking create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityBooking"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceUtilityBookingForm"),
     *      ),
     *      @SWG\Response(response=200, description="Tạo yêu cầu đặt chỗ",
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
    public function actionCreate()
    {
        $model = new ServiceUtilityBookingForm();
        $model->setScenario('create');
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
     *      path="/service-utility-booking/update",
     *      description="ServiceUtilityBooking update",
     *      operationId="ServiceUtilityBooking update",
     *      summary="ServiceUtilityBooking update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityBooking"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceUtilityBookingForm"),
     *      ),
     *      @SWG\Response(response=200, description="Cập nhập yêu cầu đặt chỗ => bặt bộc id, apartment_id",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceUtilityBookingResponse"),
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
        $model = new ServiceUtilityBookingForm();
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
     *      path="/service-utility-booking/delete",
     *      operationId="ServiceUtilityBooking delete",
     *      summary="ServiceUtilityBooking delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityBooking"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceUtilityBookingForm"),
     *      ),
     *      @SWG\Response(response=200, description="Xóa yêu cầu đặt chỗ => bặt bộc id, apartment_id",
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
        $model = new ServiceUtilityBookingForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }

    /**
     * @SWG\Post(
     *      path="/service-utility-booking/cancel",
     *      operationId="ServiceUtilityBooking cancel",
     *      summary="ServiceUtilityBooking cancel",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityBooking"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ServiceUtilityBookingForm"),
     *      ),
     *      @SWG\Response(response=200, description="Hủy yêu cầu đặt chỗ => bặt bộc id, apartment_id",
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
    public function actionCancel()
    {
        $model = new ServiceUtilityBookingForm();
        $model->setScenario('cancel');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->cancel();
    }

    /**
     * @SWG\Get(
     *      path="/service-utility-booking/list",
     *      operationId="ServiceUtilityBooking list",
     *      summary="ServiceUtilityBooking list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityBooking"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default=0, description="apartment_id"),
     *      @SWG\Parameter(in="query", name="is_paid", type="integer", default=0, description="0 - chưa thanh toán, 1 - đã thanh toán"),
     *      @SWG\Parameter(in="query", name="status", type="string", default="0,1,2", description="status"),
     *      @SWG\Parameter(in="query", name="service_utility_config_id", type="integer", default=0, description="service utility config id"),
     *      @SWG\Parameter(in="query", name="service_utility_free_id", type="integer", default=0, description="service utility free id"),
     *      @SWG\Parameter(in="query", name="start_date", type="integer", default=0, description="ngày tạo created_at"),
     *      @SWG\Parameter(in="query", name="end_date", type="integer", default=0, description="ngày tạo created_at"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên ServiceUtilityBooking <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Danh sách yêu cầu đặt chỗ",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ServiceUtilityBookingResponse"),
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
        $modelSearch = new ServiceUtilityBookingSearch();
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
     *      path="/service-utility-booking/detail",
     *      operationId="ServiceUtilityBooking detail",
     *      summary="ServiceUtilityBooking",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ServiceUtilityBooking"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ServiceUtilityBooking" ),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="id Apartment" ),
     *      @SWG\Response(response=200, description="Chi tiết yêu cầu đặt chỗ",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ServiceUtilityBookingResponse"),
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
    public function actionDetail()
    {
        $user = Yii::$app->user->getIdentity();
        $id = Yii::$app->request->get("id", 0);
        $apartment_id = Yii::$app->request->get("apartment_id", 0);
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment_id, 'resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $post = ServiceUtilityBookingResponse::findOne(['id' => (int)$id, 'apartment_id' => (int)$apartment_id]);
        return $post;
    }
}
