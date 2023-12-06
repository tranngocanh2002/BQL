<?php

use yii\db\Migration;

/**
 * Class m190808_021139_add_column_short_name
 */
class m190808_021139_add_column_short_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_area', 'short_name', $this->string(20));
        $this->addColumn('building_area', 'type', $this->integer(11)->defaultValue(0)->comment('phân loại: 0 - tầng, 1 tòa , 2 ...'));
        $this->addColumn('apartment', 'short_name', $this->string(20));
        $this->addColumn('apartment_map_resident_user', 'apartment_short_name', $this->string(20));

        $this->createIndex( 'idx-building_area-short_name','building_area','short_name' );
        $this->createIndex( 'idx-building_area-type','building_area','type' );
        $this->createIndex( 'idx-apartment-short_name','apartment','short_name' );
        $this->createIndex( 'idx-apartment_map_resident_user-apartment_short_name','apartment_map_resident_user','apartment_short_name' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190808_021139_add_column_short_name cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190808_021139_add_column_short_name cannot be reverted.\n";

        return false;
    }
    */
}
