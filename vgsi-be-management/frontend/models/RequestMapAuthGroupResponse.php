<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\RequestMapAuthGroup;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestMapAuthGroupResponse")
 * )
 */
class RequestMapAuthGroupResponse extends RequestMapAuthGroup
{
    /**
     * @SWG\Property(property="request_id", type="integer"),
     * @SWG\Property(property="auth_group_id", type="integer"),
     * @SWG\Property(property="auth_group_name", type="string"),
     * @SWG\Property(property="auth_group_name_en", type="string"),
     */
    public function fields()
    {
        return [
            'request_id',
            'auth_group_id',
            'auth_group_name' => function ($model) {
                if ($model->authGroup) {
                    return $model->authGroup->name;
                }
                return '';
            },
            'auth_group_name_en' => function ($model) {
                if ($model->authGroup) {
                    return $model->authGroup->name_en;
                }
                return '';
            },
        ];
    }
}
