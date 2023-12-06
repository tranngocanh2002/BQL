<?php

use yii\db\Migration;

/**
 * Class m230709_080441_add_column_reason_in_payment_gen_code
 */
class m230709_080441_add_column_reason_in_payment_gen_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_gen_code', 'reason', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230709_080441_add_column_reason_in_payment_gen_code cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230709_080441_add_column_reason_in_payment_gen_code cannot be reverted.\n";

        return false;
    }
    */
}
