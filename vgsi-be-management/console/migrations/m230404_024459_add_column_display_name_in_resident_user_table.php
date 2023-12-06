<?php

use yii\db\Migration;

/**
 * Class m230404_024459_add_column_display_name_in_resident_user_table
 */
class m230404_024459_add_column_display_name_in_resident_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user', 'display_name', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230404_024459_add_column_display_name_in_resident_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230404_024459_add_column_display_name_in_resident_user_table cannot be reverted.\n";

        return false;
    }
    */
}
