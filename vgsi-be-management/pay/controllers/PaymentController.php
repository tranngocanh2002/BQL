<?php

namespace pay\controllers;

use api\models\PaymentGenCodeResponse;
use api\models\PaymentGenCodeSearch;
use common\helpers\ErrorCode;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\ServicePaymentFee;
use pay\models\PaymentPayForm;
use pay\models\PaymentSuccessForm;
use resident\models\PostSearch;
use Yii;

class PaymentController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
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
     * @SWG\Post(
     *      path="/payment/create",
     *      operationId="Payment create",
     *      summary="Payment create",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Payment"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/PaymentPayForm"),
     *      ),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data",  type="object",
     *                  @SWG\Property(property="url_redirect", type="string"),
     *                  @SWG\Property(property="return_url", type="string"),
     *                  @SWG\Property(property="cancel_url", type="string"),
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
    public function actionCreate()
    {
        $model = new PaymentPayForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => Yii::t('pay', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->pay();
    }

    public function actionListByCode($code)
    {
        $this->layout = 'print';
        $paymentGenCode = PaymentGenCode::findOne(['status' => PaymentGenCode::STATUS_UNPAID, 'code' => $code]);
        if(empty($paymentGenCode)){
            return $this->render('print', [
                    'paymentGenCode' => null,
                ]
            );
        }

        if($paymentGenCode->is_auto == PaymentGenCode::IS_AUTO){
            PaymentGenCodeItem::deleteAll(['payment_gen_code_id' => $paymentGenCode->id, 'status' => PaymentGenCodeItem::STATUS_UNPAID]);
            $servicePaymentFees = ServicePaymentFee::find()->where(['building_cluster_id' => $paymentGenCode->building_cluster_id, 'apartment_id' => $paymentGenCode->apartment_id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])
                ->andWhere(['>', 'more_money_collecte', 0])->all();
            foreach ($servicePaymentFees as $servicePaymentFee){
                $check = PaymentGenCodeItem::findOne(['service_payment_fee_id' => $servicePaymentFee->id]);
                if(!empty($check)){
                    continue;
                }
                $paymentGenCodeItem = new PaymentGenCodeItem();
                $paymentGenCodeItem->building_cluster_id = $paymentGenCode->building_cluster_id;
                $paymentGenCodeItem->payment_gen_code_id = $paymentGenCode->id;
                $paymentGenCodeItem->service_payment_fee_id = $servicePaymentFee->id;
                $paymentGenCodeItem->status = PaymentGenCodeItem::STATUS_UNPAID;
                $paymentGenCodeItem->amount = $servicePaymentFee->more_money_collecte;
                if(!$paymentGenCodeItem->save()){
                    return $this->render('print', [
                            'paymentGenCode' => null,
                        ]
                    );
                }
            }
        }
        return $this->render('print', [
                'paymentGenCode' => $paymentGenCode,
                'paymentGenCodeItems' => PaymentGenCodeItem::find()->where(['payment_gen_code_id' => $paymentGenCode->id])->all(),
            ]
        );
    }

    public function actionSuccess()
    {
        $this->layout = 'print';
        Yii::info(Yii::$app->request->queryParams);
        $model = new PaymentSuccessForm();
        $model->load(Yii::$app->request->queryParams, '');
        $model->is_update = false;
        if (!$model->validate()) {
            Yii::error($model->errors);
            return [
                'success' => false,
                'message' => Yii::t('pay', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $this->render('success', ['dataRes' => $model->success()]);
    }

    public function actionNotify()
    {
        Yii::info(Yii::$app->request->queryParams);
        $model = new PaymentSuccessForm();
        $model->load(Yii::$app->request->queryParams, '');
        $model->is_update = true;
        if (!$model->validate()) {
            Yii::error($model->errors);
            return [
                'success' => false,
                'message' => Yii::t('pay', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ];
        }
        return $model->success();
    }

    public function actionCancel()
    {
        Yii::info('Cancel');
        return true;
    }
}
