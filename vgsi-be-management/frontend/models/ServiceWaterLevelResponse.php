<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceWaterLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceWaterLevelResponse")
 * )
 */
class ServiceWaterLevelResponse extends ServiceWaterLevel
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="from_level", type="integer"),
     * @SWG\Property(property="to_level", type="integer"),
     * @SWG\Property(property="price", type="integer"),
     * @SWG\Property(property="service_map_management_id", type="integer"),
     * @SWG\Property(property="service_id", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'name_en',
            'description',
            'from_level',
            'to_level',
            'price',
            'service_map_management_id',
            'service_id',
        ];
    }
}
