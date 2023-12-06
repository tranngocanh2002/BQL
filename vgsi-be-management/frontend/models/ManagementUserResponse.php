<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\rbac\AuthGroupResponse;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserResponse")
 * )
 */
class ManagementUserResponse extends ManagementUser
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="email", type="string"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="first_name", type="string"),
     * @SWG\Property(property="last_name", type="string"),
     * @SWG\Property(property="avatar", type="string"),
     * @SWG\Property(property="code_management_user", type="string"),
     * @SWG\Property(property="gender", type="integer"),
     * @SWG\Property(property="birthday", type="integer"),
     * @SWG\Property(property="parent_id", type="integer"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="auth_group", type="array",
     *      @SWG\Items(type="object", ref="#/definitions/AuthGroupResponse"),
     * ),
     * @SWG\Property(property="status_verify_phone", type="integer"),
     * @SWG\Property(property="status_verify_email", type="integer"),
     * @SWG\Property(property="is_send_email", type="integer"),
     * @SWG\Property(property="is_send_notify", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'email',
            'phone',
            'first_name',
            'last_name',
            'avatar',
            'gender',
            'birthday',
            'parent_id',
            'status',
            'status_verify_phone',
            'status_verify_email',
            'code_management_user',
            'auth_group' => function ($model) {
                return AuthGroupResponse::findOne(['id' => $model->auth_group_id]);
//                $authGroup = $model->authGroup;
//                if($authGroup){
//                    return [
//                        'id' => $authGroup->id,
//                        'name' => $authGroup->name,
//                    ];
//                }else{
//                    return null;
//                }
            },
            'is_send_email',
            'is_send_notify',
            'created_at',
            'updated_at'
        ];
    }
}
