<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUserIdentificationHistory;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserIdentificationHistoryResponse")
 * )
 */
class ResidentUserIdentificationHistoryResponse extends ResidentUserIdentificationHistory
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="type", type="integer", description="0 - nhận diện là cư dân, 1- không phải cu dân"),
     * @SWG\Property(property="time_event", type="integer", description="thời điểm nhận diện"),
     * @SWG\Property(property="image_name", type="string", description="tên ảnh"),
     * @SWG\Property(property="image_uri", type="string", description="link ảnh"),
     * @SWG\Property(property="apartments", type="array", description="Danh sách căn hộ",
     *      @SWG\Items(type="object",
     *          @SWG\Property(property="apartment_id", type="integer"),
     *          @SWG\Property(property="apartment_name", type="string"),
     *          @SWG\Property(property="apartment_parent_path", type="string"),
     *      ),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'resident_user_id',
            'resident_user_name' => function($model){
                if(!empty($model->residentUser)){
                    return $model->residentUser->first_name;
                }
                return '';
            },
            'type',
            'time_event',
            'image_name',
            'image_uri',
            'apartments' => function ($model) {
                $data = [];
                $apartmentMapResidents = ApartmentMapResidentUser::find()->where(['resident_user_id' => $model->resident_user_id])->all();
                foreach ($apartmentMapResidents as $apartmentMapResident) {
                    $data[] = [
                        'apartment_id' => $apartmentMapResident->apartment_id,
                        'apartment_name' => $apartmentMapResident->apartment_name,
                        'apartment_parent_path' => trim($apartmentMapResident->apartment_parent_path, '/'),
                    ];
                }
                return $data;
            }
        ];
    }
}
