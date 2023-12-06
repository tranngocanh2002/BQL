<?php

use yii\db\Migration;

/**
 * Class m190607_073840_add_column_service_bill_id_in_service_payment_fee
 */
class m190607_073840_add_column_service_bill_id_in_service_payment_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_payment_fee', 'service_bill_id', $this->integer(11));
        $this->addColumn('service_payment_fee', 'service_bill_code', $this->string(255));
        $this->createIndex( 'idx-service_payment_fee-service_bill_id','service_payment_fee','service_bill_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190607_073840_add_column_service_bill_id_in_service_payment_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190607_073840_add_column_service_bill_id_in_service_payment_fee cannot be reverted.\n";

        return false;
    }
    */
}
