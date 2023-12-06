<?php

use yii\db\Migration;

/**
 * Class m200102_064336_create_service_utility_config
 */
class m200102_064336_create_service_utility_config extends Migration
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
        $this->createTable('{{%service_utility_config}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'address' => $this->string(255),
            'building_cluster_id' => $this->integer(11),
            'service_utility_free_id' => $this->integer(11),
            'type' => $this->integer(11)->defaultValue(1)->comment('0- không thu phí, 1 - có thu phí'),
            'booking_type' => $this->integer(11)->defaultValue(0)->comment('0 - đặt theo lượt, 1 - đặt theo slot'),
            'start_time' => $this->integer(11),
            'end_time' => $this->integer(11),
            'price_hourly' => $this->integer(11)->comment('Giá theo 1 giờ'),
            'price_adult' => $this->integer(11)->comment('Giá 1 người lớn'),
            'price_child' => $this->integer(11)->comment('Giá 1 trẻ em'),
            'total_slot' => $this->integer(11)->comment('Tổng số chỗ sử dụng'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_utility_config-building_cluster_id','service_utility_config','building_cluster_id' );
        $this->createIndex( 'idx-service_utility_config-service_utility_free_id','service_utility_config','service_utility_free_id' );
        $this->createIndex( 'idx-service_utility_config-type','service_utility_config','type' );
        $this->createIndex( 'idx-service_utility_config-booking_type','service_utility_config','booking_type' );
        $this->createIndex( 'idx-service_utility_config-start_time','service_utility_config','start_time' );
        $this->createIndex( 'idx-service_utility_config-end_time','service_utility_config','end_time' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200102_064336_create_service_utility_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200102_064336_create_service_utility_config cannot be reverted.\n";

        return false;
    }
    */
}
