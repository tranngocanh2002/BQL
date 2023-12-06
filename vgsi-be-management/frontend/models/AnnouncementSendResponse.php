<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\Apartment;
use common\models\ServiceDebt;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementSendResponse")
 * )
 */
class AnnouncementSendResponse extends Apartment
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_area_id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="resident_user_name", type="string", description="chủ hộ"),
     * @SWG\Property(property="email", type="string", description="email"),
     * @SWG\Property(property="phone", type="string", description="phone"),
     * @SWG\Property(property="app", type="integer", description=""),
     * @SWG\Property(property="created_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'building_area_id',
            'apartment_id' => function($model){
                return $model->id;
            },
            'apartment_name' => function($model){
                return $model->name;
            },
            'apartment_parent_path' => function ($model) {
                return trim($model->parent_path, '/');
            },
            'resident_user_name',
            'email' => function($model){
                if(!empty($model->residentUser)){
                    return $model->residentUser->email;
                }
                return '';
            },
            'phone' => function($model){
                if(!empty($model->residentUser)){
                    return $model->residentUser->phone;
                }
                return '';
            },
            'app' => function($model){
                if($model->residentUser){
                    return $model->residentUser->active_app;
                }
                return 0;
            },
            'created_at',
        ];
    }
}
