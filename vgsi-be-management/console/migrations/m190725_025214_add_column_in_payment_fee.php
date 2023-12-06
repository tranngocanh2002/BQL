<?php

use yii\db\Migration;

/**
 * Class m190725_025214_add_column_in_payment_fee
 */
class m190725_025214_add_column_in_payment_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_payment_fee', 'type', $this->integer(11)->defaultValue(0)->comment('0 - phí nước, 1 - phí dịch vụ, 2 - phí xe'));
        $this->addColumn('service_payment_fee', 'start_time', $this->integer(11));
        $this->addColumn('service_payment_fee', 'end_time', $this->integer(11));
        $this->createIndex( 'idx-service_payment_fee-type','service_payment_fee','type' );
        $this->createIndex( 'idx-service_payment_fee-start_time','service_payment_fee','start_time' );
        $this->createIndex( 'idx-service_payment_fee-end_time','service_payment_fee','end_time' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_025214_add_column_in_payment_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_025214_add_column_in_payment_fee cannot be reverted.\n";

        return false;
    }
    */
}
