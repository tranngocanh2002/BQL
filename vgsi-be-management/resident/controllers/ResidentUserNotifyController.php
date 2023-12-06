<?php
namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\ResidentUserNotify;
use resident\models\ResidentUserNotifyIsHiddenForm;
use resident\models\ResidentUserNotifyIsReadForm;
use resident\models\ResidentUserNotifyResponse;
use resident\models\ResidentUserNotifySearch;
use Yii;

class ResidentUserNotifyController extends ApiController
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
     *      path="/resident-user-notify/list",
     *      operationId="ResidentUserNotify",
     *      summary="ResidentUserNotify",
     *      description="Api List ResidentUserNotify",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default="", description="apartment id"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ResidentUserNotifyResponse"),
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
        $modelSearch = new ResidentUserNotifySearch();
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
     *      path="/resident-user-notify/detail?id={id}",
     *      operationId="ResidentUserNotify detail",
     *      summary="ResidentUserNotify",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ResidentUserNotify" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ResidentUserNotifyResponse"),
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
    public function actionDetail()
    {
        $user = Yii::$app->user->getIdentity();
        $id = Yii::$app->request->get("id", 0);
        $notify = ResidentUserNotifyResponse::findOne(['id' => (int)$id, 'management_user_id' => $user->id]);
        if(!empty($notify) && $notify->is_read == ResidentUserNotify::IS_UNREAD){
            $notify->is_read = ResidentUserNotify::IS_READ;
            if(!$notify->save()){
                Yii::error($notify->getErrors());
            };
        }
        return $notify;
    }

    /**
     * @SWG\Post(
     *      path="/resident-user-notify/is-read",
     *      description="ResidentUserNotify is-read",
     *      operationId="ResidentUserNotify is-read",
     *      summary="ResidentUserNotify is-read",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentUserNotifyIsReadForm"),
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
    public function actionIsRead()
    {
        $model = new ResidentUserNotifyIsReadForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->isRead();
    }

    /**
     * @SWG\Post(
     *      path="/resident-user-notify/is-hidden",
     *      description="ResidentUserNotify is-hidden",
     *      operationId="ResidentUserNotify is-hidden",
     *      summary="ResidentUserNotify is-hidden",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentUserNotifyIsHiddenForm"),
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
        $model = new ResidentUserNotifyIsHiddenForm();
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

    /**
     * @SWG\Get(
     *      path="/resident-user-notify/count-unread",
     *      operationId="ResidentUserNotify count-unread",
     *      summary="ResidentUserNotify",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="id apartment" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="total_unread", type="integer", default=0),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *              "domain_origin": {},
     *              "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionCountUnread($apartment_id = null)
    {
        $user = Yii::$app->user->getIdentity();
        $dataQuery = [
            'resident_user_id' => $user->id,
            'is_read' => ResidentUserNotify::IS_UNREAD
        ];
        if(!empty($apartment_id) && $apartment_id != "-9999"){
            $dataQuery['apartment_id'] = [(int)$apartment_id,-1];
        }
        $total_unread = (int)ResidentUserNotify::find()
            ->where($dataQuery)
            ->count();
        return [
            'total_unread' => $total_unread,
        ];
    }
}
