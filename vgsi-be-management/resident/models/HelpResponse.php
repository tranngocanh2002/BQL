<?php

namespace resident\models;

use common\models\Help;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="HelpResponse")
 * )
 */
class HelpResponse extends Help
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string"),
     * @SWG\Property(property="title_en", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="help_category_id", type="integer"),
     * @SWG\Property(property="order", type="integer"),
     * @SWG\Property(property="help_category_name", type="string"),
     * @SWG\Property(property="help_category_name_en", type="string"),
     * @SWG\Property(property="help_category_color", type="string"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'title_en',
            'content',
            'order',
            'help_category_id',
            'help_category_name' => function($model){
                if(!empty($model->helpCategory)){
                    return $model->helpCategory->name;
                }
                return '';
            },
            'help_category_name_en' => function($model){
                if(!empty($model->helpCategory)){
                    return $model->helpCategory->name_en;
                }
                return '';
            },
            'help_category_color' => function($model){
                if(!empty($model->helpCategory)){
                    return $model->helpCategory->color;
                }
                return '';
            },
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
        ];
    }
}
