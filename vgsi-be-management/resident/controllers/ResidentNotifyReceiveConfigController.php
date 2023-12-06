<?php

namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentNotifyReceiveConfig;
use resident\models\ResidentNotifyReceiveConfigCreateForm;
use resident\models\ResidentNotifyReceiveConfigResponse;
use resident\models\ResidentNotifyReceiveConfigUpdateForm;
use Yii;

class ResidentNotifyReceiveConfigController extends ApiController
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
     *      path="/resident-notify-receive-config/update",
     *      description="ResidentNotifyReceiveConfig update",
     *      operationId="ResidentNotifyReceiveConfig update",
     *      summary="ResidentNotifyReceiveConfig update",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentNotifyReceiveConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentNotifyReceiveConfigCreateForm"),
     *      ),
     *      @SWG\Response(response=200, description="cập nhật cấu hình nhận thông báo",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object", ref="#/definitions/ResidentNotifyReceiveConfigResponse"),
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
    public function actionUpdate()
    {
        $model = new ResidentNotifyReceiveConfigCreateForm();
        $model->setScenario('update');
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->update();
    }

    /**
     * @SWG\Post(
     *      path="/resident-notify-receive-config/update-all",
     *      description="ResidentNotifyReceiveConfig update all",
     *      operationId="ResidentNotifyReceiveConfig update all",
     *      summary="ResidentNotifyReceiveConfig update all",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentNotifyReceiveConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/ResidentNotifyReceiveConfigUpdateForm"),
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
     *             "http_bearer_auth": {}
     *         }
     *      },
     * )
     */
    public function actionUpdateAll()
    {
        $model = new ResidentNotifyReceiveConfigUpdateForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->update();
    }

    /**
     * @SWG\Get(
     *      path="/resident-notify-receive-config/list-detail",
     *      operationId="ResidentNotifyReceiveConfig list-detail",
     *      summary="ResidentNotifyReceiveConfig",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentNotifyReceiveConfig"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="query", name="apartment_id", type="integer", default="", description="id căn hộ"),
     *      @SWG\Response(response=200, description="Chi tiết cấu hình nhận thông báo",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ResidentNotifyReceiveConfigResponse"),
     *                  )
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
    public function actionListDetail($apartment_id = null)
    {
        $user = Yii::$app->user->identity;
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment_id, 'resident_user_phone' => $user->phone]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
        $listChannel = ResidentNotifyReceiveConfig::$channel_list;
        $listType = ResidentNotifyReceiveConfig::$type_list;
        foreach ($listChannel as $c => $n){
            foreach ($listType as $k => $v){
                $model = ResidentNotifyReceiveConfigResponse::findOne(['channel' => $c, 'type' => $k, 'building_cluster_id' => $building_cluster_id, 'resident_user_id' => $user->id]);
                if(empty($model)){
                    $model = new ResidentNotifyReceiveConfigResponse();
                    $model->building_cluster_id = $building_cluster_id;
                    $model->resident_user_id = $user->id;
                    $model->channel = $c;
                    $model->type = $k;
                    if(!$model->save()){
                        return [
                            'success' => false,
                            'message' => Yii::t('resident', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $model->getErrors()
                        ];
                    }
                }
            }
        }
        return ResidentNotifyReceiveConfigResponse::find()->where(['building_cluster_id' => $building_cluster_id, 'resident_user_id' => $user->id])->all();
    }
}
