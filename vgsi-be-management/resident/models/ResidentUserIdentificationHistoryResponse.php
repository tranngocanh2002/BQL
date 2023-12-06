<?php

namespace resident\models;

use common\helpers\ErrorCode;
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
     */
    public function fields()
    {
        return [
            'id',
            'resident_user_id',
            'resident_user_name',
            'type',
            'time_event',
            'image_name',
            'image_uri',
        ];
    }
}
