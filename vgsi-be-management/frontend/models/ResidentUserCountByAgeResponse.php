<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\ResidentUserCountByAge;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserCountByAgeResponse")
 * )
 */
class ResidentUserCountByAgeResponse extends ResidentUserCountByAge
{
    /**
     * @SWG\Property(property="total_foreigner", type="integer"),
     * @SWG\Property(property="total_vietnam", type="integer"),
     * @SWG\Property(property="total", type="integer"),
     */
    public function fields()
    {
        return [
            'total_foreigner',
            'total_vietnam',
            'total',
        ];
    }
}
