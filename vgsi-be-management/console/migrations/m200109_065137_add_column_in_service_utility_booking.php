<?php

use yii\db\Migration;

/**
 * Class m200109_065137_add_column_in_service_utility_booking
 */
class m200109_065137_add_column_in_service_utility_booking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_booking', 'service_map_management_id', $this->integer(11));
        $this->addColumn('service_utility_booking', 'is_created_fee', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200109_065137_add_column_in_service_utility_booking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200109_065137_add_column_in_service_utility_booking cannot be reverted.\n";

        return false;
    }
    */
}
