<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\NotifySendConfig;
use frontend\models\NotifySendConfigCreateForm;
use frontend\models\NotifySendConfigResponse;
use frontend\models\NotifySendConfigUpdateForm;
use Yii;

class NotifySendConfigController extends ApiController
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
     *      path="/notify-send-config/update",
     *      description="NotifySendConfig update",
     *      operationId="NotifySendConfig update",
     *      summary="NotifySendConfig update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"NotifySendConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/NotifySendConfigCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="cập nhật cấu hình gửi thông báo",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/NotifySendConfigResponse"),
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
        $model = new NotifySendConfigCreateForm();
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
     *      path="/notify-send-config/update-all",
     *      description="NotifySendConfig update all",
     *      operationId="NotifySendConfig update all",
     *      summary="NotifySendConfig update all",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"NotifySendConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/NotifySendConfigUpdateForm"),
     *      ),
     *      @SWG\Response(response=200, description="cập nhật tất cả cấu hình gửi thông báo",
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
        $model = new NotifySendConfigUpdateForm();
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
     *      path="/notify-send-config/list-detail",
     *      operationId="NotifySendConfig list-detail",
     *      summary="NotifySendConfig",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"NotifySendConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Chi tiết cấu hình gửi thông báo",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/NotifySendConfigResponse"),
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
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $listType = NotifySendConfig::$type_list;
        foreach ($listType as $k => $v){
            $model = NotifySendConfigResponse::findOne(['type' => $k, 'building_cluster_id' => $buildingCluster->id]);
            if(empty($model)){
                $model = new NotifySendConfigResponse();
                $model->building_cluster_id = $buildingCluster->id;
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
        return NotifySendConfigResponse::find()->where(['building_cluster_id' => $buildingCluster->id])->all();
    }
}
