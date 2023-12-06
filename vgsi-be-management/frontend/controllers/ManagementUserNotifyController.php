<?php
namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ManagementUserNotify;
use frontend\models\ManagementUserNotifyIsHiddenForm;
use frontend\models\ManagementUserNotifyIsReadForm;
use frontend\models\ManagementUserNotifyResponse;
use frontend\models\ManagementUserNotifySearch;
use Yii;

class ManagementUserNotifyController extends ApiController
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
     *      path="/management-user-notify/list",
     *      operationId="ManagementUserNotify",
     *      summary="ManagementUserNotify",
     *      description="Api List ManagementUserNotify",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ManagementUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="pageSize", type="integer", default=50, description="Per page/page"),
     *      @SWG\Parameter(in="query", name="page", type="integer", default=1, description="Current Page"),
     *      @SWG\Parameter(in="query", name="sort", type="string", default="", description="Sort by:<br/> <b>+-name</b>: Tên building <br/><b>+-code</b>: Mã building <br/><b>+-status</b>: Trạng thái"),
     *      @SWG\Response(response=200, description="",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ManagementUserNotifyResponse"),
     *                  ),
     *                  @SWG\Property(property="total_unread", type="integer", default=0),
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
        $user = Yii::$app->user->getIdentity();
        $modelSearch = new ManagementUserNotifySearch();
        $dataProvider = $modelSearch->search(Yii::$app->request->queryParams, $this->is_web);
        if($this->is_web !== true){
            $total_unread = (int)ManagementUserNotify::find()
                ->where(['management_user_id' => $user->id, 'is_read' => ManagementUserNotify::IS_UNREAD])
                ->andWhere(['or', ['type' => ManagementUserNotify::TYPE_JOB], ['type' => ManagementUserNotify::TYPE_FORM]])
                ->andWhere(['not', ['request_id' => null]])
                ->count();
        }else{
            $total_unread = (int)ManagementUserNotify::find()
                ->where(['management_user_id' => $user->id, 'is_read' => ManagementUserNotify::IS_UNREAD])
                ->andWhere(['<>', 'type', [ManagementUserNotify::TYPE_JOB,ManagementUserNotify::TYPE_FORM]])
                ->count();
        }

        return [
            'items' => $dataProvider->getModels(),
            'total_unread' => $total_unread,
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
     *      path="/management-user-notify/detail?id={id}",
     *      operationId="ManagementUserNotify detail",
     *      summary="ManagementUserNotify",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ManagementUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="id", type="integer", default="1", description="id ManagementUserNotify" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ManagementUserNotifyResponse"),
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
        $user = Yii::$app->user->getIdentity();
        $id = Yii::$app->request->get("id", 0);
        if(is_numeric($id)){ $id = (int)$id;}else{ $id = 0;}
        $notify = ManagementUserNotifyResponse::findOne(['id' => $id, 'management_user_id' => $user->id]);
        if(!empty($notify) && $notify->is_read == ManagementUserNotify::IS_UNREAD){
            $notify->is_read = ManagementUserNotify::IS_READ;
            if(!$notify->save()){
                Yii::error($notify->getErrors());
            };
        }
        return $notify;
    }

    /**
     * @SWG\Post(
     *      path="/management-user-notify/is-read",
     *      description="ManagementUserNotify is-read",
     *      operationId="ManagementUserNotify is-read",
     *      summary="ManagementUserNotify is-read",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ManagementUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ManagementUserNotifyIsReadForm"),
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
    public function actionIsRead()
    {
        $model = new ManagementUserNotifyIsReadForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->isRead();
    }

    /**
     * @SWG\Post(
     *      path="/management-user-notify/is-hidden",
     *      description="ManagementUserNotify is-hidden",
     *      operationId="ManagementUserNotify is-hidden",
     *      summary="ManagementUserNotify is-hidden",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ManagementUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ManagementUserNotifyIsHiddenForm"),
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
    public function actionIsHidden()
    {
        $model = new ManagementUserNotifyIsHiddenForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->isHidden();
    }

    /**
     * @SWG\Get(
     *      path="/management-user-notify/count-unread",
     *      operationId="ManagementUserNotify count-unread",
     *      summary="ManagementUserNotify",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ManagementUserNotify"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
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
    public function actionCountUnread()
    {
        $user = Yii::$app->user->getIdentity();
        if($this->is_web !== true){
            $total_unread = (int)ManagementUserNotify::find()
                ->where(['management_user_id' => $user->id, 'is_read' => ManagementUserNotify::IS_UNREAD])
                ->andWhere(['or', ['type' => ManagementUserNotify::TYPE_JOB], ['type' => ManagementUserNotify::TYPE_FORM]])
                ->andWhere(['not', ['request_id' => null]])
                ->count();
        }else{
            $total_unread = (int)ManagementUserNotify::find()
                ->where(['management_user_id' => $user->id, 'is_read' => ManagementUserNotify::IS_UNREAD])
                ->andWhere(['<>', 'type', ManagementUserNotify::TYPE_JOB])
                ->count();
        }
        return [
            'total_unread' => $total_unread
        ];
    }
}
