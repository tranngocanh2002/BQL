<?php

use yii\db\Migration;

/**
 * Class m200205_021431_modify_column_in_service_utility_price
 */
class m200205_021431_modify_column_in_service_utility_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('service_utility_price', 'start_time', $this->string(255));
        $this->alterColumn('service_utility_price', 'end_time', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200205_021431_modify_column_in_service_utility_price cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_021431_modify_column_in_service_utility_price cannot be reverted.\n";

        return false;
    }
    */
}
