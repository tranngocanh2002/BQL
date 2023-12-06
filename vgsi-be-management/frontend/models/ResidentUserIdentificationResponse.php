<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ResidentUserIdentification;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserIdentificationResponse")
 * )
 */
class ResidentUserIdentificationResponse extends ResidentUserIdentification
{
    /**
     * @SWG\Property(property="resident_user_id", type="integer", description="Id định danh cư dân"),
     * @SWG\Property(property="resident_user_name", type="string", description="Tên cư dân"),
     * @SWG\Property(property="resident_user_gender", type="integer", description="giới tính: 0 chưa xác định, 1 - nam, 2 - nữ"),
     * @SWG\Property(property="status", type="integer", description="0 - mới khởi tao, 1 - được giám sát, 2 - lỗi chưa giám sát được"),
     * @SWG\Property(property="images", type="array",
     *     @SWG\Items(type="string"),
     * ),
     */
    public function fields()
    {
        return [
            'resident_user_id' => function($model){
                if(!empty($model->residentUser)){
                    return $model->residentUser->id;
                }
                return 0;
            },
            'resident_user_name' => function($model){
                if(!empty($model->residentUser)){
                    return $model->residentUser->first_name . ' ' .$model->residentUser->last_name;
                }
                return '';
            },
            'resident_user_gender' => function($model){
                if(!empty($model->residentUser) && !empty($model->residentUser->gender)){
                    return $model->residentUser->gender;
                }
                return 0;
            },
            'status',
            'images' => function ($model) {
                $link_api = Yii::$app->params['link_api'];
//                return ["https://api.staging.building.luci.vn/uploads/images/201911/1573812243-2560edf7-1efe-4a4a-b2d4-b747e64768fe.jpg"];
                if(!empty($model->medias)){
                    $medias = json_decode($model->medias, true);
                    if(!empty($medias['images'])){
                        $res = [];
                        foreach ($medias['images'] as $image){
                            $res[] = $link_api['web'] . $image;
                        }
                        return $res;
                    }
                }
                return null;
            },
        ];
    }
}
