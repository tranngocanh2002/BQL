<?php

use yii\db\Migration;

/**
 * Class m190912_040151_add_column_type_in_payment_gen_code
 */
class m190912_040151_add_column_type_in_payment_gen_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_gen_code', 'type', $this->integer(11)->defaultValue(0)->comment('0 - chuyển khoản, 1 - thanh toán online'));
        $this->createIndex( 'idx-payment_gen_code-type','payment_gen_code','type' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190912_040151_add_column_type_in_payment_gen_code cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190912_040151_add_column_type_in_payment_gen_code cannot be reverted.\n";

        return false;
    }
    */
}
