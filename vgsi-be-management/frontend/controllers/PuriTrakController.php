<?php
namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ResidentUserIdentification;
use common\models\UploadForm;
use frontend\models\IdentifiedEventForm;
use frontend\models\IdentifiedStatusForm;
use frontend\models\PuriTrakHistorySearch;
use frontend\models\PuriTrakStatusForm;
use frontend\models\ResidentUserIdentificationSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class PuriTrakController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
//            'status',
        ];
//        $behaviors['access'] = [
//            'class' => AccessControl::className(),
//            'only' => ['logout'],
//            'rules' => [
//                [
//                    'allow' => true,
//                    'actions' => ['logout'],
//                    'roles' => ['@'],
//                ],
//            ],
//        ];
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
     *      path="/puri-trak/status",
     *      operationId="Status",
     *      summary="PuriTrak status",
     *      description="Api user Identified",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"PuriTrak"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/PuriTrakStatusForm"),
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
        $model = new PuriTrakStatusForm();
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->status();
    }

    /**
     * @SWG\Get(
     *      path="/puri-trak/list-history",
     *      operationId="PuriTrak list history",
     *      summary="PuriTrak list history",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"PuriTrak"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="start_datetime", type="integer", default=1, description="thời gian bắt đầu"),
     *      @SWG\Parameter(in="query", name="end_datetime", type="integer", default=1, description="thời gian kết thúc"),
     *      @SWG\Parameter(in="query", name="device_id", type="string", default="", description="device id"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/PuriTrakHistoryResponse"),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
     *              ),
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
    public function actionListHistory()
    {
        $modelSearch = new PuriTrakHistorySearch();
        $dataProvider = $modelSearch->search(Yii::$app->request->queryParams);
        return [
            'items' => $dataProvider->getModels(),
            'pagination' => [
                "totalCount" => $dataProvider->getTotalCount(),
                "pageCount" => $dataProvider->pagination->pageCount,
                "currentPage" => $dataProvider->pagination->page + 1,
                "pageSize" => $dataProvider->pagination->pageSize,
            ]
        ];
    }
}
