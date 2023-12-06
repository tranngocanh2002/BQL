<?php

namespace console\controllers;

use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\rbac\AuthGroup;
use common\models\rbac\AuthItem;
use common\models\rbac\AuthItemChild;
use common\models\rbac\AuthItemWeb;
use common\models\rbac\AuthorRule;
use common\models\rbac\AuthRule;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

class RbacController extends Controller
{
    public function actionReset($type = 'all')
    {
        $auth = Yii::$app->authManager;
        if ($type == 'all') {
            $auth->removeAll();
        } else if ($type == 'assignments') {
            $auth->removeAllAssignments();
        } else if ($type == 'permissions') {
            $auth->removeAllPermissions();
        } else if ($type == 'roles') {
            $auth->removeAllRoles();
        }
    }

    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $fullPermissionRole = $this->createRole('FullPermissionRole');
        $auth->removeChildren($fullPermissionRole);
        $allPermissions = $auth->getPermissions();
        foreach ($allPermissions as $permission) {
            echo $permission->name . "\n";
            $auth->addChild($fullPermissionRole, $permission);
        }
    }

    private function createRole($role_name)
    {
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($role_name);
        if (!$role) {
            $role = $auth->createRole($role_name);
            $auth->add($role);
        }
        return $role;
    }

    public function actionUpdateRole($role_name, $permission)
    {
        $auth = \Yii::$app->authManager;
        $role = $this->createRole($role_name);
        $per = $auth->getPermission($permission);
        if ($per) {
            $auth->addChild($role, $per);
        } else {
            echo $permission . ' Not exist';
        }
    }

    public function actionAssignRole($email, $role_name, $building_id)
    {
        // the following three lines were added:
        $auth = \Yii::$app->authManager;
        $perRole = $auth->getRole($role_name);
        $user = ManagementUser::findOne(['email' => $email, 'building_cluster_id' => $building_id]);
        if ($user) {
            $auth->assign($perRole, $user->getId());
        }
    }

    public function actionSetRule()
    {
        $auth = Yii::$app->authManager;

        // add the rule
        $rule = new AuthorRule();
        $auth->add($rule);

        // add the "updateOwnPost" permission and associate the rule with it.
        $viewOwnPost = $auth->createPermission('viewOwnPost');
        $viewOwnPost->description = 'View own post';
        $viewOwnPost->ruleName = $rule->name;
        $auth->add($viewOwnPost);

        // "updateOwnPost" will be used from "updatePost"
        $viewPost = $auth->getPermission('/post/view');
        $auth->addChild($viewOwnPost, $viewPost);

        $adminRole = $auth->getRole('admin');
        // allow "author" to update their own posts
        $auth->addChild($adminRole, $viewOwnPost);
    }

    public function actionImport($file_name = 'rbacDataJson.json')
    {
        $rbacDataJson = dirname((dirname(__DIR__))) . "/db_desgin/" . $file_name;
        $dataJson = json_decode(file_get_contents($rbacDataJson), true);
        try {
            $auth = Yii::$app->authManager;
            $auth->removeAll();
            AuthItemWeb::deleteAll();
            if (!empty($dataJson['authItems'])) {
                foreach ($dataJson['authItems'] as $item){
                    $childs = $item['childs'];
                    unset($item['childs']);
                    $authItem = new AuthItem();
                    $authItem->load($item, '');
                    if(!$authItem->save()){
                        break;
                    }
                    foreach ($childs as $child){
                        $itemChild = new AuthItemChild();
                        $itemChild->load($child, '');
                        if(!$itemChild->save()){
                            break;
                        }
                    }
                }
            }
            if (!empty($dataJson['authItemWebs'])) {
                foreach ($dataJson['authItemWebs'] as $itemWebs){
                    $authItemWeb = new AuthItemWeb();
                    $authItemWeb->load($itemWebs, '');
                    if(!$authItemWeb->save()){
                        break;
                    }
                }
            }
            if (!empty($dataJson['authRules'])) {
                foreach ($dataJson['authRules'] as $rule){
                    $authRule = new AuthRule();
                    $authRule->load($rule, '');
                    if(!$authRule->save()){
                        break;
                    }
                }
            }

            $managementUsers = ManagementUser::find()->where(['is_deleted' => ManagementUser::NOT_DELETED])->andWhere(['not', ['auth_group_id' => null]])->all();
            foreach ($managementUsers as $managementUser){
                $authGroup = AuthGroup::findOne(['id' => $managementUser->auth_group_id]);
                if ($authGroup) {
                    //add quyền mới
                    $authItems = AuthItem::findAll(['name' => $authGroup->getDataRoleArray()]);
                    //add quyền mới
                    $insert_array = [];
                    foreach ($authItems as $role) {
                        $insert_array[] = [
                            (string)$managementUser->id,
                            $role->name,
                            time()
                        ];
                    }
                    Yii::$app->db->createCommand()->batchInsert('auth_assignment', [
                        'user_id', 'item_name', 'created_at'
                    ], $insert_array)->execute();
                }
            }

            $authGroups = AuthGroup::find()->all();
            foreach ($authGroups as $authGroup){
                $authGroup->updatePermissionUser();
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            echo 'Import Error';
        }
    }

    public function actionExport()
    {
        $dataJson = [
            'authItems' => [],
            'authItemWebs' => [],
            'authRules' => [],
            'authGroups' => [],
        ];
        $authItems = AuthItem::find()->all();
        foreach ($authItems as $authItem) {
            $authItemChilds = AuthItemChild::find()->where(['parent' => $authItem->name])->all();
            $childs = [];
            foreach ($authItemChilds as $authItemChild) {
                $childs[] = $authItemChild->toArray();
            }
            $authItem = $authItem->toArray();
            $authItem['childs'] = $childs;
            $dataJson['authItems'][] = $authItem;
        }

        $authItemWebs = AuthItemWeb::find()->all();
        foreach ($authItemWebs as $authItemWeb) {
            $dataJson['authItemWebs'][] = $authItemWeb->toArray();
        }

        $authItemRules = AuthRule::find()->all();
        foreach ($authItemRules as $authItemRule) {
            $dataJson['authRules'][] = $authItemRule->toArray();
        }

        file_put_contents('db_desgin/rbacDataJson.json', json_encode($dataJson));
    }
}