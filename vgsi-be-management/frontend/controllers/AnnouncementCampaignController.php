<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\AnnouncementSurvey;
use frontend\models\AnnouncementCampaignCreateForm;
use frontend\models\AnnouncementCampaignDeleteForm;
use frontend\models\AnnouncementCampaignExtendForm;
use frontend\models\AnnouncementCampaignResponse;
use frontend\models\AnnouncementCampaignSearch;
use frontend\models\AnnouncementItemSearch;
use frontend\models\AnnouncementSurveySearch;
use frontend\models\ServiceAnnouncementCampaignExportForm;
use Yii;

class AnnouncementCampaignController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [];
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
     *      path="/announcement-campaign/create",
     *      operationId="AnnouncementCampaign create",
     *      summary="AnnouncementCampaign create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AnnouncementCampaignCreateForm"),
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
     *             "http_bearer_auth": {},
     *             "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionCreate()
    {
        $model = new AnnouncementCampaignCreateForm();
        $model->load(Yii::$app->request->bodyParams, '');
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

    /**
     * @SWG\Post(
     *      path="/announcement-campaign/update",
     *      description="AnnouncementCampaign update",
     *      operationId="AnnouncementCampaign update",
     *      summary="AnnouncementCampaign update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AnnouncementCampaignCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/AnnouncementCampaignResponse"),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "http_bearer_auth": {},
     *             "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionUpdate()
    {
        $model = new AnnouncementCampaignCreateForm();
        $model->setScenario('update');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->update();
    }

    /**
     * @SWG\Post(
     *      path="/announcement-campaign/extend",
     *      description="AnnouncementCampaign extend",
     *      operationId="AnnouncementCampaign extend",
     *      summary="AnnouncementCampaign extend",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AnnouncementCampaignExtendForm"),
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
     *             "http_bearer_auth": {},
     *             "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionExtend()
    {
        $model = new AnnouncementCampaignExtendForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->extend();
    }

    /**
     * @SWG\Get(
     *      path="/announcement-campaign/report-chart",
     *      description="AnnouncementCampaign report-chart",
     *      operationId="AnnouncementCampaign report-chart",
     *      summary="AnnouncementCampaign report-chart",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id AnnouncementCampaign" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="bar_chart", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="status", type="integer"),
     *                          @SWG\Property(property="total_apartment_capacity", type="number", description="tổng diện tích căn hộ"),
     *                          @SWG\Property(property="total_answer", type="integer"),
     *                      ),
     *                  ),
     *                  @SWG\Property(property="pie_chart", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="report_day", type="string", default="2023-10-10", description="ngày làm khảo sát"),
     *                          @SWG\Property(property="total", type="integer", description="số người thực hiện"),
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "http_bearer_auth": {},
     *             "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionReportChart($id)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $sql = Yii::$app->db;
        $sums = AnnouncementSurvey::find()->select('status, count(*) as total_answer, sum(apartment_capacity) as apartment_capacity')->where(['building_cluster_id' => $buildingCluster->id, 'announcement_campaign_id' => $id])->groupBy(['status'])->all();
        $pieChart = [];
        foreach ($sums as $sum) {
            $pieChart[] = [
                'status' => $sum->status,
                'total_apartment_capacity' => $sum->apartment_capacity,
                'total_answer' => $sum->total_answer,
            ];
        }

        $countByService = $sql->createCommand("select FROM_UNIXTIME(updated_at, '%Y-%m-%d') as report_day, count(*) as total from announcement_survey where building_cluster_id = ".$buildingCluster->id." and announcement_campaign_id = $id and status in (".AnnouncementSurvey::STATUS_AGREE.",".AnnouncementSurvey::STATUS_DISAGREE.") group by FROM_UNIXTIME(updated_at, '%Y-%m-%d') order by FROM_UNIXTIME(updated_at, '%Y-%m-%d') DESC")->queryAll();
        $barChart = [];
        foreach ($countByService as $row){
            $barChart[] = [
                'report_day' => $row['report_day'],
                'total' => (int)$row['total'],
            ];
        }
        return [
            'bar_chart' => $barChart,
            'pie_chart' => $pieChart
        ];
    }

    /**
     * @SWG\Get(
     *      path="/announcement-campaign/survey-answer",
     *      operationId="AnnouncementCampaign survey answer",
     *      summary="AnnouncementCampaign survey answer",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="status", type="integer", default="", description="Trạng thái: 0 chưa làm, 1 đồng ý, 2 không đồng ý"),
     *      @SWG\Parameter(in="query", name="announcement_campaign_id", type="integer", default="", description="announcement campaign id"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default="", description="apartment id"),
     *      @SWG\Parameter(in="query", name="resident_user_id", type="integer", default="", description="resident user id"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên AnnouncementCampaign <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/AnnouncementSurveyResponse"),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "http_bearer_auth": {},
     *             "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionSurveyAnswer()
    {
        $AnnouncementCampaignSearch = new AnnouncementSurveySearch();
        $dataProvider = $AnnouncementCampaignSearch->search(Yii::$app->request->queryParams);
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

    /**
     * @SWG\Get(
     *      path="/announcement-campaign/list",
     *      operationId="AnnouncementCampaign list",
     *      summary="AnnouncementCampaign list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="title", type="string", default="", description="Tiêu đề"),
     *      @SWG\Parameter(in="query", name="type_in", type="string", default="0,1", description="các loại cần lấy"),
     *      @SWG\Parameter(in="query", name="type_not_in", type="string", default="3,4", description="các loại không cần lấy"),
     *      @SWG\Parameter(in="query", name="status", type="integer", default="", description="Trạng thái: 0 - nháp, 1 - công khai, 2 - hẹn giờ công khai"),
     *      @SWG\Parameter(in="query", name="start_time_from", type="integer", default="", description="Ngày tạo"),
     *      @SWG\Parameter(in="query", name="start_time_to", type="integer", default="", description="Ngày tạo"),
     *      @SWG\Parameter(in="query", name="announcement_category_id", type="integer", default="", description="announcement category id"),
     *      @SWG\Parameter(in="query", name="management_user_name", type="string", default="", description="tên người tạo"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên AnnouncementCampaign <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/AnnouncementCampaignResponse"),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "http_bearer_auth": {},
     *             "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionList()
    {
        $AnnouncementCampaignSearch = new AnnouncementCampaignSearch();
        $dataProvider = $AnnouncementCampaignSearch->search(Yii::$app->request->queryParams);
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

    /**
     * @SWG\Get(
     *      path="/announcement-campaign/detail?id={id}",
     *      operationId="AnnouncementCampaign detail",
     *      summary="AnnouncementCampaign",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id AnnouncementCampaign" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/AnnouncementCampaignResponse"),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *              "http_bearer_auth": {},
     *              "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionDetail()
    {
        $user = Yii::$app->user->getIdentity();
        $id = Yii::$app->request->get("id", 0);
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $post = AnnouncementCampaignResponse::findOne(['id' => $id, 'building_cluster_id' => $user->building_cluster_id]);
        return $post;
    }

    /**
     * @SWG\Get(
     *      path="/announcement-campaign/list-item",
     *      operationId="AnnouncementCampaign list item",
     *      summary="AnnouncementCampaign list item",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="announcement_campaign_id", type="integer", default="", description="announcement campaign id"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên AnnouncementCampaign <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/AnnouncementItemResponse"),
     *                  ),
     *                  @SWG\Property(property="total_count", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="total_apartment", type="integer", default=1),
     *                          @SWG\Property(property="total_email", type="integer", default=1),
     *                          @SWG\Property(property="total_app", type="integer", default=1),
     *                          @SWG\Property(property="total_sms", type="integer", default=1),
     *                      ),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "http_bearer_auth": {},
     *             "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionListItem()
    {
        $AnnouncementItemSearch = new AnnouncementItemSearch();
        $data = $AnnouncementItemSearch->search(Yii::$app->request->queryParams);
        $dataCount = $data['dataCount'];
        $dataProvider = $data['dataProvider'];
        return [
            'items' => $dataProvider->getModels(),
            'total_count' => $dataCount,
            'pagination' => [
                "totalCount" => $dataProvider->getTotalCount(),
                "pageCount" => $dataProvider->pagination->pageCount,
                "currentPage" => $dataProvider->pagination->page + 1,
                "pageSize" => $dataProvider->pagination->pageSize,
            ]
        ];
    }

    /**
     * @SWG\Get(
     *      path="/announcement-campaign/list-apartment-send",
     *      operationId="AnnouncementCampaign list apartment-send",
     *      summary="AnnouncementCampaign list apartment-send",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="building_area_ids", type="string", default="1,2", description="building area ids"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên AnnouncementCampaign <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/AnnouncementSendResponse"),
     *                  ),
     *                  @SWG\Property(property="total_count", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="total_apartment", type="integer", default=1),
     *                          @SWG\Property(property="total_email", type="integer", default=1),
     *                          @SWG\Property(property="total_app", type="integer", default=1),
     *                          @SWG\Property(property="total_sms", type="integer", default=1),
     *                      ),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "http_bearer_auth": {},
     *             "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionListApartmentSend()
    {
        $AnnouncementItemSearch = new AnnouncementItemSearch();
        $data = $AnnouncementItemSearch->searchSend(Yii::$app->request->queryParams);
        $dataCount = $data['dataCount'];
        $dataProvider = $data['dataProvider'];
        return [
            'items' => $dataProvider->getModels(),
            'total_count' => $dataCount,
            'pagination' => [
                "totalCount" => $dataProvider->getTotalCount(),
                "pageCount" => $dataProvider->pagination->pageCount,
                "currentPage" => $dataProvider->pagination->page + 1,
                "pageSize" => $dataProvider->pagination->pageSize,
            ]
        ];
    }

    /**
     * @SWG\Get(
     *      path="/announcement-campaign/list-apartment-send-new",
     *      operationId="AnnouncementCampaign list apartment-send-new",
     *      summary="AnnouncementCampaign list apartment-send-new",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="building_area_ids", type="string", default="1,2", description="building area ids"),
     *      @SWG\Parameter(in="query", name="targets", type="string", default="1,2", description="đối tượng nhận thông báo"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên AnnouncementCampaign <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/AnnouncementSendResponse"),
     *                  ),
     *                  @SWG\Property(property="total_count", type="array",
     *                      @SWG\Items(type="object",
     *                          @SWG\Property(property="total_apartment", type="integer", default=1),
     *                          @SWG\Property(property="total_email", type="integer", default=1),
     *                          @SWG\Property(property="total_app", type="integer", default=1),
     *                          @SWG\Property(property="total_sms", type="integer", default=1),
     *                      ),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *             "http_bearer_auth": {},
     *             "domain_origin": {}
     *         }
     *      },
     * )
     */
    public function actionListApartmentSendNew()
    {
        $AnnouncementItemSearch = new AnnouncementItemSearch();
        $data = $AnnouncementItemSearch->searchSendNew(Yii::$app->request->queryParams);
        $dataCount = $data['dataCount'];
        $dataProvider = $data['dataProvider'];
        return [
            'items' => $dataProvider->getModels(),
            'total_count' => $dataCount,
            'pagination' => [
                "totalCount" => $dataProvider->getTotalCount(),
                "pageCount" => $dataProvider->pagination->pageCount,
                "currentPage" => $dataProvider->pagination->page + 1,
                "pageSize" => $dataProvider->pagination->pageSize,
            ]
        ];
    }

    /**
     * @SWG\Post(
     *      path="/announcement-campaign/delete",
     *      operationId="AnnouncementCampaign delete",
     *      summary="AnnouncementCampaign delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementCampaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AnnouncementCampaignDeleteForm"),
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
    public function actionDelete()
    {
        $model = new AnnouncementCampaignDeleteForm();
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }
     /**
     * @SWG\Get(
     *      path="/announcement-campaign/export-file",
     *      description="AnnouncementCampaign delete",
     *      operationId="AnnouncementCampaign delete",
     *      summary="Announcement export",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Announcement campaign"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="Name"),
     *      @SWG\Parameter(in="query", name="email", type="string", default="", description="Email"),
     *      @SWG\Parameter(in="query", name="auth_group_id", type="integer", default="", description="auth group id"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="file_path", type="string"),
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
    public function actionExportFile()
    {
        $model = new ServiceAnnouncementCampaignExportForm();
        return $model->export(Yii::$app->request->queryParams, true);
    }
}
