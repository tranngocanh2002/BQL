<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use frontend\models\ApartmentCreateForm;
use frontend\models\ApartmentDeleteForm;
use frontend\models\ApartmentImportForm;
use frontend\models\ApartmentMapResidentTypeUpdateForm;
use frontend\models\ApartmentMapResidentUserAddForm;
use frontend\models\ApartmentMapResidentUserAddFormClone;
use frontend\models\ApartmentMapResidentUserRemoveForm;
use frontend\models\ApartmentMapResidentUserResponse;
use frontend\models\ApartmentResponse;
use frontend\models\ApartmentSearch;
use Yii;

class ApartmentController extends ApiController
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
     *      path="/apartment/create",
     *      operationId="Apartment create",
     *      summary="Apartment create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentCreateForm"),
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
        $model = new ApartmentCreateForm();
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
     *      path="/apartment/update",
     *      description="Apartment update",
     *      operationId="Apartment update",
     *      summary="Apartment update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ApartmentResponse"),
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
        $model = new ApartmentCreateForm();
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
     *      path="/apartment/delete",
     *      operationId="Apartment delete",
     *      summary="Apartment delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentDeleteForm"),
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
    public function actionDelete()
    {
        $model = new ApartmentDeleteForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->delete();
    }
    
    /**
     * @SWG\Get(
     *      path="/apartment/list",
     *      operationId="Apartment list",
     *      summary="Apartment list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="form_type", type="integer", default=0, description="0: Villa đơn lập, 1: Villa song lập, 2: Nhà phố, 3: Nhà phố thương mại, 4: Căn hộ Studio, 5: Căn hộ, 6: Căn hộ Duplex thông tầng, 7: Căn hộ penthouse, 8: Officetel, 9:  Khách sạn và căn hộ dịch vụ"),
     *      @SWG\Parameter(in="query", name="code", type="string", default="", description="Code"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="Tên"),
     *      @SWG\Parameter(in="query", name="parent_path", type="string", default="", description="parent path"),
     *      @SWG\Parameter(in="query", name="resident_user_id", type="integer", default="", description="resident user id"),
     *      @SWG\Parameter(in="query", name="resident_user_name", type="string", default="", description="resident user name"),
     *      @SWG\Parameter(in="query", name="status", type="integer", default="", description="Trạng thái"),
     *      @SWG\Parameter(in="query", name="status_delivery", type="integer", default="", description="Tình trạng bàn giao: 0 - chưa bàn giao,1 - đã bàn giao"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên Apartment <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ApartmentResponse"),
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
        $ApartmentSearch = new ApartmentSearch();
        $dataProvider = $ApartmentSearch->search(Yii::$app->request->queryParams);
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
     *      path="/apartment/detail?id={id}",
     *      operationId="Apartment detail",
     *      summary="Apartment",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id Apartment" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ApartmentResponse"),
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
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $post = ApartmentResponse::findOne(['id' => $id, 'building_cluster_id' => $user->building_cluster_id]);
        return $post;
    }

    /**
     * @SWG\Post(
     *      path="/apartment/add-resident-user",
     *      operationId="Apartment add-resident-user",
     *      summary="Apartment add-resident-user",
     *      description="Dùng để thêm mới hoạc đổi vai trò thành viên",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
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
    public function actionAddResidentUser()
    {
        $model = new ApartmentMapResidentUserAddFormClone();
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
     * @SWG\Post(
     *      path="/apartment/update-resident-type",
     *      operationId="Apartment update-resident-type",
     *      summary="Apartment update-resident-type",
     *      description="Đổi vai trò thành viên",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentMapResidentTypeUpdateForm"),
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
    public function actionUpdateResidentType()
    {
        $model = new ApartmentMapResidentTypeUpdateForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->process();
    }

    /**
     * @SWG\Get(
     *      path="/apartment/list-resident-user?apartment_id={apartment_id}",
     *      operationId="Apartment list-resident-user",
     *      summary="Apartment list-resident-user",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default=1, description="apartment id"),
     *      @SWG\Response(response=200, description="Info",
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
    public function actionListResidentUser()
    {
        $apartment_id = Yii::$app->request->get("apartment_id", 0);
        $apartment_id = (int)$apartment_id;
        if (empty($apartment_id)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        } else {
            // trường hợp này lấy tất cả , user bị xóa cho xuống cuối
            $apartmentMapResidentUsers = ApartmentMapResidentUserResponse::find()
                ->where(['apartment_id' => $apartment_id])
                ->orderBy(['created_at' => SORT_DESC, 'is_deleted' => SORT_ASC, 'deleted_at' => SORT_DESC])
                ->all();
            return [
                'items' => $apartmentMapResidentUsers,
            ];
        }
    }

    /**
     * @SWG\Post(
     *      path="/apartment/remove-resident-user",
     *      operationId="Apartment add-resident-user",
     *      summary="Apartment add-resident-user",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentMapResidentUserRemoveForm"),
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
    public function actionRemoveResidentUser()
    {
        $model = new ApartmentMapResidentUserRemoveForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->remove();
    }


    /**
     * @SWG\Post(
     *      path="/apartment/import",
     *      description="Apartment import",
     *      operationId="Apartment import",
     *      summary="Apartment import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentImportForm"),
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
        $model = new ApartmentImportForm();
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
     *      path="/apartment/gen-form",
     *      description="Apartment import",
     *      operationId="Apartment import",
     *      summary="Apartment import",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
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
        $model = new ApartmentImportForm();
        return $model->genForm();
    }

    /**
     * @SWG\Get(
     *      path="/apartment/export",
     *      description="Apartment export",
     *      operationId="Apartment export",
     *      summary="Apartment export",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="form_type", type="integer", default=0, description="0: Villa đơn lập, 1: Villa song lập, 2: Nhà phố, 3: Nhà phố thương mại, 4: Căn hộ Studio, 5: Căn hộ, 6: Căn hộ Duplex thông tầng, 7: Căn hộ penthouse, 8: Officetel, 9:  Khách sạn và căn hộ dịch vụ"),
     *      @SWG\Parameter(in="query", name="code", type="string", default="", description="Code"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="Tên"),
     *      @SWG\Parameter(in="query", name="parent_path", type="string", default="", description="parent path"),
     *      @SWG\Parameter(in="query", name="resident_user_id", type="integer", default="", description="resident user id"),
     *      @SWG\Parameter(in="query", name="resident_user_name", type="string", default="", description="resident user name"),
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
        $ApartmentSearch = new ApartmentSearch();
        return $ApartmentSearch->search(Yii::$app->request->queryParams, true);
    }

    /**
     * @SWG\Get(
     *      path="/apartment/list-type",
     *      operationId="Apartment list-type",
     *      summary="Apartment list-type",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="array",
     *                   @SWG\Items(type="object",
     *                        @SWG\Property(property="id", type="integer"),
     *                        @SWG\Property(property="name", type="string"),
     *                        @SWG\Property(property="name_en", type="string"),
     *                   ),
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
    public function actionListType()
    {
        $formTypeList = Yii::$app->params['Apartment_form_type_list'];
        $formTypeListEn = Yii::$app->params['Apartment_form_type_en_list'];
        $res = [];
        foreach ($formTypeList as $id => $name){
            $res[] = [
                'id' => $id,
                'name' => $name,
                'name_en' => $formTypeListEn[$id] ?? '',
            ];
        }
        return $res;
    }

}
