<?php

use yii\db\Migration;

/**
 * Class m200205_025144_modify_column_price_in_service_utility_price
 */
class m200205_025144_modify_column_price_in_service_utility_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('service_utility_price', 'price_hourly', $this->integer(11)->defaultValue(0));
        $this->alterColumn('service_utility_price', 'price_adult', $this->integer(11)->defaultValue(0));
        $this->alterColumn('service_utility_price', 'price_child', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200205_025144_modify_column_price_in_service_utility_price cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_025144_modify_column_price_in_service_utility_price cannot be reverted.\n";

        return false;
    }
    */
}
