<?php

use yii\db\Migration;

/**
 * Class m230520_144417_add_column_full_name_and_birthday_in_user_table
 */
class m230520_144417_add_column_full_name_and_birthday_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'full_name', $this->string(255));
        $this->addColumn('user', 'birthday', $this->integer(12));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230520_144417_add_column_full_name_and_birthday_in_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230520_144417_add_column_full_name_and_birthday_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
