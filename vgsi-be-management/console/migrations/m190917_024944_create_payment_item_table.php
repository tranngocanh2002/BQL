<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payment_item}}`.
 */
class m190917_024944_create_payment_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_gen_code','is_auto', $this->integer(11)->defaultValue(0)->comment('0- tạo thủ công, 1 - tạo tự động'));
        $this->addColumn('payment_gen_code','payment_order_id', $this->integer(11));
        $this->addColumn('payment_gen_code','lock_time', $this->integer(11));

        $this->createTable('{{%payment_gen_code_item}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'payment_gen_code_id' => $this->integer(11),
            'service_payment_fee_id' => $this->integer(11),
            'amount' => $this->integer(11),
            'type' => $this->integer(11)->comment('0- offline, 1 - online')
        ]);

        $this->createIndex( 'idx-payment_gen_code_item-building_cluster_id','payment_gen_code_item','building_cluster_id' );
        $this->createIndex( 'idx-payment_gen_code_item-payment_gen_code_id','payment_gen_code_item','payment_gen_code_id' );
        $this->createIndex( 'idx-payment_gen_code_item-service_payment_fee_id','payment_gen_code_item','service_payment_fee_id' );
        $this->createIndex( 'idx-payment_gen_code_item-type','payment_gen_code_item','type' );

        $this->createTable('{{%payment_order_item}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'payment_order_id' => $this->integer(11),
            'service_payment_fee_id' => $this->integer(11),
            'amount' => $this->integer(11),
        ]);

        $this->createIndex( 'idx-payment_order_item-building_cluster_id','payment_order_item','building_cluster_id' );
        $this->createIndex( 'idx-payment_order_item-payment_order_id','payment_order_item','payment_order_id' );
        $this->createIndex( 'idx-payment_order_item-service_payment_fee_id','payment_order_item','service_payment_fee_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payment_gen_code_item}}');
        $this->dropTable('{{%payment_order_item}}');
    }
}
