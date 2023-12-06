<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use frontend\models\AnnouncementTemplateForm;
use frontend\models\AnnouncementTemplateResponse;
use Yii;
use common\models\AnnouncementTemplate;
use frontend\models\AnnouncementTemplateSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AnnouncementTemplateController implements the CRUD actions for AnnouncementTemplate model.
 */
class AnnouncementTemplateController extends ApiController
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
     * @SWG\Get(
     *      path="/announcement-template/list",
     *      operationId="AnnouncementTemplate",
     *      summary="users",
     *      description="Api List AnnouncementTemplate",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementTemplate"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="name", type="string", default="", description="Tên"),
     *      @SWG\Parameter(in="query", name="type", type="string", default="", description="0 - Thông báo thường, 1 - Thông báo phí, 2 - nhắc nợ lần một, 3 - nhắc nợ lần hai, 4 - nhắc nợ lần 3 , 5 - thông báo tạo ngừng dịch vụ"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên Apartment <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="AnnouncementTemplate",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/AnnouncementTemplateResponse"),
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
    public function actionList()
    {
        $modelSearch = new AnnouncementTemplateSearch();
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

    /**
     * @SWG\Get(
     *      path="/announcement-template/detail",
     *      operationId="AnnouncementTemplate detail",
     *      summary="AnnouncementTemplate",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementTemplate"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id template" ),
     *      @SWG\Parameter( in="query", name="type", type="integer", default="1", description="type" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/AnnouncementTemplateResponse"),
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
    public function actionDetail()
    {
        $buildingCluster = \Yii::$app->building->BuildingCluster;
        $id = Yii::$app->request->get("id", 0);
        $type = Yii::$app->request->get("type", 0);
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        if(is_numeric($type)){ $type = (int)$type;}
        if(!empty($id)){
            return AnnouncementTemplateResponse::find()
                ->where(['id' => $id])
                ->andWhere(['or', ['building_cluster_id' => null], ['building_cluster_id' => $buildingCluster->id]])->one();
        }else{
            return AnnouncementTemplateResponse::find()
                ->where(['type' => $type])
                ->andWhere(['or', ['building_cluster_id' => null], ['building_cluster_id' => $buildingCluster->id]])->one();
        }
    }

    /**
     * @SWG\Post(
     *      path="/announcement-template/create",
     *      operationId="AnnouncementTemplate create",
     *      summary="AnnouncementTemplate create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementTemplate"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AnnouncementTemplateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="message", type="string"),
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
        $model = new AnnouncementTemplateForm();
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
     *      path="/announcement-template/update",
     *      description="AnnouncementTemplate update",
     *      operationId="AnnouncementTemplate update",
     *      summary="AnnouncementTemplate update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementTemplate"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AnnouncementTemplateForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/AnnouncementTemplateResponse"),
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
    public function actionUpdate()
    {
        $model = new AnnouncementTemplateForm();
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
     *      path="/announcement-template/delete",
     *      operationId="AnnouncementTemplate delete",
     *      summary="AnnouncementTemplate delete",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementTemplate"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AnnouncementTemplateForm"),
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
        $model = new AnnouncementTemplateForm();
        $model->setScenario('delete');
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->delete();
    }

}
