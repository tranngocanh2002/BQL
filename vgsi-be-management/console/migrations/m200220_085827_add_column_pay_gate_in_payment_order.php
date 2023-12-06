<?php

use yii\db\Migration;

/**
 * Class m200220_085827_add_column_pay_gate_in_payment_order
 */
class m200220_085827_add_column_pay_gate_in_payment_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_order', 'pay_gate', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200220_085827_add_column_pay_gate_in_payment_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200220_085827_add_column_pay_gate_in_payment_order cannot be reverted.\n";

        return false;
    }
    */
}
