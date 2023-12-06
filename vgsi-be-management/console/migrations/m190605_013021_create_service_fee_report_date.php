<?php

use yii\db\Migration;

/**
 * Class m190605_013021_create_service_fee_report_date
 */
class m190605_013021_create_service_fee_report_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%service_fee_report_date}}', [
            'id' => $this->primaryKey(),
            'date' => $this->integer(11)->notNull()->comment('báo cáo ngày'),
            'status' => $this->integer(11)->defaultValue(0)->comment('Trạng thái 0 - chưa thanh toán, 1 - đã thanh toán'),
            'service_map_management_id' => $this->integer(11)->comment('theo loai dich vụ'),
            'total_price' => $this->integer(11)->defaultValue(0)->comment('Tổng tiền'),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
        $this->createIndex( 'idx-service_fee_report_date-date','service_fee_report_date','date' );
        $this->createIndex( 'idx-service_fee_report_date-status','service_fee_report_date','status' );
        $this->createIndex( 'idx-service_fee_report_date-service_map_management_id','service_fee_report_date','service_map_management_id' );
        $this->createIndex( 'idx-service_fee_report_date-total_price','service_fee_report_date','total_price' );
        $this->createIndex( 'idx-service_fee_report_date-building_cluster_id','service_fee_report_date','building_cluster_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190605_013021_create_service_fee_report_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190605_013021_create_service_fee_report_date cannot be reverted.\n";

        return false;
    }
    */
}
