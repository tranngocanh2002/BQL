<?php

use yii\db\Migration;

/**
 * Class m190613_020150_add_column_resident_user_name_in_apartment
 */
class m190613_020150_add_column_resident_user_name_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment', 'resident_user_name', $this->string(255)->comment('Tên chủ hộ'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190613_020150_add_column_resident_user_name_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190613_020150_add_column_resident_user_name_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
