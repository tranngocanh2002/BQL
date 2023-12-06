<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_building_config}}`.
 */
class m190605_100503_create_service_building_config_table extends Migration
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

        $this->createTable('{{%service_building_config}}', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(11)->notNull(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'price' => $this->integer(11),
            'unit' => $this->integer(11),
            'day' => $this->integer(11)->defaultValue(1)->comment('ngày tạo phí : mặc định là 1 - (ngày đầu tháng)'),
            'month_cycle' => $this->integer(11)->defaultValue(1)->comment('chu kỳ lặp của tháng: mặc định là 1 - (1 tháng 1 lần)'),
            'cr_minutes' => $this->string(255)->defaultValue('*'),
            'cr_hours' => $this->string(255)->defaultValue('*'),
            'cr_days' => $this->string(255)->defaultValue('*'),
            'cr_months' => $this->string(255)->defaultValue('*'),
            'cr_days_of_week' => $this->string(255)->defaultValue('*'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_building_config-cr_minutes','service_building_config','cr_minutes' );
        $this->createIndex( 'idx-service_building_config-cr_hours','service_building_config','cr_hours' );
        $this->createIndex( 'idx-service_building_config-cr_days','service_building_config','cr_days' );
        $this->createIndex( 'idx-service_building_config-cr_months','service_building_config','cr_months' );
        $this->createIndex( 'idx-service_building_config-cr_days_of_week','service_building_config','cr_days_of_week' );
        $this->createIndex( 'idx-service_building_config-unit','service_building_config','unit' );
        $this->createIndex( 'idx-service_building_config-price','service_building_config','price' );
        $this->createIndex( 'idx-service_building_config-service_id','service_building_config','service_id' );
        $this->createIndex( 'idx-service_building_config-service_map_management_id','service_building_config','service_map_management_id' );
        $this->createIndex( 'idx-service_building_config-building_cluster_id','service_building_config','building_cluster_id' );
        $this->createIndex( 'idx-service_building_config-building_area_id','service_building_config','building_area_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_building_config}}');
    }
}
