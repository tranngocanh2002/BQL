<?php

use yii\db\Migration;

/**
 * Class m190803_070627_add_column_approved_by_id_in_payment_fee
 */
class m190803_070627_add_column_approved_by_id_in_payment_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_payment_fee', 'approved_by_id', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190803_070627_add_column_approved_by_id_in_payment_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190803_070627_add_column_approved_by_id_in_payment_fee cannot be reverted.\n";

        return false;
    }
    */
}
