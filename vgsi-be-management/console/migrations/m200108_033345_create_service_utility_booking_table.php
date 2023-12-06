<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_utility_booking}}`.
 */
class m200108_033345_create_service_utility_booking_table extends Migration
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
        $this->createTable('{{%service_utility_booking}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11)->notNull(),
            'service_utility_config_id' => $this->integer(11)->notNull(),
            'service_utility_free_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(0)->comment('-1: hủy yêu cầu, 0: khởi tạo, 1: xác nhận yêu cầu'),
            'start_time' => $this->integer(11),
            'end_time' => $this->integer(11),
            'total_adult' => $this->integer(11)->defaultValue(0)->comment('Số lượng người lớn'),
            'total_child' => $this->integer(11)->defaultValue(0)->comment('Số lượng trẻ em'),
            'total_slot' => $this->integer(11)->defaultValue(0)->comment('Tổng chỗ đặt'),
            'price' => $this->integer(11)->defaultValue(0),
            'fee_of_month' => $this->integer(11),
            'service_payment_fee_id' => $this->integer(11),
            'description' => $this->text(),
            'json_desc' => $this->text(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-service_utility_booking-building_cluster_id','service_utility_booking','building_cluster_id' );
        $this->createIndex( 'idx-service_utility_booking-apartment_id','service_utility_booking','apartment_id' );
        $this->createIndex( 'idx-service_utility_booking-service_utility_config_id','service_utility_booking','service_utility_config_id' );
        $this->createIndex( 'idx-service_utility_booking-service_utility_free_id','service_utility_booking','service_utility_free_id' );
        $this->createIndex( 'idx-service_utility_booking-status','service_utility_booking','status' );
        $this->createIndex( 'idx-service_utility_booking-total_slot','service_utility_booking','total_slot' );
        $this->createIndex( 'idx-service_utility_booking-price','service_utility_booking','price' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_utility_booking}}');
    }
}
