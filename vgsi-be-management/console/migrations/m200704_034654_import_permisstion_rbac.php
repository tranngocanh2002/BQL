<?php

use common\models\ManagementUser;
use common\models\rbac\AuthGroup;
use common\models\rbac\AuthItem;
use common\models\rbac\AuthItemChild;
use common\models\rbac\AuthItemWeb;
use common\models\rbac\AuthRule;
use yii\db\Migration;

/**
 * Class m200704_034654_import_permisstion_rbac
 */
class m200704_034654_import_permisstion_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'title_en', $this->string(255));
        $this->addColumn('announcement_category', 'name_en', $this->string(255));
        $this->addColumn('announcement_item', 'title_en', $this->string(255));
        $this->addColumn('announcement_template', 'name_en', $this->string(255));
        $this->addColumn('help_category', 'name_en', $this->string(255));
        $this->addColumn('help', 'title_en', $this->string(255));
        $this->addColumn('post_category', 'name_en', $this->string(255));
        $this->addColumn('post', 'title_en', $this->string(255));
        $this->addColumn('request', 'title_en', $this->string(255));
        $this->addColumn('request_category', 'name_en', $this->string(255));
        $this->addColumn('service_map_management', 'service_name_en', $this->string(255));
        $this->addColumn('service_parking_level', 'name_en', $this->string(255));
        $this->addColumn('service_electric_level', 'name_en', $this->string(255));
        $this->addColumn('service_water_level', 'name_en', $this->string(255));
        $this->addColumn('service_electric_fee', 'description_en', $this->text());
        $this->addColumn('service_old_debit_fee', 'description_en', $this->text());
        $this->addColumn('service_parking_fee', 'description_en', $this->text());
        $this->addColumn('service_payment_fee', 'description_en', $this->text());
        $this->addColumn('service_water_fee', 'description_en', $this->text());
        $this->addColumn('service_building_fee', 'description_en', $this->text());
        $this->addColumn('service_utility_config', 'name_en', $this->string(255));
        $this->addColumn('service_utility_config', 'address_en', $this->string(255));
        $this->addColumn('service_utility_free', 'name_en', $this->string(255));
        $this->addColumn('auth_group', 'name_en', $this->string(255));
        $this->addColumn('building_cluster', 'link_dksd', $this->string(255));

        $rbacDataJson = dirname((dirname(__DIR__))) . "/db_desgin/rbacDataJson.json";
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200704_034654_import_permisstion_rbac cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200704_034654_import_permisstion_rbac cannot be reverted.\n";

        return false;
    }
    */
}
