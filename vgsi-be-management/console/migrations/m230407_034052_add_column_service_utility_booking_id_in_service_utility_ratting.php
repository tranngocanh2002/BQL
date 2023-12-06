<?php

use yii\db\Migration;

/**
 * Class m230407_034052_add_column_service_utility_booking_id_in_service_utility_ratting
 */
class m230407_034052_add_column_service_utility_booking_id_in_service_utility_ratting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_ratting', 'service_utility_booking_id', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230407_034052_add_column_service_utility_booking_id_in_service_utility_ratting cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230407_034052_add_column_service_utility_booking_id_in_service_utility_ratting cannot be reverted.\n";

        return false;
    }
    */
}
