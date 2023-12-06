<?php
/**
 * Created by PhpStorm.
 * User: linhpv
 * Date: 2/11/15
 * Time: 10:57 AM
 */

namespace common\helpers;

use Yii;
use yii\web\UploadedFile;

class FileUtils {
    public static function saveFileUpload(UploadedFile $fileInstance, $subFolder,$alias)
    {
        $baseUrl = Yii::getAlias('@webroot');
        $baseFolder = Yii::$app->params['base_folder_file_upload'];

        if(!file_exists($baseUrl . $baseFolder . $subFolder)) {
            mkdir($baseUrl . $baseFolder . $subFolder, 0777, true);
        }

        if(!file_exists($baseUrl . $baseFolder . $subFolder . $alias.$fileInstance->baseName . '.' . $fileInstance->extension)) {
            return $fileInstance->saveAs($baseUrl . $baseFolder . $subFolder . $alias.$fileInstance->baseName . '.' . $fileInstance->extension);
        }

        return false;
    }
    
    /**
     * 
     * @param type $model
     * @return boolean
     */
    public static function deleteFile($model)
    {
        $baseUrl = Yii::getAlias('@webroot');
        if(file_exists($baseUrl . $model->path . $model->filename)) {           
            return unlink($baseUrl . $model->path . $model->filename);
        }

        return false;
    }
}