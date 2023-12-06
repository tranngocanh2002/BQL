<?php

use yii\db\Migration;

/**
 * Class m230521_025724_add_column_avatar_in_user_table
 */
class m230521_025724_add_column_avatar_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'avatar', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230521_025724_add_column_avatar_in_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230521_025724_add_column_avatar_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
