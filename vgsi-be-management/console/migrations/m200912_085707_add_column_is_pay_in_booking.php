<?php

use yii\db\Migration;

/**
 * Class m200912_085707_add_column_is_pay_in_booking
 */
class m200912_085707_add_column_is_pay_in_booking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_booking', 'is_paid', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200912_085707_add_column_is_pay_in_booking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200912_085707_add_column_is_pay_in_booking cannot be reverted.\n";

        return false;
    }
    */
}
