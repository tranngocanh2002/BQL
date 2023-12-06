<?php

namespace frontend\models;

use common\models\Post;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PostResponse")
 * )
 */
class PostResponse extends Post
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string"),
     * @SWG\Property(property="title_en", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="order", type="integer"),
     * @SWG\Property(property="post_category_id", type="integer"),
     * @SWG\Property(property="post_category_name", type="string"),
     * @SWG\Property(property="post_category_name_en", type="string"),
     * @SWG\Property(property="post_category_color", type="string"),
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
            'post_category_id',
            'post_category_name' => function($model){
                if(!empty($model->postCategory)){
                    return $model->postCategory->name;
                }
                return '';
            },
            'post_category_name_en' => function($model){
                if(!empty($model->postCategory)){
                    return $model->postCategory->name_en;
                }
                return '';
            },
            'post_category_color' => function($model){
                if(!empty($model->postCategory)){
                    return $model->postCategory->color;
                }
                return '';
            },
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
        ];
    }
}
