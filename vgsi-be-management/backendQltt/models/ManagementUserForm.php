<?php

namespace backendQltt\models;

use yii\base\Model;
use common\models\ManagementUser;
use common\models\BuildingCluster;
use Yii;
use Exception;
use yii\base\NotSupportedException;

/**
 * Password reset form
 */
class ManagementUserForm extends Model {

    public $password;
    public $email;
    public $auth_group_id;
    public $building_cluster_id;
    public $confirm_password;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['password', 'confirm_password', 'building_cluster_id', 'auth_group_id', 'email'], 'required'],
            ['password', 'string', 'min' => 8],
            ['confirm_password', 'compare', 'compareAttribute'=>'password', 'message' => Yii::t('backendQltt', " Mật khẩu không trùng khớp" )],
            ['email', 'email', 'message' => Yii::t('backendQltt', " Email không đúng định dạng" )],
            ['email', 'match', 'pattern' => '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'message' => Yii::t('common', 'Email không đúng định dạng.')],
            ['password', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/u', 'message' => Yii::t('backendQltt', 'Mật khẩu không đúng định dạng.')],
            ['confirm_password', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/u', 'message' => Yii::t('backendQltt', 'Nhập lại mật khẩu không đúng định dạng.')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'email' => Yii::t('common', 'Email'),
            'password' => Yii::t('common', 'Mật khẩu'),
            'confirm_password' => Yii::t('common', 'Nhập lại mật khẩu'),
        ];
    }

    public function store()
    {
        try {
            $managementUser = new ManagementUser();
            $managementUser->email = $this->email;
            $managementUser->password = Yii::$app->security->generatePasswordHash("$this->password");
            $managementUser->auth_group_id = $this->auth_group_id;
            $managementUser->building_cluster_id = $this->building_cluster_id;
            $managementUser->role_type = ManagementUser::SUPPER_ADMIN;

            if (!$managementUser->save()) {
                Yii::error($managementUser->errors);

                throw new NotSupportedException('Create error');
            }

            return true;
        } catch (Exception $e) {
            throw new NotSupportedException($e->getMessage());
        }
    }

    public static function findModelByBuildingClusterId($id)
    {
        if (($model = ManagementUser::findOne(['building_cluster_id' => $id, 'role_type' => ManagementUser::SUPPER_ADMIN])) !== null) {
            return $model;
        } else {
            // Yii::$app->session->setFlash('error', Yii::t('backend', 'Management User BuildingCluster Error'));
            return new ManagementUser();
        }
    }

    public function updateRecord($id,$email = null)
    {
        try {
            if(null != $email)
            {
                $buildingCluster = BuildingCluster::findOne(['id' => $id]);
                $buildingCluster->email = $email;
                $buildingCluster->save();
            }

            $managementUser = $this->findModelByBuildingClusterId($id);
            $managementUser->email = $email ?? $this->email;
            $managementUser->password = Yii::$app->security->generatePasswordHash("$this->password");

            if (!$managementUser->save()) {
                Yii::error($managementUser->errors);

                throw new NotSupportedException('Create error');
            }
            if (!$buildingCluster->save()) {
                Yii::error($buildingCluster->errors);

                throw new NotSupportedException('Create error');
            }

            return true;
        } catch (\Throwable $th) {
            throw new NotSupportedException($th->getMessage());
        }
    }
}
