<?php
namespace frontend\controllers;

use common\helpers\ErrorCode;
use frontend\models\DavisStatusForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class DavisController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'status',
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
     *      path="/davis/status",
     *      operationId="Status",
     *      summary="Davis status",
     *      description="Api user Identified",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Davis"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/DavisStatusForm"),
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
     *         }
     *      },
     * )
     */
    public function actionStatus()
    {
        $model = new DavisStatusForm();
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->status();
    }
}
