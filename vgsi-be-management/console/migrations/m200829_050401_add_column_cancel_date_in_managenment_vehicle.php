<?php

use yii\db\Migration;

/**
 * Class m200829_050401_add_column_cancel_date_in_managenment_vehicle
 */
class m200829_050401_add_column_cancel_date_in_managenment_vehicle extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_management_vehicle', 'cancel_date', $this->integer(11)->comment('Thời điểm hủy'));
        $this->createIndex( 'idx-service_management_vehicle-cancel_date','service_management_vehicle','cancel_date' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200829_050401_add_column_cancel_date_in_managenment_vehicle cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200829_050401_add_column_cancel_date_in_managenment_vehicle cannot be reverted.\n";

        return false;
    }
    */
}
