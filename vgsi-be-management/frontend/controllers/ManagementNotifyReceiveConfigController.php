<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ManagementNotifyReceiveConfig;
use frontend\models\ManagementNotifyReceiveConfigCreateForm;
use frontend\models\ManagementNotifyReceiveConfigResponse;
use frontend\models\ManagementNotifyReceiveConfigUpdateForm;
use Yii;

class ManagementNotifyReceiveConfigController extends ApiController
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
     *      path="/management-notify-receive-config/update",
     *      description="ManagementNotifyReceiveConfig update",
     *      operationId="ManagementNotifyReceiveConfig update",
     *      summary="ManagementNotifyReceiveConfig update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ManagementNotifyReceiveConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ManagementNotifyReceiveConfigCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="cập nhật cấu hình nhận thông báo",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ManagementNotifyReceiveConfigResponse"),
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
        $model = new ManagementNotifyReceiveConfigCreateForm();
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
     *      path="/management-notify-receive-config/update-all",
     *      description="ManagementNotifyReceiveConfig update all",
     *      operationId="ManagementNotifyReceiveConfig update all",
     *      summary="ManagementNotifyReceiveConfig update all",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ManagementNotifyReceiveConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ManagementNotifyReceiveConfigUpdateForm"),
     *      ),
     *      @SWG\Response(response=200, description="cập nhật tất cả cấu hình nhận thông báo",
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
    public function actionUpdateAll()
    {
        $model = new ManagementNotifyReceiveConfigUpdateForm();
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
     * @SWG\Get(
     *      path="/management-notify-receive-config/list-detail",
     *      operationId="ManagementNotifyReceiveConfig list-detail",
     *      summary="ManagementNotifyReceiveConfig",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ManagementNotifyReceiveConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Chi tiết cấu hình nhận thông báo",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ManagementNotifyReceiveConfigResponse"),
     *                  )
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
    public function actionListDetail()
    {
        $user = Yii::$app->user->identity;
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $listChannel = ManagementNotifyReceiveConfig::$channel_list;
        $listType = ManagementNotifyReceiveConfig::$type_list;
        foreach ($listChannel as $c => $n){
            foreach ($listType as $k => $v){
                $model = ManagementNotifyReceiveConfigResponse::findOne(['channel' => $c, 'type' => $k, 'building_cluster_id' => $buildingCluster->id, 'management_user_id' => $user->id]);
                if(empty($model)){
                    $model = new ManagementNotifyReceiveConfigResponse();
                    $model->building_cluster_id = $buildingCluster->id;
                    $model->management_user_id = $user->id;
                    $model->channel = $c;
                    $model->type = $k;
                    if(!$model->save()){
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $model->getErrors()
                        ];
                    }
                }
            }
        }
        return ManagementNotifyReceiveConfigResponse::find()->where(['building_cluster_id' => $buildingCluster->id, 'management_user_id' => $user->id])->all();
    }
}
