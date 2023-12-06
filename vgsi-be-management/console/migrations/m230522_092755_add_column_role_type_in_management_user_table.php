<?php

use yii\db\Migration;

/**
 * Class m230522_092755_add_column_role_type_in_management_user_table
 */
class m230522_092755_add_column_role_type_in_management_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('management_user', 'role_type', $this->integer(2)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230522_092755_add_column_role_type_in_management_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230522_092755_add_column_role_type_in_management_user_table cannot be reverted.\n";

        return false;
    }
    */
}
