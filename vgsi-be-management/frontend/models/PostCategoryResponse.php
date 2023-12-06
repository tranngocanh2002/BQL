<?php

namespace frontend\models;

use common\models\Post;
use common\models\PostCategory;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PostCategoryResponse")
 * )
 */
class PostCategoryResponse extends PostCategory
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="order", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="color", type="string"),
     * @SWG\Property(property="count_post", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'order',
            'name',
            'name_en',
            'color',
            'count_post' => function($model){
                return (int)Post::find()->where(['post_category_id' => $model->id])->count();
            },
        ];
    }
}
