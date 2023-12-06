<?php

namespace common\models\rbac;

use common\helpers\ErrorCode;
use common\models\ManagementUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AssignAuthGroupForm")
 * )
 */
class AssignAuthGroupForm extends Model
{
    /**
     * @SWG\Property(description="User Management Id", default=1, type="integer")
     * @var integer
     */
    public $user_management_id;

    /**
     * @SWG\Property(description="Auth Group Code", default="", type="string")
     * @var string
     */
    public $auth_group_code;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_management_id'], 'integer'],
            [['auth_group_code'], 'string'],
            [['user_management_id', 'auth_group_code'], 'required'],
        ];
    }

    public function assign()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $userManagement = ManagementUser::find()->where(['id' => $this->user_management_id, 'is_deleted' => 0, 'status' => 1])->one();
            $authGroup = AuthGroup::findOne(['code' => $this->auth_group_code]);
            if (!empty($userManagement) && !empty($authGroup)) {
                $userManagement->auth_group_id = $authGroup->id;
                $auth = \Yii::$app->authManager;
                AuthAssignment::deleteAll(['user_id' => $userManagement->id]);
                if (!empty($authGroup->data_role)) {
                    $roles = json_decode($authGroup->data_role);
                    foreach ($roles as $role) {
                        $perRole = $auth->getRole($role);
                        if(!empty($perRole)){
                            $auth->assign($perRole, $userManagement->id);
                        }
                    }
                }
                if(!$userManagement->save()){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => 'System Error',
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $userManagement->getErrors()
                    ];
                }
                $transaction->commit();
                return '';
            } else {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => 'Data Not Exist',
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => $ex->getMessage(),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                // 'errors' => $ex->getMessage()
            ];
        }
    }
}
