<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_building_fee}}`.
 */
class m190719_033450_create_service_building_fee_table extends Migration
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

        $this->createTable('{{%service_building_fee}}', [
            'id' => $this->primaryKey(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11)->notNull(),
            'count_month' => $this->integer(11)->comment('số tháng gửi tiếp theo'),
            'start_time' => $this->integer(11),
            'end_time' => $this->integer(11),
            'service_building_config_id' => $this->integer(11)->notNull(),
            'total_money' => $this->integer(11)->defaultValue(0),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa duyệt, 1 - đã duyệt'),
            'description' => $this->text(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_building_fee-service_building_config_id','service_building_fee','service_building_config_id' );
        $this->createIndex( 'idx-service_building_fee-service_map_management_id','service_building_fee','service_map_management_id' );
        $this->createIndex( 'idx-service_building_fee-building_cluster_id','service_building_fee','building_cluster_id' );
        $this->createIndex( 'idx-service_building_fee-building_area_id','service_building_fee','building_area_id' );
        $this->createIndex( 'idx-service_building_fee-apartment_id','service_building_fee','apartment_id' );
        $this->createIndex( 'idx-service_building_fee-total_money','service_building_fee','total_money' );
        $this->createIndex( 'idx-service_building_fee-status','service_building_fee','status' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_building_fee}}');
    }
}
