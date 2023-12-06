<?php

use yii\db\Migration;

/**
 * Class m230512_100619_add_column_type_in_management_user_device_token_table
 */
class m230512_100619_add_column_type_in_management_user_device_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('management_user_device_token', 'type', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230512_100619_add_column_type_in_management_user_device_token_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230512_100619_add_column_type_in_management_user_device_token_table cannot be reverted.\n";

        return false;
    }
    */
}
