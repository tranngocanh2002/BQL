<?php

namespace frontend\models;

use common\models\HistoryResidentMapApartment;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="HistoryResidentMapApartmentResponse")
 * )
 */
class HistoryResidentMapApartmentResponse extends HistoryResidentMapApartment
{
    /**
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="time_in", type="integer"),
     * @SWG\Property(property="time_out", type="integer")
     */
    public function fields()
    {
        return [
            'apartment_id',
            'time_in',
            'time_out'
        ];
    }
}
