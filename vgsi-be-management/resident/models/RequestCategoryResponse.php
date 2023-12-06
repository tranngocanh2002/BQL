<?php

namespace resident\models;

use common\models\rbac\AuthGroup;
use common\models\rbac\AuthGroupResponse;
use common\models\RequestCategory;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestCategoryResponse")
 * )
 */
class RequestCategoryResponse extends RequestCategory
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="color", type="string"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'name_en',
            'color',
        ];
    }
}
