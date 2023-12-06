<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use common\models\ServicePaymentFee;
use resident\models\AnnouncementItemIsHiddenForm;
use resident\models\AnnouncementItemResponse;
use resident\models\AnnouncementCampaignResponse;
use resident\models\AnnouncementItemSearch;
use resident\models\AnnouncementCampaignSearch;
use Yii;

class AnnouncementCampaignController extends ApiController
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
     *      path="/announcement-item/list?apartment_id={apartment_id}",
     *      operationId="AnnouncementItem list",
     *      summary="AnnouncementItem list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementItem"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default=1, description="id căn hộ"),
     *      @SWG\Parameter(in="query", name="is_hidden", type="integer", default=0, description="is_hidden : 0 - là hiện, 1 - là ẩn"),
     *      @SWG\Parameter(in="query", name="title", type="string", default="", description="Title"),
     *      @SWG\Parameter(in="query", name="perPage", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-announcement_campaign.title</b>: Tiêu đề thông báo <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/AnnouncementItemResponse"),
     *                  ),
     *                  @SWG\Property(property="pagination", type="object", ref="#/definitions/Pagination"),
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
    public function actionList()
    {
        $searchModel = new AnnouncementCampaignSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
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
     *      path="/announcement-item/detail?id={id}",
     *      operationId="AnnouncementItem detail",
     *      summary="AnnouncementItem",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementItem"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id AnnouncementItem" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/AnnouncementItemResponse"),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *              "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionDetail($id)
    {
        $announcementItem = AnnouncementCampaign::findOne(['id' => (int)$id]);
        return $announcementItem;
    }

    /**
     * @SWG\Get(
     *      path="/announcement-item/count-unread?apartment_id={apartment_id}",
     *      operationId="AnnouncementItem count-unread",
     *      summary="AnnouncementItem",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementItem"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment_id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="total_unread", type="integer", default=0),
     *                  @SWG\Property(property="total_fee_unpaid", type="integer", default=0, description="Tổng phí chưa thanh toán"),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *              "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionCountUnread()
    {
        $user = Yii::$app->user->getIdentity();
        $apartment_id = Yii::$app->request->get("apartment_id", 0);
        $apartment_id = (int)$apartment_id;
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $apartment_id, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        return [
            'total_unread' => (int)AnnouncementItem::find()->where(['apartment_id' => $apartment_id, 'read_at' => null])->count(),
            'total_fee_unpaid' => (int)ServicePaymentFee::find()->where(['apartment_id' => $apartment_id, 'status' => ServicePaymentFee::STATUS_UNPAID])->sum('price')
        ];
    }


    /**
     * @SWG\Post(
     *      path="/announcement-item//is-hidden",
     *      description="AnnouncementItem is-hidden",
     *      operationId="AnnouncementItem is-hidden",
     *      summary="AnnouncementItem is-hidden",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"AnnouncementItem"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/AnnouncementItemIsHiddenForm"),
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
    public function actionIsHidden()
    {
        $model = new AnnouncementItemIsHiddenForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->isHidden();
    }
}
