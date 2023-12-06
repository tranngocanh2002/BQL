<?php
namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Site controller
 * @SWG\Swagger (
 *      @SWG\Info(
 *          title = "Luci Developer API",
 *          version = "1.0.0",
 *          @SWG\Contact(
 *              email="luci@luci.vn"
 *          )
 *     ),
 *      schemes={"http", "https"},
 *      host=API_HOST,
 *      basePath="/"
 * )
 */

/**
 * @SWG\SecurityScheme(
 *      securityDefinition="api_app_key",
 *      type="apiKey",
 *      in="header",
 *      name="X-Luci-Api-Key"
 * )
 */

/**
 * @SWG\SecurityScheme(
 *      securityDefinition="domain_origin",
 *      type="apiKey",
 *      in="header",
 *      name="Domain-Origin"
 * )
 */
/**
 * @SWG\SecurityScheme(
 *      securityDefinition="http_bearer_auth",
 *      type="apiKey",
 *      in="header",
 *      name="Authorization"
 * )
 */

class SiteController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'index',
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['update'],
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
     *      path="/site",
     *      operationId="Index",
     *      summary="Index",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Index"},
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
     *         }
     *      },
     * )
     */
    public function actionIndex()
    {
        return [
            'name' => 'Yii Api',
            'description' => 'API phục vụ app',
            'email' => 'admin@enjoy.vn',
            'website' => 'enjoy.vn',
        ];
    }

}
