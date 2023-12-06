<?php
namespace resident\controllers;

use common\helpers\ErrorCode;
use resident\models\ResidentUserCancelDeleteForm;
use resident\models\ResidentUserChangePasswordForm;
use resident\models\ResidentUserDeleteForm;
use resident\models\ResidentUserResponse;
use resident\models\ResidentUserSearchByPhone;
use resident\models\ResidentUserSetPasswordForm;
use resident\models\ApartmentMapResidentUserUpdatePhone;
use resident\models\ResidentUserUpdateForm;
use frontend\models\ApartmentMapResidentUserUpdateForm;
use resident\models\LoginOtpForm;
use Yii;
use yii\debug\models\search\User;

class ResidentUserController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [ 
            'unlock-deleted-user'
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
     *      path="/resident-user/update",
     *      description="ResidentUser update",
     *      operationId="ResidentUser update",
     *      summary="ResidentUser update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentUserUpdateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ResidentUserResponse"),
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
        $model = new ResidentUserUpdateForm();
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
     *      path="/resident-user/set-password",
     *      description="ResidentUser set-password",
     *      operationId="ResidentUser set-password",
     *      summary="ResidentUser set-password",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentUserSetPasswordForm"),
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
    public function actionSetPassword()
    {
        $model = new ResidentUserSetPasswordForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->setPassword();
    }

    /**
     * @SWG\Post(
     *      path="/resident-user/change-password",
     *      description="ResidentUser change-password",
     *      operationId="ResidentUser change-password",
     *      summary="ResidentUser change-password",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentUserChangePasswordForm"),
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
    public function actionChangePassword()
    {
        $model = new ResidentUserChangePasswordForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->changePassword();
    }

    /**
     * @SWG\Get(
     *      path="/resident-user/detail",
     *      operationId="ResidentUser detail",
     *      summary="ResidentUser",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ResidentUserResponse"),
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
        $residentUser = ResidentUserResponse::findOne(['id' => $user->id]);
        return $residentUser;
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
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionListByPhone()
    {
        $modelSearch = new ResidentUserSearchByPhone();
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
     * @SWG\Post(
     *      path="/resident-user/delete",
     *      description="ResidentUser delete",
     *      operationId="ResidentUser delete",
     *      summary="ResidentUser delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentUserDeleteForm"),
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
        $model = new ResidentUserDeleteForm();
        Yii::info(Yii::$app->request->bodyParams);
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->delete();
    }

    /**
     * @SWG\Post(
     *      path="/resident-user/cancel-delete",
     *      description="ResidentUser cancel delete",
     *      operationId="ResidentUser cancel delete",
     *      summary="ResidentUser cancel delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUser"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
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
    public function actionCancelDelete()
    {
        $model = new ResidentUserCancelDeleteForm();
        return $model->cancelDelete();
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
        return $model->changeNewPhone();
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
        return $model->VerifyOtpChangePhone(Yii::$app->request->post('building_cluster_id'),Yii::$app->request->post('apartment_id'));
    }

    /**
     * @SWG\Get(
     *      path="/resident-user/get-otp-check-by-current-phone",
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
    public function actionGetOtpCheckByCurrentPhone()
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
     *      path="/resident-user/unlock-deleted-user",
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
     *                      @SWG\Items(type="object", ref="#/definitions/UnlockDeletedUser"),
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
    public function actionUnlockDeletedUser()
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
        return $model->unlockDeletedUser();
    }
}
