<?php

use yii\db\Migration;

/**
 * Class m190521_081840_modify_request_map_auth_group_table
 */
class m190521_081840_modify_request_map_auth_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('request_map_auth_group','request_category_map_auth_group');
        $this->renameColumn('request_category_map_auth_group','request_id','request_category_id');
        $this->createIndex( 'idx-request_category_map_auth_group-auth_group_id','request_category_map_auth_group','auth_group_id' );
        $this->createIndex( 'idx-request_category_map_auth_group-request_category_id','request_category_map_auth_group','request_category_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190521_081840_modify_request_map_auth_group_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190521_081840_modify_request_map_auth_group_table cannot be reverted.\n";

        return false;
    }
    */
}
