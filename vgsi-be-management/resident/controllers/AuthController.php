<?php
namespace resident\controllers;

use common\helpers\ErrorCode;
use resident\models\LoginForm;
use resident\models\LoginOtpForm;
use resident\models\LoginPasswordForm;
use resident\models\LogoutForm;
use resident\models\ResidentUserRefreshTokenForm;
use resident\models\ResidentUserSetPasswordForm;
use Yii;

class AuthController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'index',
            'login',
            'check-phone',
            'send-otp',
            'login-otp',
            'login-password',
            'refresh-token',
            'change-password-by-otp',
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
     *      path="/auth/check-phone",
     *      operationId="check-phone",
     *      summary="User check-phone",
     *      description="Api user check-phone",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Auth"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/LoginOtpForm"),
     *      ),
     *      @SWG\Response(response=200, description="Kiểm tra số điện thoại đã tồn tại trong căn hộ chưa",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *         }
     *      },
     * )
     */
    public function actionCheckPhone()
    {
        $model = new LoginOtpForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            Yii::error($model->errors);
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->checkPhone();
    }

    /**
     * @SWG\Post(
     *      path="/auth/send-otp",
     *      operationId="send-otp",
     *      summary="User send-otp",
     *      description="Api user send-otp",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Auth"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/LoginOtpForm"),
     *      ),
     *      @SWG\Response(response=200, description="Send Otp login : staging mặc định otp_code = 1234",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="code", type="string", default="mã xác thực"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *         }
     *      },
     * )
     */
    public function actionSendOtp()
    {
        $model = new LoginOtpForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            Yii::error($model->errors);
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->sendOtp();
    }

    /**
     * @SWG\Post(
     *      path="/auth/login-otp",
     *      operationId="login-otp",
     *      summary="User login-otp",
     *      description="Api user login-otp",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Auth"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/LoginOtpForm"),
     *      ),
     *      @SWG\Response(response=200, description="login Otp",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="access_token", type="string", default="string"),
     *                  @SWG\Property(property="refresh_token", type="string", default="string"),
     *                  @SWG\Property(property="info_user", type="object", ref="#/definitions/ResidentUserResponse"),
     *                  @SWG\Property(property="apartments", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ApartmentMapResidentUserResponse"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *         }
     *      },
     * )
     */
    public function actionLoginOtp()
    {
        $model = new LoginOtpForm();
        $model->setScenario('login');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            Yii::error($model->errors);
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->login();
    }

    /**
     * @SWG\Post(
     *      path="/auth/login-password",
     *      operationId="login-password",
     *      summary="User login-password",
     *      description="Api user login-password",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Auth"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/LoginPasswordForm"),
     *      ),
     *      @SWG\Response(response=200, description="login password",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="access_token", type="string", default="string"),
     *                  @SWG\Property(property="refresh_token", type="string", default="string"),
     *                  @SWG\Property(property="info_user", type="object", ref="#/definitions/ResidentUserResponse"),
     *                  @SWG\Property(property="apartments", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ApartmentMapResidentUserResponse"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *         }
     *      },
     * )
     */
    public function actionLoginPassword()
    {
        $model = new LoginPasswordForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            Yii::error($model->errors);
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->login();
    }

    /**
     * @SWG\Post(
     *      path="/auth/login",
     *      operationId="login",
     *      summary="User Login",
     *      description="Api user login",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Auth"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/LoginForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="access_token", type="string", default="string"),
     *                  @SWG\Property(property="refresh_token", type="string", default="string"),
     *                  @SWG\Property(property="info_user", type="object", ref="#/definitions/ResidentUserResponse"),
     *                  @SWG\Property(property="apartments", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ApartmentMapResidentUserResponse"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *         }
     *      },
     * )
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams, '');
        Yii::info($model->attributes);

        return $model->login();
    }

    /**
     * @SWG\Post(
     *      path="/auth/logout",
     *      operationId="logout",
     *      summary="User logout",
     *      description="Api user logout",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Auth"},
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
    public function actionLogout()
    {
        $logout = new LogoutForm();
        return $logout->logout();
    }

    /**
     * @SWG\Post(
     *      path="/auth/refresh-token",
     *      operationId="refresh-token",
     *      summary="User refresh-token",
     *      description="Api user refresh-token",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Auth"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentUserRefreshTokenForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="access_token", type="string", default="string"),
     *                  @SWG\Property(property="refresh_token", type="string", default="string"),
     *                  @SWG\Property(property="info_user", type="object", ref="#/definitions/ResidentUserResponse"),
     *                  @SWG\Property(property="apartments", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ApartmentMapResidentUserResponse"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *         }
     *      },
     * )
     */

    public function actionRefreshToken()
    {
        $model = new ResidentUserRefreshTokenForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->refreshAccessToken();
    }
    /**
     * @SWG\Post(
     *      path="/auth/change-password-by-otp",
     *      operationId="change-password-by-otp",
     *      summary="User change-password-by-otp",
     *      description="Api user change-password-by-otp",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Auth"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/LoginForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="access_token", type="string", default="string"),
     *                  @SWG\Property(property="refresh_token", type="string", default="string"),
     *                  @SWG\Property(property="info_user", type="object", ref="#/definitions/ResidentUserResponse"),
     *                  @SWG\Property(property="apartments", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ApartmentMapResidentUserResponse"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *         }
     *      },
     * )
     */
    public function actionChangePasswordByOtp()
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
        return $model->setPasswordByOtp();
    }
}
