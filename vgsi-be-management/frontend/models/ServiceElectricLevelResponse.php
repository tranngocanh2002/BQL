<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceElectricLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceElectricLevelResponse")
 * )
 */
class ServiceElectricLevelResponse extends ServiceElectricLevel
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
