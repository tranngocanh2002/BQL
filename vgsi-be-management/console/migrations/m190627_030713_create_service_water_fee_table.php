<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_water_fee}}`.
 */
class m190627_030713_create_service_water_fee_table extends Migration
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
        $this->createTable('{{%service_water_fee}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11),
            'service_map_management_id' => $this->integer(11),
            'start_index' => $this->integer(11)->defaultValue(0)->comment('chỉ số đầu'),
            'end_index' => $this->integer(11)->defaultValue(0)->comment('chỉ số cuối'),
            'total_index' => $this->integer(11)->defaultValue(0)->comment('tổng chỉ số sử dụng'),
            'total_money' => $this->integer(11)->defaultValue(0)->comment('tổng tiền'),
            'lock_time' => $this->integer(11)->defaultValue(0)->comment('thời gian chốt'),
            'description' => $this->text(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa duyệt, 1 - đã duyệt'),
            'is_created_fee' => $this->integer(1)->defaultValue(0)->comment('0 - chưa tạo phí thanh toán, 1 - đã tạo phí thanh toán => không được sửa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex('idx-service_water_fee-service_map_management_id', 'service_water_fee', 'service_map_management_id');
        $this->createIndex('idx-service_water_fee-lock_time', 'service_water_fee', 'lock_time');
        $this->createIndex('idx-service_water_fee-status', 'service_water_fee', 'status');
        $this->createIndex('idx-service_water_fee-building_cluster_id', 'service_water_fee', 'building_cluster_id');
        $this->createIndex('idx-service_water_fee-is_created_fee', 'service_water_fee', 'is_created_fee');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_water_fee}}');
    }
}
