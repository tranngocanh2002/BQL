<?php

namespace frontend\models;

use common\models\ServiceUtilityRatting;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityRatingResponse")
 * )
 */
class ServiceUtilityRatingResponse extends ServiceUtilityRatting
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="star", type="number"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="service_utility_free_id", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'star',
            'resident_user_id',
            'service_utility_free_id',
            'created_at',
        ];
    }
}
