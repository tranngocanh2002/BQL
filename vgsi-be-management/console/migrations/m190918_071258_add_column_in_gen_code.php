<?php

use yii\db\Migration;

/**
 * Class m190918_071258_add_column_in_gen_code
 */
class m190918_071258_add_column_in_gen_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_gen_code_item', 'status', $this->integer(11)->defaultValue(0)->comment('0 - chưa thanh toán, 1 đã thanh toán'));
        $this->createIndex( 'idx-payment_gen_code_item-status','payment_gen_code_item','status' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190918_071258_add_column_in_gen_code cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190918_071258_add_column_in_gen_code cannot be reverted.\n";

        return false;
    }
    */
}
