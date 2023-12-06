<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\ApartmentMapResidentUser;
use common\models\Apartment;
use common\models\BuildingArea;
use common\models\City;
use frontend\models\ReportAnnouncementResponse;
use frontend\models\ReportCountAllResponseForm;
use frontend\models\ReportCountApartmentResponseForm;
use frontend\models\ReportCountResidentResponseForm;
use frontend\models\ReportRequestForm;
use frontend\models\ReportServiceBookingRevenueForm;
use frontend\models\ReportServiceFeeForm;
use frontend\models\ServiceBookingReportWeekSearch;
use frontend\models\MaintenanceDeviceSearch;
use frontend\models\ReportCountMaintainEquipmentResponse;
use frontend\models\ServicePaymentFeeSearch;
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @SWG\Get(
     *      path="/report/count-all",
     *      operationId="Report count all",
     *      summary="Report count all",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ReportCountAllResponseForm"),
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
    public function actionCountAll()
    {
        $model = new ReportCountAllResponseForm();
//        $model->load(Yii::$app->request->bodyParams, '');
//        if (!$model->validate()) {
//            return [
//                'success' => false,
//                'message' => Yii::t('frontend', "Invalid data"),
//                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                'errors' => $model->getErrors()
//            ];
//        }
        return $model->countAll();
    }

    /**
     * @SWG\Get(
     *      path="/report/request-by-day?from_day={from_day}&to_day={to_day}",
     *      operationId="Report request-by-day",
     *      summary="Report request-by-day",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="from_day", type="integer", default="1", description="from_day" ),
     *      @SWG\Parameter( in="query", name="to_day", type="integer", default="1", description="to_day" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ReportRequestForm"),
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
    public function actionRequestByDay()
    {
        $model = new ReportRequestForm();
        return $model->byDay(Yii::$app->request->queryParams);
    }

    /**
     * @SWG\Get(
     *      path="/report/service-fee-by-day",
     *      operationId="Report service-fee-by-day",
     *      summary="Report service-fee-by-day",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="from_month", type="integer", default="1", description="month" ),
     *      @SWG\Parameter( in="query", name="to_month", type="integer", default="1", description="month" ),
     *      @SWG\Parameter( in="query", name="type", type="integer", default="0", description="0: month, 1: day" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="array",
     *                  @SWG\Items(type="object", ref="#/definitions/ReportServiceFeeForm"),
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
    public function actionServiceFeeByDay()
    {
        $model = new ReportServiceFeeForm();
        return $model->byMonth(Yii::$app->request->queryParams);
    }

    /**
     * @SWG\Get(
     *      path="/report/announcement-recent",
     *      operationId="Report service-fee-by-day",
     *      summary="Report service-fee-by-day",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="array",
     *                  @SWG\Items(type="object", ref="#/definitions/ReportAnnouncementResponse"),
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
    public function actionAnnouncementRecent()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        return ReportAnnouncementResponse::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_send' => AnnouncementCampaign::IS_SEND])->limit(10)->all();
    }

    /**
     * @SWG\Get(
     *      path="/report/service-booking-revenue",
     *      operationId="Report service-booking-revenue",
     *      summary="Report service-booking-revenue",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="service_utility_free_id", type="integer", default="1", description="id dịch vụ" ),
     *      @SWG\Parameter(in="query", name="start_date", type="integer", default="", description="thoi gian bat dau"),
     *      @SWG\Parameter(in="query", name="end_date", type="integer", default="", description="thoi gian ket thuc"),
     *      @SWG\Response(response=200, description="tổng doanh thu của dịch vụ booking",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="array",
     *                   @SWG\Items(type="object", ref="#/definitions/ReportServiceBookingRevenueForm"),
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
    public function actionServiceBookingRevenue()
    {
        $model = new ReportServiceBookingRevenueForm();
        return $model->revenue(Yii::$app->request->queryParams);
    }

    /**
     * @SWG\Get(
     *      path="/report/service-booking-list-revenue",
     *      operationId="Report",
     *      summary="Report",
     *      description="Api List User",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="service_utility_free_id", type="integer", default="", description="dich vu"),
     *      @SWG\Parameter(in="query", name="start_date", type="integer", default="", description="thoi gian bat dau"),
     *      @SWG\Parameter(in="query", name="end_date", type="integer", default="", description="thoi gian ket thuc"),
     *      @SWG\Response(response=200, description="Danh sách tổng hợp doanh thu của dịch vụ theo tuần",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="array",
     *                  @SWG\Items(type="object",
     *                      @SWG\Property(property="date", type="integer"),
     *                      @SWG\Property(property="services", type="array",
     *                          @SWG\Items(type="object",
     *                              @SWG\Property(property="service_utility_free_id", type="integer"),
     *                              @SWG\Property(property="service_utility_free_name", type="string"),
     *                              @SWG\Property(property="total_price", type="integer"),
     *                          ),
     *                      ),
     *                  ),
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
    public function actionServiceBookingListRevenue()
    {
        $model = new ServiceBookingReportWeekSearch();
        return $model->search(Yii::$app->request->queryParams);
    }

    /**
     * @SWG\Get(
     *      path="/report/count-apartment",
     *      operationId="Report count apartment",
     *      summary="Report count apartment",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Thống kê loại căn hộ theo tòa",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ReportCountApartmentResponseForm"),
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
    public function actionCountApartment()
    {
        $model = new ReportCountApartmentResponseForm();
        return $model->countApartment();
    }

    /**
     * @SWG\Get(
     *      path="/report/count-resident",
     *      operationId="Report count resident",
     *      summary="Report count resident",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Thông kê số cư dân trong từng khu vực",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="array",
     *                  @SWG\Items(type="object",
     *                      @SWG\Property(property="building_area_id", type="integer"),
     *                      @SWG\Property(property="building_area_name", type="string"),
     *                      @SWG\Property(property="total_count", type="integer"),
     *                  ),
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
    public function sumArea(&$building_area_ids, $buildingCluster, $building, $buildingArea)
    {
        $buildings = BuildingArea::find()->where(['building_cluster_id' => $buildingCluster->id, 'parent_id' => $building->id, 'is_deleted' => BuildingArea::NOT_DELETED])->all();
        if (empty($buildings))
            return $building_area_ids[$buildingArea->id][] = $building->id;
        else
            foreach ($buildings as $building) {
                $this->sumArea($building_area_ids, $buildingCluster, $building, $buildingArea);
            }
    }
    public function actionCountResident()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $apartmentByAreaData = [];
        $buildingAreas = BuildingArea::find()->where(['building_cluster_id' => $buildingCluster->id, 'parent_id' => null, 'is_deleted' => BuildingArea::NOT_DELETED])->all();
        $building_area_ids = [];
        foreach ($buildingAreas as $buildingArea) {
            $buildings = BuildingArea::find()->where(['building_cluster_id' => $buildingCluster->id, 'parent_id' => $buildingArea->id, 'is_deleted' => BuildingArea::NOT_DELETED])->all();
            foreach ($buildings as $building) {
                $this->sumArea($building_area_ids, $buildingCluster, $building, $buildingArea);
            }
        }
        foreach ($building_area_ids as $key => $building_area_id) {
            $countMember = Apartment::find()->where(['building_cluster_id' => $buildingCluster->id, 'building_area_id' => $building_area_id, 'status' => Apartment::STATUS_LIVE ,'is_deleted' => Apartment::NOT_DELETED])->sum('total_members');
            $buildingAreas = BuildingArea::find()->where(['id' => $key])->one();
            $apartmentByAreaData[] = [
                'building_area_id' => $key,
                'building_area_name' => $buildingAreas->name ?? "",
                'total_count' => (int)$countMember,
            ];
        }
        return $apartmentByAreaData;
    }

    /**
     * @SWG\Get(
     *      path="/report/count-maintain-equipment",
     *      operationId="Report count maintain equipment",
     *      summary="Report count maintain equipment",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Thống kê số lượng thiết bị bảo trì theo thời gian",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="array",
     *                  @SWG\Items(type="object",
     *                      @SWG\Property(property="building_area_id", type="integer"),
     *                      @SWG\Property(property="building_area_name", type="string"),
     *                      @SWG\Property(property="total_count", type="integer"),
     *                  ),
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
    public function actionCountMaintainEquipment()
    {
        $modelSearch = new MaintenanceDeviceSearch();
        $dataProviders = $modelSearch->searchMaintainEquipment(Yii::$app->request->queryParams);
        $aryDataProvider = [];
        foreach($dataProviders as $dataProvider)
        {
            $aryDataProvider[] = $dataProvider;
        }
        return [
            'items' => $aryDataProvider,
            'from_month' => Yii::$app->request->queryParams['from_month'] ?? null,
            'to_month'   => Yii::$app->request->queryParams['to_month'] ?? null
        ];
    }

    /**
     * @SWG\Get(
     *      path="/report/count-total-revenue",
     *      operationId="Report count total revenue",
     *      summary="Report count total revenue",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Report"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Tổng doanh thu theo tháng",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="array",
     *                  @SWG\Items(type="object",
     *                      @SWG\Property(property="building_area_id", type="integer"),
     *                      @SWG\Property(property="building_area_name", type="string"),
     *                      @SWG\Property(property="total_count", type="integer"),
     *                  ),
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
    public function actionCountTotalRevenue()
    {
        $modelSearch = new ServicePaymentFeeSearch();
        $data        = $modelSearch->getAllTotalRevenue(Yii::$app->request->queryParams);
        $data        = $data->getModels();
        $aryData     = [];
        foreach($data as $key => $value)
        {
            $aryData[$key]['price'] = $value->price;
            $aryData[$key]['service_id'] = $value->serviceMapManagement->service_id;
            $aryData[$key]['service_name'] = $value->serviceMapManagement->service_name;
            $aryData[$key]['service_name_en'] = $value->serviceMapManagement->service_name_en;
            $aryData[$key]['color'] = $value->serviceMapManagement->color;
        }
        $summedData = [];
        foreach ($aryData as $item) {
            $serviceId = $item['service_id'];
            if (!isset($summedData[$serviceId])) {
                $summedData[$serviceId] = $item;
                continue;
            }
            $summedData[$serviceId]['price'] += $item['price'];
        }

        $aryData = array_values($summedData);
        return [
            'item' => $aryData
        ];
    }
    
}
