<?php
namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\Apartment;
use frontend\models\CheckOtpTokenForm;
use frontend\models\LogoutForm;
use frontend\models\ForgotPasswordForm;
use frontend\models\ManagementUserRefreshTokenForm;
use frontend\models\PdfTemplateCreateForm;
use frontend\models\ResetPasswordForm;
use riskivy\captcha\CaptchaHelper;
use Yii;
use frontend\models\LoginForm;
use yii\filters\AccessControl;

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
            'forgot-password',
            'check-otp-token',
            'reset-password',
            'generate-captcha',
            'refresh-token',
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['logout'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['logout'],
                    'roles' => ['@'],
                ],
            ],
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
     *                  @SWG\Property(property="auth_group", ref="#/definitions/AuthGroupResponse"),
     *                  @SWG\Property(property="building_info", ref="#/definitions/BuildingClusterResponse"),
     *              ),
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
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams, '');
        Yii::info($model->attributes);

        return $model->login($this->is_web);
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
     *             "domain_origin": {},
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
     *     path="/auth/forgot-password",
     *     operationId="forgot-password",
     *     summary="user forgot-password",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Auth"},
     *     @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *     @SWG\Parameter(in="body", name="body", required=true,
     *         @SWG\Schema(ref="#/definitions/ForgotPasswordForm"),
     *     ),
     *     @SWG\Response(response=200, description="Info",
     *         @SWG\Schema(type="object",
     *             @SWG\Property(property="success", type="boolean"),
     *             @SWG\Property(property="statusCode", type="integer", default=200),
     *              ),
     *         ),
     *     ),
     *     security={
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *         }
     *     },
     *  )
     */
    function actionForgotPassword() {
        $modelForm = new ForgotPasswordForm();
        if ($modelForm->load(Yii::$app->request->post(), '') && $modelForm->validate()) {
            return $modelForm->sendEmail($this->is_web);
        }
        return [
            'success' => false,
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            'errors' => $modelForm->errors
        ];
    }

    /**
     * @SWG\Post(
     *     path="/auth/check-otp-token",
     *     operationId="check-otp-token",
     *     summary="user check-otp-token",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Auth"},
     *     @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *     @SWG\Parameter(in="body", name="body", required=true,
     *         @SWG\Schema(ref="#/definitions/CheckOtpTokenForm"),
     *     ),
     *     @SWG\Response(response=200, description="Info",
     *         @SWG\Schema(type="object",
     *             @SWG\Property(property="success", type="boolean"),
     *             @SWG\Property(property="statusCode", type="integer", default=200),
     *         ),
     *     ),
     *     security={
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *         }
     *     },
     *  )
     */
    function actionCheckOtpToken() {
        $modelForm = new CheckOtpTokenForm();
        if ($modelForm->load(Yii::$app->request->post(), '') && $modelForm->validate()) {
            return $modelForm->check();
        }
        return [
            'success' => false,
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            'errors' => $modelForm->errors
        ];
    }

    /**
     * @SWG\Post(
     *     path="/auth/reset-password",
     *     operationId="reset-password",
     *     summary="user reset-password",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Auth"},
     *     @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *     @SWG\Parameter(in="body", name="body", required=true,
     *         @SWG\Schema(ref="#/definitions/ResetPasswordForm"),
     *     ),
     *     @SWG\Response(response=200, description="Info",
     *         @SWG\Schema(type="object",
     *             @SWG\Property(property="success", type="boolean"),
     *             @SWG\Property(property="statusCode", type="integer", default=200),
     *             @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="refresh_token", type="string", default="string"),
     *             ),
     *         ),
     *     ),
     *     security={
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *         }
     *     },
     *  )
     */
    function actionResetPassword() {
        $modelForm = new ResetPasswordForm();
        if ($modelForm->load(Yii::$app->request->post(), '') && $modelForm->validate()) {
            return $modelForm->resetPassword();
        }
        return [
            'success' => false,
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            'errors' => $modelForm->errors
        ];
    }

    /**
     * @SWG\Get(
     *      path="/auth/generate-captcha",
     *      operationId="generate-captcha",
     *      summary="generate-captcha",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Auth"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="captchaImage", type="string"),
     *              ),
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
    public function actionGenerateCaptcha()
    {
        return [
            'captchaImage' => (new CaptchaHelper())->generateImage()
        ];
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
     *          @SWG\Schema(ref="#/definitions/ManagementUserRefreshTokenForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="access_token", type="string", default="string"),
     *                  @SWG\Property(property="refresh_token", type="string", default="string"),
     *                  @SWG\Property(property="auth_group", ref="#/definitions/AuthGroupResponse"),
     *              ),
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
    public function actionRefreshToken()
    {
        $model = new ManagementUserRefreshTokenForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->refreshAccessToken();
    }

    public function actionPdf($apartment_id, $campaign_type)
    {
        $this->layout = 'print';
        $model = new PdfTemplateCreateForm();
        $model->apartment_id = (int)$apartment_id;
        $model->campaign_type = (int)$campaign_type;
        die($model->gen());
//        $apartment = Apartment::findOne(['id' => (int)$apartment_id]);
//        $content_pdf = $this->render('/pdf/fee_new_db', ['apartment' => $apartment, 'campaign_type' => (int)$campaign_type]);
////        $content_pdf = $this->render('/pdf/test');
//        die($content_pdf);
    }
}
