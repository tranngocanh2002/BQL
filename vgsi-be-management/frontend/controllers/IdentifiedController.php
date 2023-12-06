<?php
namespace frontend\controllers;

use common\helpers\ErrorCode;
use common\models\ResidentUserIdentification;
use common\models\UploadForm;
use frontend\models\IdentifiedEventForm;
use frontend\models\IdentifiedStatusForm;
use frontend\models\ResidentUserIdentificationSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class IdentifiedController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
//            'upload',
//            'event',
//            'status',
//            'information-resident'
        ];
//        $behaviors['access'] = [
//            'class' => AccessControl::className(),
//            'only' => ['logout'],
//            'rules' => [
//                [
//                    'allow' => true,
//                    'actions' => ['logout'],
//                    'roles' => ['@'],
//                ],
//            ],
//        ];
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
     *
     * @SWG\Post(
     *      path="/identified/upload",
     *      operationId="Identified upload",
     *      summary="Identified file",
     *      consumes = {"multipart/form-data"},
     *      produces = {"application/json"},
     *      tags={"Identified"},
     *      @SWG\Parameter(in="header",name="X-Luci-Language",required=false,type="string",default="vi-VN",description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="formData",name="UploadForm[files][]",type="file",required=true,),
     *      @SWG\Response(response=200,description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success",type="boolean",),
     *              @SWG\Property(property="statusCode",type="integer",default=200),
     *              @SWG\Property(property="data",type="object",
     *                   @SWG\Property(property="files",type="object",default={}),
     *              )
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *         }
     *      },
     * )
     */
    public function actionUpload()
    {
        die;
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $model->base_folder = 'uploads/identified/';
            $model->files = UploadedFile::getInstances($model, 'files');
            $dataRes = $model->uploadIdentified();
            if ($dataRes) {
                // file is uploaded successfully
                return [
                    'success' => true,
                    'message' => Yii::t('frontend', "Upload successfully"),
                    'files' => $dataRes,
                ];
            } else {
                $error = $model->getErrors();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Upload not successful"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $error
                ];
            }
        }
        return [
            'success' => false,
            'message' => Yii::t('frontend', "Invalid data"),
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            'errors' => $model->getErrors()
        ];
    }

    /**
     * @SWG\Post(
     *      path="/identified/event",
     *      operationId="Event",
     *      summary="Identified Event",
     *      description="Api user Identified",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Identified"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/IdentifiedEventForm"),
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
     *         }
     *      },
     * )
     */
    public function actionEvent()
    {
        $model = new IdentifiedEventForm();
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->event();
    }

    /**
     * @SWG\Get(
     *      path="/identified/information-resident",
     *      operationId="identified information-resident",
     *      summary="identified information-resident",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Identified"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Response(response=200, description="Info",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="success", type="boolean"),
     *              @SWG\Property(property="statusCode", type="integer", default=200),
     *              @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="items", type="array",
     *                      @SWG\Items(type="object", ref="#/definitions/ResidentUserIdentificationResponse"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      security = {
     *         {
     *             "api_app_key": {},
     *         }
     *      },
     * )
     */
    public function actionInformationResident()
    {
        $model = new ResidentUserIdentificationSearch();
        $dataProvider = $model->search(Yii::$app->request->queryParams);
        ResidentUserIdentification::updateAll(['is_sync' => ResidentUserIdentification::IS_SYNC]);
        return [
            'items' => $dataProvider->getModels(),
//            'pagination' => [
//                "totalCount" => $dataProvider->getTotalCount(),
//                "pageCount" => $dataProvider->pagination->pageCount,
//                "currentPage" => $dataProvider->pagination->page + 1,
//                "pageSize" => $dataProvider->pagination->pageSize,
//            ]
        ];
    }

    /**
     * @SWG\Post(
     *      path="/identified/status",
     *      operationId="Status",
     *      summary="Identified status",
     *      description="Api user Identified",
     *      consumes = {"application/json"},
     *      produces = {"application/json"},
     *      tags={"Identified"},
     *      @SWG\Parameter(in="header", name="X-Luci-Language", required=false, type="string", default="vi-VN", description="Language, format vi-VN, en-US"),
     *      @SWG\Parameter(in="body", name="body", required=true,
     *          @SWG\Schema(ref="#/definitions/IdentifiedStatusForm"),
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
     *         }
     *      },
     * )
     */
    public function actionStatus()
    {
        $model = new IdentifiedStatusForm();
        $model->load(Yii::$app->request->bodyParams, '');
        return $model->event();
    }
}
