<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ServiceParkingLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceParkingLevelResponse")
 * )
 */
class ServiceParkingLevelResponse extends ServiceParkingLevel
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="description", type="string"),
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
            'code',
            'description',
            'price',
            'service_map_management_id',
            'service_id',
        ];
    }
}
