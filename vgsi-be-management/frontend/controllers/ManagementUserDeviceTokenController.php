<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ManagementUserDeviceToken;
use frontend\models\ManagementUserDeviceTokenCreateForm;
use Yii;

class ManagementUserDeviceTokenController extends ApiController
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
     *      path="/management-user-device-token/create",
     *      operationId=" ManagementUserDeviceToken create",
     *      summary=" ManagementUserDeviceToken create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ManagementUserDeviceToken"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ManagementUserDeviceTokenCreateForm"),
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
        $model = new ManagementUserDeviceTokenCreateForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if($this->is_web){
            $model->type = ManagementUserDeviceToken::TYPE_WEB;
        }
        if(!$this->is_web){
            $model->type = ManagementUserDeviceToken::TYPE_APP;
        }
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
}
