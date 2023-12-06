<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_payment_fee}}`.
 */
class m190529_065348_create_service_payment_fee_table extends Migration
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
        $this->createTable('{{%service_payment_fee}}', [
            'id' => $this->primaryKey(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11)->notNull(),
            'apartment_id' => $this->integer(11)->notNull(),
            'description' => $this->string(255),
            'price' => $this->integer(11)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('0 : chưa thanh toán, 1 - đã thanh toán'),
            'fee_of_month' => $this->integer(11)->comment('Thành toán phí tháng ? '),
            'day_expired' => $this->integer(11)->comment('Ngày hết hạn thanh toán'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_payment_fee-service_map_management_id','service_payment_fee','service_map_management_id' );
        $this->createIndex( 'idx-service_payment_fee-building_cluster_id','service_payment_fee','building_cluster_id' );
        $this->createIndex( 'idx-service_payment_fee-apartment_id','service_payment_fee','apartment_id' );
        $this->createIndex( 'idx-service_payment_fee-price','service_payment_fee','price' );
        $this->createIndex( 'idx-service_payment_fee-status','service_payment_fee','status' );
        $this->createIndex( 'idx-service_payment_fee-fee_of_month','service_payment_fee','fee_of_month' );
        $this->createIndex( 'idx-service_payment_fee-day_expired','service_payment_fee','day_expired' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_payment_fee}}');
    }
}
