<?php

use yii\db\Migration;

/**
 * Class m190910_033748_del_payment_transaction_and_add_column_in_payment_order
 */
class m190910_033748_del_payment_transaction_and_add_column_in_payment_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%payment_transaction}}');
        $this->addColumn('payment_order', 'transaction_status', $this->string(255));
        $this->addColumn('payment_order', 'error_code', $this->string(255));
        $this->addColumn('payment_order', 'error_text', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190910_033748_del_payment_transaction_and_add_column_in_payment_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190910_033748_del_payment_transaction_and_add_column_in_payment_order cannot be reverted.\n";

        return false;
    }
    */
}
