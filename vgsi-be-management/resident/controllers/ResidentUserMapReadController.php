<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUserMapRead;
use resident\models\ResidentUserMapReadResponse;
use Yii;

class ResidentUserMapReadController extends ApiController
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
     *      path="/resident-user-map-read/list",
     *      operationId="ResidentUserMapRead list",
     *      summary="ResidentUserMapRead list",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUserMapRead"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter( in="query", name="apartment_id", type="integer", default="1", description="apartment id" ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ResidentUserMapReadResponse"),
     *                  ),
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
        $user = Yii::$app->user->getIdentity();
        $apartment_id = Yii::$app->request->get("apartment_id", 0);
        $apartment_id = (int)$apartment_id;
        $apartment = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $apartment_id]);
        $items = ResidentUserMapReadResponse::find()->where(['resident_user_phone' => $user->phone]);
        if (!empty($apartment)) {
            $items = $items->andWhere(['building_cluster_id' => $apartment->building_cluster_id]);
        }
        return [
            'items' => $items->all(),
        ];
    }

    /**
     * @SWG\Post(
     *      path="/resident-user-map-read/is-read",
     *      description="ResidentUserMapRead is-read",
     *      operationId="ResidentUserMapRead is-read",
     *      summary="ResidentUserMapRead is-read",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"ResidentUserMapRead"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="id", description="Id - của ResidentUserMapRead", default=1, type="integer"),
     *          ),
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
        $params = Yii::$app->request->bodyParams;
        if (!$params['id']) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        } else {
            $user = Yii::$app->user->getIdentity();
            $id = (int)$params['id'];
            $item = ResidentUserMapRead::findOne(['id' => $id, 'resident_user_id' => $user->id]);
            if(empty($item)){
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $item->is_read = ResidentUserMapRead::IS_READ;
            if(!$item->save()){
                Yii::error($item->getErrors());
            };
        }
        return [
            'success' => true,
            'message' => Yii::t('resident', "Update success"),
        ];

    }
}
