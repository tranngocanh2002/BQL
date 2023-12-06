<?php

use yii\db\Migration;

/**
 * Class m190716_014416_add_column_fee_of_month_in_service_fee
 */
class m190716_014416_add_column_fee_of_month_in_service_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_water_fee', 'fee_of_month', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190716_014416_add_column_fee_of_month_in_service_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190716_014416_add_column_fee_of_month_in_service_fee cannot be reverted.\n";

        return false;
    }
    */
}
