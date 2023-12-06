<?php

use yii\db\Migration;

/**
 * Class m190612_020338_add_column_is_send_notify_in_resident
 */
class m190612_020338_add_column_is_send_notify_in_resident extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user', 'is_send_notify', $this->integer(1)->defaultValue(1)->comment('1 - co nhan notify, 0 - ko nhan notify'));
        $this->addColumn('apartment_map_resident_user', 'resident_user_is_send_notify', $this->integer(1)->defaultValue(1)->comment('1 - co nhan notify, 0 - ko nhan notify'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190612_020338_add_column_is_send_notify_in_resident cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190612_020338_add_column_is_send_notify_in_resident cannot be reverted.\n";

        return false;
    }
    */
}
