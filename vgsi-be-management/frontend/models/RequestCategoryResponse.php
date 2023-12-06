<?php

namespace frontend\models;

use common\models\rbac\AuthGroup;
use common\models\rbac\AuthGroupResponse;
use common\models\Request;
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
     * @SWG\Property(property="type", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="color", type="string"),
     * @SWG\Property(property="count_request", type="integer"),
     * @SWG\Property(property="auth_groups", type="array",
     *     @SWG\Items(type="object",
     *          @SWG\Property(property="id", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(property="code", type="string"),
     *     ),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'type',
            'name',
            'name_en',
            'color',
            'count_request' => function($model){
                return (int)Request::find()->where(['request_category_id' => $model->id])->count();
            },
            'auth_group_ids' => function ($model) {
                $res = [];
                if(!empty($model->auth_group_ids)){
                    $auth_group_ids =  json_decode($model->auth_group_ids,true);
                    $res = AuthGroupResponse::find()->where(['in', 'id', $auth_group_ids])->all();
                }
                return $res;
            },
        ];
    }
}
