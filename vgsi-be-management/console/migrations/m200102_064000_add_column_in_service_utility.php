<?php

use yii\db\Migration;

/**
 * Class m200102_064000_add_column_in_service_utility
 */
class m200102_064000_add_column_in_service_utility extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_free', 'booking_type', $this->integer(11)->defaultValue(0)->comment('0 - đặt theo lượt, 1 - đặt theo slot'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200102_064000_add_column_in_service_utility cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200102_064000_add_column_in_service_utility cannot be reverted.\n";

        return false;
    }
    */
}
