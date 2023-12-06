<?php

namespace frontend\controllers;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use Yii;
use yii\data\ActiveDataProvider;
use frontend\controllers\ApiController;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * DeviceController implements the CRUD actions for Device model.
 */
class DeviceController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'control',
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['logout'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['logout'],
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'control' => ['POST'],
        ];
    }

    /**
     * @SWG\Post(
     *     path="/device/control",
     *     operationId="devices_control",
     *     summary="Device Control",
     *     consumes = {"application/json"},
     *     produces = {"application/json"},
     *     tags={"Device"},
     *     @SWG\Parameter(in="header", name="X-Lumi-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *     @SWG\Parameter(in="body", name="body", required=true,
     *         @SWG\Schema(
     *             @SWG\Property(property="topic", type="string", default="LUMI_DEVS_002", description=""),
     *             @SWG\Property(property="payload", type="object", description="data control",
     *                 @SWG\Property(property="cmd", type="string", default="set", description="type action get/set/reset/restart"),
     *                 @SWG\Property(property="devices", type="array", description="",
     *                     @SWG\Items(type="object",
     *                         @SWG\Property(property="devid", type="string", default="string", description="device id"),
     *                         @SWG\Property(property="states", type="array",
     *                              @SWG\Items(type="object",
     *                                  @SWG\Property(property="OnOff", type="object",
     *                                      @SWG\Property(property="on", type="boolean", default=true),
     *                                  ),
     *                                  @SWG\Property(property="StartStop", type="object",
     *                                      @SWG\Property(property="start", type="boolean", default=true),
     *                                  ),
     *                              ),
     *                         ),
     *                     )
     *                 )
     *             )
     *         ),
     *     ),
     *     @SWG\Response(response=200, description="Info",
     *         @SWG\Schema(type="object",
     *             @SWG\Property(property="success", type="boolean", description="type of success true/false"),
     *             @SWG\Property(property="statusCode", type="integer", default=200, description="status code of response"),
     *             @SWG\Property(property="data", type="string", description="data of response. If empty then server recieve and push to Thing successfully", default=""),
     *         ),
     *     ),
     *     security = {
     *         {
     *             "api_app_key": {}
     *         }
     *     },
     * )
     * @return mixed
     */
    public function actionControl()
    {
        $payload = Yii::$app->request->post('payload');
        $topic = Yii::$app->request->post('topic');
        if (!$payload || !$topic) {
            return [
                'success' => false,
                'message' => Yii::t('app', "Invalid params"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        try {
            $data_post = [
                'topic' => $topic . '/control',
                'payload' => serialize($payload)
            ];
            Yii::info(serialize($payload));
            $status = ApiHelper::pushSatatusDeviceToIot($data_post);

            if (!$status['success']) {
                return $status;
            }
            return '';
        } catch (\Exception $exception) {
            Yii::error($exception);
            return [
                'success' => false,
                'message' => Yii::t('app', "System busy"),
                'statusCode' => ErrorCode::ERROR_SYSTEM_ERROR,
            ];
        }
    }
}
