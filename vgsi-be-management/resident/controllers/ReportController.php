<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use resident\models\ReportServiceFeeForm;
use Yii;

class ReportController extends ApiController
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
        ];
    }

    /**
     * @SWG\Get(
     *      path="/report/service-fee-by-month",
     *      operationId="Report service-fee-by-month",
     *      summary="Report service-fee-by-month",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="căn hộ" ),
     *      @SWG\Parameter( in="query", name="month", type="integer", default="1", description="month" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ReportServiceFeeForm"),
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
    public function actionServiceFeeByMonth()
    {
        $model = new ReportServiceFeeForm();
        $model->setScenario('byMonth');
        $model->load(Yii::$app->request->queryParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->byMonth();
    }

    /**
     * @SWG\Get(
     *      path="/report/service-fee-by-range-month",
     *      operationId="Report service-fee-by-range-month",
     *      summary="Report service-fee-by-range-month",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="căn hộ" ),
     *      @SWG\Parameter( in="query", name="service_map_management_id", type="integer", default="1", description="lấy theo từng dịch vụ" ),
     *      @SWG\Parameter( in="query", name="start_month", type="integer", default="1", description="start month" ),
     *      @SWG\Parameter( in="query", name="end_month", type="integer", default="1", description="end month" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="array",
     *                  @SWG\Items(type="object",
     *                      @SWG\Property(property="month_str", type="string"),
     *                      @SWG\Property(property="month", type="integer"),
     *                      @SWG\Property(property="total_fee", type="integer"),
     *                  )
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
    public function actionServiceFeeByRangeMonth()
    {
        $model = new ReportServiceFeeForm();
        $model->setScenario('byRangeMonth');
        $model->load(Yii::$app->request->queryParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->byRangeMonth();
    }
}
