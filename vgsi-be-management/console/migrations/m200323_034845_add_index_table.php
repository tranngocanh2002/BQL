<?php

use yii\db\Migration;

/**
 * Class m200323_034845_add_index_table
 */
class m200323_034845_add_index_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex( 'idx-apartment-parent_path','apartment','parent_path' );
        $this->createIndex( 'idx-service_bill-code','service_bill','code' );
        $this->createIndex( 'idx-service_bill-building_cluster_id','service_bill','building_cluster_id' );
        $this->createIndex( 'idx-service_bill-building_area_id','service_bill','building_area_id' );
        $this->createIndex( 'idx-service_bill-apartment_id','service_bill','apartment_id' );
        $this->createIndex( 'idx-service_bill-management_user_id','service_bill','management_user_id' );
        $this->createIndex( 'idx-service_bill-resident_user_id','service_bill','resident_user_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200323_034845_add_index_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200323_034845_add_index_table cannot be reverted.\n";

        return false;
    }
    */
}
