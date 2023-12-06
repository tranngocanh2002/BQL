<?php
namespace frontend\controllers;

use common\helpers\ErrorCode;
use frontend\models\ApartmentMapResidentUserAddForm;
use frontend\models\ApartmentMapResidentUserResponse;
use frontend\models\ApartmentMapResidentUserSearch;
use frontend\models\ApartmentMapResidentUserSearchByPhone;
use frontend\models\ApartmentMapResidentUserUpdatePhone;
use frontend\models\ApartmentMapResidentUserUpdateForm;
use frontend\models\ResidentUserCreateForm;
use frontend\models\ResidentUserImportForm;
use frontend\models\ResidentUserOldResponse;
use frontend\models\ResidentUserSearch;
use frontend\models\ResidentUserResponse;
use frontend\models\ResidentUserSearchByPhone;
use Yii;

class ResidentUserController extends ApiController
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
     *      path="/resident-user/create",
     *      operationId="ResidentUser create",
     *      summary="ResidentUser create",
     *      description="Dùng để thêm mới hoạc đổi vai trò thành viên",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentMapResidentUserAddForm"),
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
    public function actionCreate()
    {
        $model = new ApartmentMapResidentUserAddForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->add();
    }

    
    /**
     * @SWG\Get(
     *      path="/resident-user/list?pageSize={pageSize}&page={page}",
     *      operationId="users",
     *      summary="users",
     *      description="Api List User",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="resident_user_id", type="integer", default="", description="resident user id"),
     *      @SWG\Parameter(in="query", name="type", type="integer", default="", description="Vai trò : 0 - thành viên, 1- chủ hộ"),
     *      @SWG\Parameter(in="query", name="total_apartment", type="integer", default="", description="total_apartment: số lượng bds"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default="", description="apartment id"),
     *      @SWG\Parameter(in="query", name="apartment_name", type="string", default="", description="apartment name"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="Name"),
     *      @SWG\Parameter(in="query", name="phone", type="string", default="", description="Phone"),
     *      @SWG\Parameter(in="query", name="status", type="integer", default="", description="Trạng thái"),
     *      @SWG\Parameter(in="query", name="install_app", type="integer", default="", description="0 - chưa cài app, 1 - đã cài app"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="All Permission Controler/Action",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ApartmentMapResidentUserResponse"),
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
        $modelSearch = new ApartmentMapResidentUserSearch();
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
     *      path="/resident-user/list-old?pageSize={pageSize}&page={page}",
     *      operationId="users",
     *      summary="users old",
     *      description="Api List User Old",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="Name"),
     *      @SWG\Parameter(in="query", name="phone", type="string", default="", description="Phone"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ResidentUserOldResponse"),
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
    public function actionListOld()
    {
        $modelSearch = new ResidentUserSearch();
        $dataProvider = $modelSearch->searchOld(Yii::$app->request->queryParams);
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
     *      path="/resident-user/list-apartment",
     *      operationId="users",
     *      summary="users",
     *      description="Api List apartment bu user",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="resident_user_id", type="integer", default="", description="resident user id"),
     *      @SWG\Parameter(in="query", name="resident_user_phone", type="string", default="", description="resident user phone"),
     *      @SWG\Response(response=200, description="All apartment by user",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ApartmentMapResidentUserResponse"),
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
    public function actionListApartment()
    {
        $modelSearch = new ApartmentMapResidentUserSearch();
        $dataProvider = $modelSearch->listsByUser(Yii::$app->request->queryParams);
        return [
            'items' => $dataProvider->getModels(),
        ];
    }

    /**
     * @SWG\Get(
     *      path="/resident-user/detail?id={id}",
     *      operationId="ResidentUser detail",
     *      summary="ResidentUser",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ResidentUser" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ApartmentMapResidentUserResponse"),
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
//        $user = Yii::$app->user->getIdentity();
//        $id = Yii::$app->request->get("id", 0);
//        $post = ApartmentMapResidentUserResponse::findOne(['resident_user_id' => (int)$id, 'building_cluster_id' => $user->building_cluster_id]);
//        return $post;
        $user = Yii::$app->user->getIdentity();
        $id = Yii::$app->request->get("id", 0);
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $post = ApartmentMapResidentUserResponse::findOne(['id' => $id, 'building_cluster_id' => $user->building_cluster_id]);
        return $post;
    }

    /**
     * @SWG\Get(
     *      path="/resident-user/detail-old?id={id}",
     *      operationId="ResidentUser detail old",
     *      summary="ResidentUser",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ResidentUser Old" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ResidentUserOldResponse"),
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
    public function actionDetailOld()
    {
        $id = Yii::$app->request->get("id", 0);
        $post = ResidentUserOldResponse::findOne(['id' => (int)$id]);
        return $post;
    }

    /**
     * @SWG\Post(
     *      path="/resident-user/update",
     *      operationId="ResidentUser update",
     *      summary="ResidentUser update",
     *      description="thay đổi user map căn hộ",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentMapResidentUserUpdateForm"),
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
    public function actionUpdate()
    {
        $model = new ApartmentMapResidentUserUpdateForm();
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
     *      path="/resident-user/import",
     *      description="ResidentUser import",
     *      operationId="ResidentUser import",
     *      summary="ResidentUser import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentUserImportForm"),
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
    public function actionImport()
    {
        $model = new ResidentUserImportForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->import();
    }

    /**
     * @SWG\Get(
     *      path="/resident-user/gen-form",
     *      description="ResidentUser import",
     *      operationId="ResidentUser import",
     *      summary="ResidentUser import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="file_path", type="string"),
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
    public function actionGenForm()
    {
        $model = new ResidentUserImportForm();
        return $model->genForm();
    }

    /**
     * @SWG\Get(
     *      path="/resident-user/export",
     *      description="ResidentUser export",
     *      operationId="ResidentUser export",
     *      summary="ResidentUser export",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="resident_user_id", type="integer", default="", description="resident user id"),
     *      @SWG\Parameter(in="query", name="type", type="integer", default="", description="Vai trò : 0 - thành viên, 1- chủ hộ"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default="", description="apartment id"),
     *      @SWG\Parameter(in="query", name="apartment_name", type="string", default="", description="apartment name"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="Name"),
     *      @SWG\Parameter(in="query", name="phone", type="string", default="", description="Phone"),
     *      @SWG\Parameter(in="query", name="status", type="integer", default="", description="Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="file_path", type="string"),
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
    public function actionExport()
    {
        $modelSearch = new ApartmentMapResidentUserSearch();
        return $modelSearch->search(Yii::$app->request->queryParams, true);
    }

    /**
     * @SWG\Get(
     *      path="/resident-user/list-by-phone",
     *      operationId="users",
     *      summary="users",
     *      description="Api List User",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="phone", type="string", default="", description="Phone"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="All Permission Controler/Action",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ResidentUserResponseByPhone"),
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
    public function actionListByPhone()
    {
        $modelSearch = new ApartmentMapResidentUserSearchByPhone();
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
     *      path="/resident-user/change-phone",
     *      operationId="users",
     *      summary="users",
     *      description="Api List User",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="phone", type="string", default="", description="Phone"),
     *      @SWG\Response(response=200, description="All Permission Controler/Action",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ChangePhone"),
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
    public function actionChangePhone()
    {
        $model = new ApartmentMapResidentUserUpdatePhone();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->changePhone();
    }

    /**
     * @SWG\Get(
     *      path="/resident-user/verify-otp-change-phone",
     *      operationId="users",
     *      summary="users",
     *      description="Api List User",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="otp", type="string", default="", description="otp"),
     *      @SWG\Response(response=200, description="All Permission Controler/Action",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/VerifyOtpChangePhone"),
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
    public function actionVerifyOtpChangePhone()
    {
        $model = new ApartmentMapResidentUserUpdatePhone();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->VerifyOtpChangePhone();
    }
}
