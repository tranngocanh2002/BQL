<?php

use yii\db\Migration;

/**
 * Class m230525_151858_add_column_reason_in_service_utility_booking
 */
class m230525_151858_add_column_reason_in_service_utility_booking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_booking', 'reason', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230525_151858_add_column_reason_in_service_utility_booking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230525_151858_add_column_reason_in_service_utility_booking cannot be reverted.\n";

        return false;
    }
    */
}
