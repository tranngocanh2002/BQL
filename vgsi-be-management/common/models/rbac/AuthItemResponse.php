<?php

namespace common\models\rbac;

use common\helpers\ErrorCode;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AuthItemResponse")
 * )
 */
class AuthItemResponse extends AuthItem
{
    /**
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="description_en", type="string"),
     * @SWG\Property(property="tag", type="string"),
     * @SWG\Property(property="note", type="string"),
     * @SWG\Property(property="data_web", type="array",
     *      @SWG\Items(type="string", default="string"),
     *  ),
     */
    public function fields()
    {
        return [
            'name',
            'description',
            'description_en',
            'tag',
            'note',
            'data_web' => function ($model) {
                return (!empty($model->data_web)) ? json_decode($model->data_web) : [];
            }
        ];
    }
}
