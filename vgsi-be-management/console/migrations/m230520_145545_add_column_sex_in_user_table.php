<?php

use yii\db\Migration;

/**
 * Class m230520_145545_add_column_sex_in_user_table
 */
class m230520_145545_add_column_sex_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'sex', $this->integer(1)->defaultValue(0)->comment('0 - Nam, 1 - Nữ'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230520_145545_add_column_sex_in_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230520_145545_add_column_sex_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
