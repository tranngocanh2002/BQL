<?php

use yii\db\Migration;

/**
 * Class m230714_091454_add_column_logged_in_user_table
 */
class m230714_091454_add_column_logged_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'logged',  $this->integer(1)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230714_091454_add_column_logged_in_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230714_091454_add_column_logged_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
