<?php

namespace frontend\models;

use common\models\Job;
use common\models\JobComment;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="JobCommentResponse")
 * )
 */
class JobCommentResponse extends JobComment
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="content_en", type="string"),
     * @SWG\Property(property="creator", type="object", ref="#/definitions/CreatorJobCommentResponse", description="người tạo"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="type", type="integer", description="0- comment, 1- history"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'content',
            'content_en',
            'creator' => function($model){
                return CreatorJobCommentResponse::findOne(['id' => $model->created_by]);
            },
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
            'type',
            'created_at',
            'updated_at',
        ];
    }
}
