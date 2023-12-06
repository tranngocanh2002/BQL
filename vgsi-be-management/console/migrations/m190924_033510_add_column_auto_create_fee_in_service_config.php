<?php

use yii\db\Migration;

/**
 * Class m190924_033510_add_column_auto_create_fee_in_service_config
 */
class m190924_033510_add_column_auto_create_fee_in_service_config extends Migration
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

        $this->addColumn('service_building_config', 'auto_create_fee', $this->integer(11)->defaultValue(1)->comment('0 - không tạo phí tự động, 1 - tạo phí tự động'));
        $this->createIndex( 'idx-service_building_config-auto_create_fee','service_building_config','auto_create_fee' );

        $this->createTable('{{%service_vehicle_config}}', [
            'id' => $this->primaryKey(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'auto_create_fee' => $this->integer(11)->defaultValue(1)->comment('0 - không tạo phí tự động, 1 - tạo phí tự động'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-service_vehicle_config-auto_create_fee','service_vehicle_config','auto_create_fee' );
        $this->createIndex( 'idx-service_vehicle_config-service_map_management_id','service_vehicle_config','service_map_management_id' );
        $this->createIndex( 'idx-service_vehicle_config-building_cluster_id','service_vehicle_config','building_cluster_id' );
        $this->createIndex( 'idx-service_vehicle_config-building_area_id','service_vehicle_config','building_area_id' );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190924_033510_add_column_auto_create_fee_in_service_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190924_033510_add_column_auto_create_fee_in_service_config cannot be reverted.\n";

        return false;
    }
    */
}
