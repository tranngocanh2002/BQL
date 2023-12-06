<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_bill}}`.
 */
class m190529_082001_create_service_bill_table extends Migration
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

        $this->createTable('{{%service_bill}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(255)->notNull()->comment('Mã phiếu thu'),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11),
            'management_user_id' => $this->integer(11)->comment('Người tạo phiếu'),
            'resident_user_id' => $this->integer(11)->comment('Chủ hộ ở thời điểm hiện tại'),
            'resident_user_name' => $this->string(255)->comment('Chủ hộ ở thời điểm hiện tại'),
            'payer_name' => $this->string(255)->comment('Người nộp tiền'),
            'type_payment' => $this->integer(11)->defaultValue(0)->comment('0 - Tiền mặt, 1 - chuyển khoản'),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa thanh toán, 1 - đã thanh toán'),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 - chưa xóa, 1 - đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createTable('{{%service_bill_item}}', [
            'id' => $this->primaryKey(),
            'service_bill_id' => $this->integer(11)->notNull(),
            'service_payment_fee_id' => $this->integer(11)->notNull(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'description' => $this->string(255),
            'price' => $this->integer(11)->notNull(),
            'fee_of_month' => $this->integer(11)->comment('Thanh toán phí tháng ? '),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex('idx-service_bill_item-service_map_management_id', 'service_bill_item', 'service_map_management_id');
        $this->createIndex('idx-service_bill_item-service_payment_fee_id', 'service_bill_item', 'service_payment_fee_id');
        $this->createIndex('idx-service_bill_item-service_bill_id', 'service_bill_item', 'service_bill_id');
        $this->createIndex('idx-service_bill_item-price', 'service_bill_item', 'price');
        $this->createIndex('idx-service_bill_item-fee_of_month', 'service_bill_item', 'fee_of_month');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_bill}}');
    }
}
