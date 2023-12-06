<?php

namespace backendQltt\controllers;


use common\helpers\ErrorCode;
use common\models\UploadForm;
use Yii;
use yii\web\UploadedFile;

class UploadController extends BaseController
{

    public function actionTmp()
    {
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $model->files = UploadedFile::getInstances($model, 'files');
            $dataRes = $model->upload();
            if ($dataRes) {
                echo json_encode(['full_path' => trim($dataRes[0], '/'), 'file_name' => $dataRes[0]]);die;
            }
        }
        echo json_encode(['error' => 'File not found!']);die;
    }
}
