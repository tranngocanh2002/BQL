<?php

namespace pay\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use pay\models\PaymentVnpayForm;
use common\helpers\ErrorCode;

class PaymentVnpayController extends Controller
{

    public function actionIpn()
    {
        $model = new PaymentVnpayForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('pay', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->ipn();
    }
}