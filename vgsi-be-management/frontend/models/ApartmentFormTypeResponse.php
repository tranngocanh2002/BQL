<?php

namespace frontend\models;

use common\models\Post;
use common\models\ApartmentFormType;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentFormTypeResponse")
 * )
 */
class ApartmentFormTypeResponse extends ApartmentFormType
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     */
    public function fields()
    {
        return [
            'id',
            'building_cluster_id',
            'name',
        ];
    }
}
