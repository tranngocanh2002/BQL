<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_info}}`.
 */
class m190725_043535_create_service_info_table extends Migration
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

        $this->createTable('{{%service_water_info}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11)->notNull(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'start_date' => $this->integer(11),
            'end_date' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_water_info-apartment_id','service_water_info','apartment_id' );
        $this->createIndex( 'idx-service_water_info-service_map_management_id','service_water_info','service_map_management_id' );
        $this->createIndex( 'idx-service_water_info-building_cluster_id','service_water_info','building_cluster_id' );
        $this->createIndex( 'idx-service_water_info-building_area_id','service_water_info','building_area_id' );
        $this->createIndex( 'idx-service_water_info-start_date','service_water_info','start_date' );
        $this->createIndex( 'idx-service_water_info-end_date','service_water_info','end_date' );

        $this->createTable('{{%service_building_info}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11)->notNull(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'start_date' => $this->integer(11),
            'end_date' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_building_info-apartment_id','service_building_info','apartment_id' );
        $this->createIndex( 'idx-service_building_info-service_map_management_id','service_building_info','service_map_management_id' );
        $this->createIndex( 'idx-service_building_info-building_cluster_id','service_building_info','building_cluster_id' );
        $this->createIndex( 'idx-service_building_info-building_area_id','service_building_info','building_area_id' );
        $this->createIndex( 'idx-service_building_info-start_date','service_building_info','start_date' );
        $this->createIndex( 'idx-service_building_info-end_date','service_building_info','end_date' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_water_info}}');
        $this->dropTable('{{%service_building_info}}');
    }
}
