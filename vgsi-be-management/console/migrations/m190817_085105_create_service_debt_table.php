<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_debt}}`.
 */
class m190817_085105_create_service_debt_table extends Migration
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
        
        $this->createTable('{{%service_debt}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11)->notNull(),
            'early_debt' => $this->bigInteger(20)->defaultValue(0)->comment('Nợ đầu kỳ'),
            'end_debt' => $this->bigInteger(20)->defaultValue(0)->comment('Nợ cuối kỳ'),
            'receivables' => $this->bigInteger(20)->defaultValue(0)->comment('Phát sinh phải thu'),
            'collected' => $this->bigInteger(20)->defaultValue(0)->comment('Phát sinh đã thu'),
            'month' => $this->integer(11)->comment('công nợ của tháng'),
            'status' => $this->integer(1)->defaultValue(0)->comment('0 - còn nợ, 1 - không nợ'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-service_debt-building_cluster_id','service_debt','building_cluster_id' );
        $this->createIndex( 'idx-service_debt-building_area_id','service_debt','building_area_id' );
        $this->createIndex( 'idx-service_debt-apartment_id','service_debt','apartment_id' );
        $this->createIndex( 'idx-service_debt-early_debt','service_debt','early_debt' );
        $this->createIndex( 'idx-service_debt-end_debt','service_debt','end_debt' );
        $this->createIndex( 'idx-service_debt-receivables','service_debt','receivables' );
        $this->createIndex( 'idx-service_debt-collected','service_debt','collected' );
        $this->createIndex( 'idx-service_debt-status','service_debt','status' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_debt}}');
    }
}
