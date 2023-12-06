<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\AnnouncementSurvey;
use common\models\ApartmentMapResidentUser;
use common\models\SurveyAnswer;
use resident\models\SurveyAnswerCreateForm;
use resident\models\SurveyAnswerResponse;
use Yii;

class SurveyAnswerController extends ApiController
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
     *      path="/survey-answer/create",
     *      operationId="SurveyAnswer create",
     *      summary="SurveyAnswer create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"SurveyAnswer"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/SurveyAnswerCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/SurveyAnswerResponse"),
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
    public function actionCreate()
    {
        $model = new SurveyAnswerCreateForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->create();
    }

    /**
     * @SWG\Get(
     *      path="/survey-answer/detail",
     *      operationId="SurveyAnswer detail",
     *      summary="SurveyAnswer",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"SurveyAnswer"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="announcement_campaign_id", type="integer", default="1", description="id announcement campaign" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/SurveyAnswerResponse"),
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
    public function actionDetail($announcement_campaign_id)
    {
        $sums = AnnouncementSurvey::find()->select('status, count(*) as total_answer, sum(apartment_capacity) as apartment_capacity')->where(['announcement_campaign_id' => $announcement_campaign_id])->groupBy(['status'])->all();
        $res = [];
        foreach ($sums as $sum) {
            $res[] = [
                'status' => (int)$sum->status,
                'total_apartment_capacity' => (double)$sum->apartment_capacity,
                'total_answer' => (int)$sum->total_answer,
            ];
        }
        return $res;
    }
}
