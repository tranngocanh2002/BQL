<?php

use yii\db\Migration;

/**
 * Class m230611_022919_add_column_is_send_notify_in_service_utility_booking
 */
class m230611_022919_add_column_is_send_notify_in_service_utility_booking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_booking', 'is_send_notify', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230611_022919_add_column_is_send_notify_in_service_utility_booking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230611_022919_add_column_is_send_notify_in_service_utility_booking cannot be reverted.\n";

        return false;
    }
    */
}
