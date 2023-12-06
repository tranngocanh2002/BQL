<?php

namespace resident\models;

use common\models\PuriTrakHistory;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PuriTrakHistoryResponse")
 * )
 */
class PuriTrakHistoryResponse extends PuriTrakHistory
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="puri_trak_id", type="integer"),
     * @SWG\Property(property="aqi", type="float"),
     * @SWG\Property(property="h", type="float"),
     * @SWG\Property(property="t", type="float"),
     * @SWG\Property(property="time", type="integer"),
     * @SWG\Property(property="hours", type="integer"),
     * @SWG\Property(property="device_id", type="string"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="lat", type="float"),
     * @SWG\Property(property="long", type="float"),
     */
    public function fields()
    {
        return [
            'id',
            'puri_trak_id',
            'aqi',
            'h',
            't',
            'time',
            'device_id',
            'name',
            'lat',
            'long',
            'hours',
        ];
    }
}
