<?php

use yii\db\Migration;

/**
 * Class m190716_082704_add_service_parking_table
 */
class m190716_082704_add_service_parking_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%service_parking_level}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'code' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'service_id' => $this->integer(11)->notNull(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'price' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_parking_level-price','service_parking_level','price' );
        $this->createIndex( 'idx-service_parking_level-service_id','service_parking_level','service_id' );
        $this->createIndex( 'idx-service_parking_level-service_map_management_id','service_parking_level','service_map_management_id' );
        $this->createIndex( 'idx-service_parking_level-building_cluster_id','service_parking_level','building_cluster_id' );
        $this->createIndex( 'idx-service_parking_level-building_area_id','service_parking_level','building_area_id' );

        $this->createTable('{{%service_management_vehicle}}', [
            'id' => $this->primaryKey(),
            'number' => $this->string(255)->comment('biển số xe')->notNull(),
            'description' => $this->text(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11)->notNull(),
            'service_parking_level_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_management_vehicle-apartment_id','service_management_vehicle','apartment_id' );
        $this->createIndex( 'idx-service_management_vehicle-service_parking_level_id','service_management_vehicle','service_parking_level_id' );
        $this->createIndex( 'idx-service_management_vehicle-building_cluster_id','service_management_vehicle','building_cluster_id' );
        $this->createIndex( 'idx-service_management_vehicle-building_area_id','service_management_vehicle','building_area_id' );


        $this->createTable('{{%service_parking_fee}}', [
            'id' => $this->primaryKey(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11)->notNull(),
            'count_month' => $this->integer(11)->comment('số tháng gửi tiếp theo'),
            'start_time' => $this->integer(11),
            'end_time' => $this->integer(11),
            'service_parking_level_id' => $this->integer(11)->notNull(),
            'service_management_vehicle_id' => $this->integer(11)->notNull(),
            'total_money' => $this->integer(11)->defaultValue(0),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa duyệt, 1 - đã duyệt'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_parking_fee-service_map_management_id','service_parking_fee','service_map_management_id' );
        $this->createIndex( 'idx-service_parking_fee-service_parking_level_id','service_parking_fee','service_parking_level_id' );
        $this->createIndex( 'idx-service_parking_fee-service_management_vehicle_id','service_parking_fee','service_management_vehicle_id' );
        $this->createIndex( 'idx-service_parking_fee-building_cluster_id','service_parking_fee','building_cluster_id' );
        $this->createIndex( 'idx-service_parking_fee-building_area_id','service_parking_fee','building_area_id' );
        $this->createIndex( 'idx-service_parking_fee-apartment_id','service_parking_fee','apartment_id' );
        $this->createIndex( 'idx-service_parking_fee-total_money','service_parking_fee','total_money' );
        $this->createIndex( 'idx-service_parking_fee-status','service_parking_fee','status' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190716_082704_add_service_parking_table cannot be reverted.\n";
        $this->dropTable('{{%service_parking_level}}');
        $this->dropTable('{{%service_management_vehicle}}');
        $this->dropTable('{{%service_parking_fee}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190716_082704_add_service_parking_table cannot be reverted.\n";

        return false;
    }
    */
}
