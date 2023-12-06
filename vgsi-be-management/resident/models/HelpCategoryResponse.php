<?php

namespace resident\models;

use common\models\Help;
use common\models\HelpCategory;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="HelpCategoryResponse")
 * )
 */
class HelpCategoryResponse extends HelpCategory
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="color", type="string"),
     * @SWG\Property(property="count_help", type="integer"),
     * @SWG\Property(property="order", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'name_en',
            'color',
            'order',
            'count_help' => function($model){
                return (int)Help::find()->where(['help_category_id' => $model->id])->count();
            },
        ];
    }
}
