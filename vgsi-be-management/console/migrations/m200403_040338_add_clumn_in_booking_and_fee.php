<?php

use yii\db\Migration;

/**
 * Class m200403_040338_add_clumn_in_booking_and_fee
 */
class m200403_040338_add_clumn_in_booking_and_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_booking', 'code', $this->string(255)->comment('Mã booking code'));
        $this->addColumn('service_payment_fee', 'is_debt', $this->integer(11)->defaultValue(1)->comment('0 - Chưa cần chạy công nợ, 1 - được phép tính công nợ'));
        $this->createIndex( 'idx-service_payment_fee-is_debt','service_payment_fee','is_debt' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200403_040338_add_clumn_in_booking_and_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200403_040338_add_clumn_in_booking_and_fee cannot be reverted.\n";

        return false;
    }
    */
}
