<?php

use yii\db\Migration;

/**
 * Class m230418_144459_add_column_in_management_user_table
 */
class m230418_144459_add_column_in_management_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('management_user', 'is_send_email', $this->integer(11)->defaultValue(1));
        $this->addColumn('management_user', 'is_send_notify', $this->integer(11)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230418_144459_add_column_in_management_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230418_144459_add_column_in_management_user_table cannot be reverted.\n";

        return false;
    }
    */
}
