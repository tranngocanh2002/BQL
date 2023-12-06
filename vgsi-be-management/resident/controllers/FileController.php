<?php

namespace resident\controllers;

use common\helpers\ErrorCode;
use common\models\FileUpload;
use common\models\UploadForm;
use Yii;
use yii\web\UploadedFile;

class FileController extends ApiController
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
     *
     * @SWG\Post(
     *      path="/file/upload",
     *      operationId="upload",
     *      summary="Upload file",
     *      consumes = {"multipart/form-data"},
     *      produces = {"application/json"},
     *      tags={"File"},
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
     *             "domain_origin": {},
     *             "http_bearer_auth": {},
     *         }
     *      },
     * )
     */
    public function actionUpload()
    {
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $model->files = UploadedFile::getInstances($model, 'files');
            $dataRes = $model->upload();
            if ($dataRes) {
                // file is uploaded successfully
                return [
                    'success' => true,
                    'message' => Yii::t('resident', "Upload successfully"),
                    'files' => $dataRes,
                ];
            } else {
                $error = $model->getErrors();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Upload not successful"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $error
                ];
            }
        }
        return [
            'success' => false,
            'message' => Yii::t('resident', "Invalid data"),
            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            'errors' => $model->getErrors()
        ];
    }

    /**
     * @SWG\Post(
     *     path="/file/delete",
     *     operationId="register",
     *     summary="del file",
     *     consumes = {"application/json"},
     *     produces = {"application/json"},
     *     tags={"File"},
     *     @SWG\Parameter(in="header",name="X-Luci-Language",required=false,type="string",default="vi-VN",description="Language, format vi-VN, en-US"),
     *     @SWG\Parameter(in="body",name="body",required=true,
     *         @SWG\Schema(type="object",
     *              @SWG\Property(property="files",type="array",
     *                 @SWG\Items(type="string",default="full path file"),
     *             ),
     *          ),
     *     ),
     *     @SWG\Response(response=200,description="Info",
     *         @SWG\Schema(type="object",
     *             @SWG\Property(property="success",type="boolean",),
     *             @SWG\Property(property="statusCode",type="integer",default=200,),
     *         ),
     *     ),
     *     security={
     *         {
     *             "api_app_key": {},
     *             "domain_origin": {},
     *             "http_bearer_auth": {},
     *         }
     *     },
     *  )
     */
    public function actionDelete()
    {
        $files = Yii::$app->request->post('files');
        if (!$files || !is_array($files)) {
            $error = [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_NOT_FOUND,
                'errors' => Yii::t('resident', 'Data of files incorrect')
            ];
            return $error;
        }
        $base_folder = Yii::$app->params['upload']['folder'];
        foreach ($files as $file) {
            $path_file = explode($base_folder, $file);
            unlink(Yii::$app->basePath . '/web/' . $base_folder . $path_file[1]);
            FileUpload::deleteAll(['path' => '/' . $base_folder . $path_file[1]]);
        }
        return '';
    }
}
