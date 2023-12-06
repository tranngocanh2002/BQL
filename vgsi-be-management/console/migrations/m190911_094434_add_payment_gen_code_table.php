<?php

use yii\db\Migration;

/**
 * Class m190911_094434_add_payment_gen_code_table
 */
class m190911_094434_add_payment_gen_code_table extends Migration
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

        $this->createTable('{{%payment_gen_code}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'apartment_id' => $this->integer(11)->notNull(),
            'service_payment_fee_ids' => $this->string(255),
            'status' => $this->integer(11)->defaultValue(0),
            'code' => $this->string(255)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-payment_gen_code-building_cluster_id','payment_gen_code','building_cluster_id' );
        $this->createIndex( 'idx-payment_gen_code-apartment_id','payment_gen_code','apartment_id' );
        $this->createIndex( 'idx-payment_gen_code-status','payment_gen_code','status' );
        $this->createIndex( 'idx-payment_gen_code-code','payment_gen_code','code' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190911_094434_add_payment_gen_code_table cannot be reverted.\n";
        $this->dropTable('{{%payment_gen_code}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190911_094434_add_payment_gen_code_table cannot be reverted.\n";

        return false;
    }
    */
}
