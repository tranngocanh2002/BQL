<?php

use yii\db\Migration;

/**
 * Class m190610_030156_add_column_is_send_email_in_resident
 */
class m190610_030156_add_column_is_send_email_in_resident extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user', 'is_send_email', $this->integer(1)->defaultValue(1)->comment('1 - co nhan email, 0 - ko nhan email'));
        $this->addColumn('apartment_map_resident_user', 'resident_user_is_send_email', $this->integer(1)->defaultValue(1)->comment('1 - co nhan email, 0 - ko nhan email'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190610_030156_add_column_is_send_email_in_resident cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190610_030156_add_column_is_send_email_in_resident cannot be reverted.\n";

        return false;
    }
    */
}
