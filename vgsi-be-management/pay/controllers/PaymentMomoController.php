<?php

namespace pay\controllers;

use common\helpers\ErrorCode;
use pay\models\PaymentMomoForm;
use pay\models\PaymentMomoNotifyForm;
use pay\models\PaymentMomoResultForm;
use Yii;

class PaymentMomoController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
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
     *      path="/payment-momo/create",
     *      operationId="PaymentMomo create",
     *      summary="PaymentMomo create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"PaymentMomo"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/PaymentMomoForm"),
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
    public function actionCreate()
    {
        $model = new PaymentMomoForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('pay', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->create();
    }

    public function actionNotify()
    {
        Yii::info(Yii::$app->request->queryParams);
        $model = new PaymentMomoNotifyForm();
        $model->load(Yii::$app->request->queryParams, '');
        if (!$model->validate()) {
            Yii::error($model->errors);
            return [
                'success' => false,
                'message' => Yii::t('pay', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->ipn();
    }

    public function actionResult()
    {
        Yii::info(Yii::$app->request->queryParams);
        $model = new PaymentMomoResultForm();
        return $model->result(Yii::$app->request->queryParams);
    }
}
