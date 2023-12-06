<?php
namespace common\models\rbac;

use common\models\Post;
use Yii;
use yii\rbac\Rule;

class AuthorRule extends Rule
{
    public $name = 'isAuthor';

    public function execute($user, $item, $params)
    {
        $post_id = Yii::$app->request->get('id');
        Yii::info("======================");
        Yii::info($user);
        Yii::info($item);
        Yii::info($post_id);
        Yii::info("======================");
        $post = Post::findOne(['id' => (int)$post_id]);
        if($post){
            if($post->created_by == $user){
                return true;
            }
        }
        return false;
    }
}