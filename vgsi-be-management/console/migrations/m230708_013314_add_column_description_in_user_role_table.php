<?php

use yii\db\Migration;

/**
 * Class m230708_013314_add_column_description_in_user_role_table
 */
class m230708_013314_add_column_description_in_user_role_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_role', 'description', $this->string(1000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230708_013314_add_column_description_in_user_role_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230708_013314_add_column_description_in_user_role_table cannot be reverted.\n";

        return false;
    }
    */
}
