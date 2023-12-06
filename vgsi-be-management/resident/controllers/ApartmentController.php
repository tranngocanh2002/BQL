<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use resident\models\ApartmentActiveForm;
use resident\models\ApartmentImageForm;
use resident\models\ApartmentMapResidentUserAddByCodeForm;
use resident\models\ApartmentMapResidentUserAddForm;
use resident\models\ApartmentMapResidentUserRemoveForm;
use resident\models\ApartmentMapResidentUserResponse;
use Yii;
use yii\filters\RateLimiter;

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
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
            'only' => ['list'],
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
     *      path="/apartment/list",
     *      operationId="Apartment list",
     *      description="Danh sách ăn hộ của user resident",
     *      summary="Apartment list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
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
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionList()
    {
        $user = Yii::$app->user->getIdentity();
        $apartments = ApartmentMapResidentUserResponse::find()->where(['resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->orderBy(['created_at' => SORT_DESC])->all();
        return [
            'items' => $apartments,
        ];
    }

    /**
     * @SWG\Get(
     *      path="/apartment/detail?id={id}",
     *      operationId="Apartment detail",
     *      description="thông tin chi tiết ăn hộ của user resident",
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
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ApartmentMapResidentUserResponse"),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *              "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionDetail()
    {
        $user = Yii::$app->user->getIdentity();
        $id = Yii::$app->request->get("id", 0);
        $apartmentMap = ApartmentMapResidentUserResponse::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => (int)$id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMap)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data")
            ];
        }
        if(!empty($apartmentMap) && $apartmentMap->install_app == ApartmentMapResidentUser::NOT_INSTALL_APP){
            $apartmentMap->install_app = ApartmentMapResidentUser::INSTALL_APP;
            $apartmentMap->save();
        }
        return $apartmentMap;
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
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionAddResidentUser()
    {
        $model = new ApartmentMapResidentUserAddForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->add();
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
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->remove();
    }

    /**
     * @SWG\Post(
     *      path="/apartment/add-resident-user-by-code",
     *      operationId="Apartment add-resident-user-by-code",
     *      summary="Apartment add-resident-user",
     *      description="Dùng để thêm thành viên vào căn hộ khi có mã căn hộ - user resident tự add mã căn hộ",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentMapResidentUserAddByCodeForm"),
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
    public function actionAddResidentUserByCode()
    {
        $model = new ApartmentMapResidentUserAddByCodeForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->addByCode();
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
     *          @SWG\Schema(ref="#/definitions/ApartmentImageForm"),
     *      ),
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
     *         }
     *      },
     * )
     */
    public function actionUpdate()
    {
        $model = new ApartmentImageForm();
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
     *      path="/apartment/active",
     *      description="Apartment active",
     *      operationId="Apartment active",
     *      summary="Apartment active",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Apartment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ApartmentActiveForm"),
     *      ),
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
     *         }
     *      },
     * )
     */
    public function actionActive()
    {
        $model = new ApartmentActiveForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->active();
    }
}
