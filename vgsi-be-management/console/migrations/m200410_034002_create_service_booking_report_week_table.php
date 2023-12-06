<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_booking_report_week}}`.
 */
class m200410_034002_create_service_booking_report_week_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%service_booking_report_week}}', [
            'id' => $this->primaryKey(),
            'date' => $this->integer(11)->comment('Ngày đầu tuần'),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - Chưa thanh toán, 1- đã thanh toán'),
            'building_cluster_id' => $this->integer(11),
            'service_map_management_id' => $this->integer(11),
            'service_utility_config_id' => $this->integer(11),
            'service_utility_free_id' => $this->integer(11),
            'total_price' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
        $this->createIndex( 'idx-service_booking_report_week-status','service_booking_report_week','status' );
        $this->createIndex( 'idx-service_booking_report_week-date','service_booking_report_week','date' );
        $this->createIndex( 'idx-service_booking_report_week-building_cluster_id','service_booking_report_week','building_cluster_id' );
        $this->createIndex( 'idx-service_booking_report_week-service_map_management_id','service_booking_report_week','service_map_management_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_booking_report_week}}');
    }
}
