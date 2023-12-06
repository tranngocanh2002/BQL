<?php

use yii\db\Migration;

/**
 * Class m191123_064002_add_column_in_eparking_card_history
 */
class m191123_064002_add_column_in_eparking_card_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('eparking_card_history', 'service_management_vehicle_id', $this->integer(11)->comment('Id xe'));
        $this->addColumn('eparking_card_history', 'building_cluster_id', $this->integer(11));
        $this->addColumn('eparking_card_history', 'apartment_id', $this->integer(11));
        $this->createIndex( 'idx-eparking_card_history-service_management_vehicle_id','eparking_card_history','service_management_vehicle_id' );
        $this->createIndex( 'idx-eparking_card_history-building_cluster_id','eparking_card_history','building_cluster_id' );
        $this->createIndex( 'idx-eparking_card_history-apartment_id','eparking_card_history','apartment_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191123_064002_add_column_in_eparking_card_history cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191123_064002_add_column_in_eparking_card_history cannot be reverted.\n";

        return false;
    }
    */
}
