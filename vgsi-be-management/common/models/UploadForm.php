<?php

namespace common\models;

use common\helpers\CUtils;
use yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="UploadForm")
 * )
 */
class UploadForm extends Model
{

    /**
     * @SWG\Property(description="Files Upload")
     * @var files
     */
    public $files;

    public $base_folder;

    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'maxFiles' => Yii::$app->params['upload']['maxFiles'], 'maxSize' => Yii::$app->params['upload']['maxSize']],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $arrFile = [];
            $arrFolderFile = [
                'text' => 'texts',
                'image' => 'images',
                'audio' => 'audios',
                'video' => 'videos',
                'application' => 'applications',
            ];
            if(empty($this->base_folder)){
                $this->base_folder = Yii::$app->params['upload']['folder'];
            }
            $timeCurent = time();
            foreach ($this->files as $file) {
                $type_file = explode('/', $file->type);
                Yii::info($file->type);
                $folder = isset($arrFolderFile[$type_file['0']]) ? $arrFolderFile[$type_file['0']] : 'other';
                $base_folder_save = $this->base_folder . $folder . '/' . date('Ym');
                FileHelper::createDirectory($base_folder_save);
                $file_name = $timeCurent . '-' . $file->baseName;
                $file_name = CUtils::slugify($file_name);
                if(empty($file->extension)){
                    $file->extension = 'jpg';
                }
                $file_path = $base_folder_save . '/' . $file_name . '.' . $file->extension;
                $file->saveAs($file_path);
                $this->convertOrientation($file_path);
                $arrFile[] = '/' . $file_path;

                $fileUpload = new FileUpload();
                $fileUpload->name = $file->baseName;
                $fileUpload->type = $file->type;
                $fileUpload->size = $file->size;
                $fileUpload->path = '/' . $file_path;
                $fileUpload->save();
            }
            return $arrFile;
        } else {
            return false;
        }
    }

    public function uploadIdentified()
    {
        if ($this->validate()) {
            $arrFile = [];
            if(empty($this->base_folder)){
                $this->base_folder = Yii::$app->params['upload']['folder'];
            }
            foreach ($this->files as $file) {
                $base_folder_save = $this->base_folder;
                FileHelper::createDirectory($base_folder_save);
                $file_name = $file->baseName;
                $file_path = $base_folder_save . $file_name . '.jpg';
                $file->saveAs($file_path);
                $this->convertOrientation($file_path);
                $arrFile[] = '/' . $file_path;

                $fileUpload = new FileUpload();
                $fileUpload->name = $file->baseName;
                $fileUpload->type = $file->type;
                $fileUpload->size = $file->size;
                $fileUpload->path = '/' . $file_path;
                $fileUpload->save();
            }
            return $arrFile;
        } else {
            return false;
        }
    }

    private function convertOrientation($file_path)
    {
        $fileandpath = \Yii::getAlias('@webroot') . '/' . $file_path;
        if (!file_exists($fileandpath)) {
            return false;
        }
        $exif = @exif_read_data($fileandpath);
        if (!$exif || !is_array($exif)) {
            return false;
        }
        $exif = array_change_key_case($exif, CASE_LOWER);
        if (!array_key_exists('orientation', $exif)) {
            return false;
        }
        $img_res = $this->get_image_resource($fileandpath);
        if (is_null($img_res)) {
            return false;
        }

        switch ($exif['orientation']) {
            case 1:
                return true;
                break;
            case 2:
                $final_img = $this->imageflip($img_res, IMG_FLIP_HORIZONTAL);
                break;
            case 3:
                $final_img = $this->imageflip($img_res, IMG_FLIP_VERTICAL);
                break;
            case 4:
                $final_img = $this->imageflip($img_res, IMG_FLIP_BOTH);
                break;
            case 5:
                $final_img = imagerotate($img_res, -90, 0);
                $final_img = $this->imageflip($img_res, IMG_FLIP_HORIZONTAL);
                break;
            case 6:
                $final_img = imagerotate($img_res, -90, 0);
                break;
            case 7:
                $final_img = imagerotate($img_res, 90, 0);
                $final_img = $this->imageflip($img_res, IMG_FLIP_HORIZONTAL);
                break;
            case 8:
                $final_img = imagerotate($img_res, 90, 0);
                break;
        }
        if (!isset($final_img)) {
            return;
        }
        return $this->save_image_resource($final_img, $fileandpath);
    }

    private function get_image_resource($file)
    {
        $img = null;
        $p = explode(".", strtolower($file));
        $ext = array_pop($p);
        switch ($ext) {
            case "png":
                $img = @imagecreatefrompng($file);
                break;
            case "jpg":
            case "jpeg":
                $img = @imagecreatefromjpeg($file);
                break;
            case "gif":
                $img = @imagecreatefromgif($file);
                break;
        }

        return $img;
    }

    private function save_image_resource($resource, $location)
    {
        $success = false;
        $p = explode(".", strtolower($location));
        $ext = array_pop($p);
        switch ($ext) {
            case "png":
                $success = imagepng($resource, $location);
                break;
            case "jpg":
            case "jpeg":
                $success = imagejpeg($resource, $location);
                break;
            case "gif":
                $success = imagegif($resource, $location);
                break;
        }

        return $success;
    }

    private function imageflip($resource, $mode)
    {
        if ($mode == IMG_FLIP_VERTICAL || $mode == IMG_FLIP_BOTH) {
            $resource = imagerotate($resource, 180, 0);
        }
        if ($mode == IMG_FLIP_HORIZONTAL || $mode == IMG_FLIP_BOTH) {
            $resource = imagerotate($resource, 90, 0);
        }

        return $resource;
    }

}
