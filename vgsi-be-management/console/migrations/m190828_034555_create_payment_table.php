<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payment}}`.
 */
class m190828_034555_create_payment_table extends Migration
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

        $this->createTable('{{%payment_config}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'gate' => $this->integer(11)->defaultValue(0)->comment('Cổng thanh toán : 0 - ngân lượng, 1 - vnpay , ...')->notNull(),
            'receiver_account' => $this->string(255)->comment('Tài khoản nhận'),
            'merchant_id' => $this->string(255),
            'merchant_pass' => $this->string(255),
            'checkout_url' => $this->string(255)->comment('link check out'),
            'return_url' => $this->string(255),
            'cancel_url' => $this->string(255),
            'notify_url' => $this->string(255),
            'note' => $this->string(255),
            'status' => $this->integer(11)->defaultValue(1)->comment('0 - chưa kích hoạt, 1 - đã kích hoạt'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-payment_config-building_cluster_id','payment_config','building_cluster_id' );
        $this->createIndex( 'idx-payment_config-gate','payment_config','gate' );
        $this->createIndex( 'idx-payment_config-status','payment_config','status' );

        $this->createTable('{{%payment_order}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'apartment_id' => $this->integer(11),
            'service_bill_id' => $this->integer(11),
            'total_amount' => $this->integer(11)->notNull(),
            'txh_name' => $this->string(255),
            'txt_email' => $this->string(255),
            'txt_phone' => $this->string(255),
            'description' => $this->text(),
            'code' => $this->string(255)->comment('Mã đơn hàng')->notNull(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - khởi tạo, 1 - thành công, 2 - thất bại'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-payment_order-building_cluster_id','payment_order','building_cluster_id' );
        $this->createIndex( 'idx-payment_order-apartment_id','payment_order','apartment_id' );
        $this->createIndex( 'idx-payment_order-service_bill_id','payment_order','service_bill_id' );
        $this->createIndex( 'idx-payment_order-total_amount','payment_order','total_amount' );
        $this->createIndex( 'idx-payment_order-code','payment_order','code' );

        $this->createTable('{{%payment_transaction}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'payment_order_id' => $this->integer(11),
            'total_amount' => $this->integer(11)->notNull(),
            'transaction_info' => $this->text(),
            'payment_id' => $this->integer(11),
            'payment_type' => $this->integer(11),
            'error_text' => $this->string(255),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - khởi tạo, 1 - thành công, 2 - thất bại'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-payment_transaction-building_cluster_id','payment_transaction','building_cluster_id' );
        $this->createIndex( 'idx-payment_transaction-payment_order_id','payment_transaction','payment_order_id' );
        $this->createIndex( 'idx-payment_transaction-total_amount','payment_transaction','total_amount' );
        $this->createIndex( 'idx-payment_transaction-payment_id','payment_transaction','payment_id' );
        $this->createIndex( 'idx-payment_transaction-status','payment_transaction','status' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payment}}');
    }
}
