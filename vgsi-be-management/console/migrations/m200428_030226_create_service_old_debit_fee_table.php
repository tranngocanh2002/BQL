<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_old_debit_fee}}`.
 */
class m200428_030226_create_service_old_debit_fee_table extends Migration
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

        $this->createTable('{{%service_old_debit_fee}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11),
            'service_map_management_id' => $this->integer(11),
            'total_money' => $this->integer(11)->defaultValue(0)->comment('tổng tiền nợ còn lại > 0 là phải trả, < 0 được hoàn'),
            'description' => $this->text(),
            'json_desc' => $this->text(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa duyệt, 1 - đã duyệt'),
            'is_created_fee' => $this->integer(1)->defaultValue(0)->comment('0 - chưa tạo phí thanh toán, 1 - đã tạo phí thanh toán => không được sửa'),
            'fee_of_month' => $this->integer(11),
            'service_payment_fee_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex('idx-service_old_debit_fee-service_map_management_id', 'service_old_debit_fee', 'service_map_management_id');
        $this->createIndex('idx-service_old_debit_fee-apartment_id', 'service_old_debit_fee', 'apartment_id');
        $this->createIndex('idx-service_old_debit_fee-service_payment_fee_id', 'service_old_debit_fee', 'service_payment_fee_id');
        $this->createIndex('idx-service_old_debit_fee-status', 'service_old_debit_fee', 'status');
        $this->createIndex('idx-service_old_debit_fee-building_cluster_id', 'service_old_debit_fee', 'building_cluster_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_old_debit_fee}}');
    }
}
