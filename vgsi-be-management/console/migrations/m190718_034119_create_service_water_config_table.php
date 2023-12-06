<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_water_config}}`.
 */
class m190718_034119_create_service_water_config_table extends Migration
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
        
        $this->createTable('{{%service_water_config}}', [
            'id' => $this->primaryKey(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'type' => $this->integer(1)->defaultValue(0)->comment('0 - thu phí theo căn hộ, 1 - thu phí theo đầu người/ căn hộ'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-service_water_config-type','service_water_config','type' );
        $this->createIndex( 'idx-service_water_config-service_map_management_id','service_water_config','service_map_management_id' );
        $this->createIndex( 'idx-service_water_config-building_cluster_id','service_water_config','building_cluster_id' );
        $this->createIndex( 'idx-service_water_config-building_area_id','service_water_config','building_area_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_water_config}}');
    }
}
