<?php

use yii\db\Migration;

/**
 * Class m190812_024543_create_service_bill_number
 */
class m190812_024543_create_service_bill_number extends Migration
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

        $this->createTable('{{%service_bill_number}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'year' => $this->integer(11),
            'index_number' => $this->integer(11)->comment('số thứ tự phiếu thu'),
            'service_bill_id' => $this->integer(11),
            'service_bill_number' => $this->string(255),
            'service_bill_type_payment' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_bill_number-building_cluster_id','service_bill_number','building_cluster_id' );
        $this->createIndex( 'idx-service_bill_number-index_number','service_bill_number','index_number' );
        $this->createIndex( 'idx-service_bill_number-service_bill_type_payment','service_bill_number','service_bill_type_payment' );
        $this->createIndex( 'idx-service_bill_number-year','service_bill_number','year' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190812_024543_create_service_bill_number cannot be reverted.\n";
        $this->dropTable('{{%service_bill_number}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190812_024543_create_service_bill_number cannot be reverted.\n";

        return false;
    }
    */
}
