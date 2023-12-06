<?php

namespace frontend\models;

use common\models\RequestAnswerInternal;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestAnswerInternalResponse")
 * )
 */
class RequestAnswerInternalResponse extends RequestAnswerInternal
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="request_id", type="integer"),
     * @SWG\Property(property="management_user_id", type="integer"),
     * @SWG\Property(property="management_user_email", type="string"),
     * @SWG\Property(property="management_user_name", type="string"),
     * @SWG\Property(property="management_user_avatar", type="string"),
     * @SWG\Property(property="management_user_auth_group_name", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'request_id',
            'management_user_id',
            'management_user_email' => function ($model) {
                if (!empty($model->managementUser)) {
                    return $model->managementUser->email;
                }
                return '';
            },
            'management_user_name' => function ($model) {
                if (!empty($model->managementUser)) {
                    return $model->managementUser->first_name;
                }
                return '';
            },
            'management_user_avatar' => function ($model) {
                if (!empty($model->managementUser)) {
                    return $model->managementUser->avatar;
                }
                return '';
            },
            'management_user_auth_group_name' => function ($model) {
                if (!empty($model->managementUser)) {
                    return $model->managementUser->authGroup->name;
                }
                return '';
            },
            'content',
            'created_at',
            'attach' => function ($model) {
                return (!empty($model->attach)) ? json_decode($model->attach) : null;
            },
        ];
    }
}
