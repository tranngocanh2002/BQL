<?php

use yii\db\Migration;

/**
 * Class m190725_041349_add_column_service_payment_fee_id
 */
class m190725_041349_add_column_service_payment_fee_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_parking_fee', 'service_payment_fee_id', $this->integer(11));
        $this->createIndex( 'idx-service_parking_fee-service_payment_fee_id','service_parking_fee','service_payment_fee_id' );

        $this->addColumn('service_water_fee', 'service_payment_fee_id', $this->integer(11));
        $this->createIndex( 'idx-service_water_fee-service_payment_fee_id','service_water_fee','service_payment_fee_id' );

        $this->addColumn('service_building_fee', 'service_payment_fee_id', $this->integer(11));
        $this->createIndex( 'idx-service_building_fee-service_payment_fee_id','service_building_fee','service_payment_fee_id' );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_041349_add_column_service_payment_fee_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_041349_add_column_service_payment_fee_id cannot be reverted.\n";

        return false;
    }
    */
}
