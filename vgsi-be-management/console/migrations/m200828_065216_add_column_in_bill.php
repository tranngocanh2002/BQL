<?php

use yii\db\Migration;

/**
 * Class m200828_065216_add_column_in_bill
 */
class m200828_065216_add_column_in_bill extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_bill', 'payment_gen_code_id', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200828_065216_add_column_in_bill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200828_065216_add_column_in_bill cannot be reverted.\n";

        return false;
    }
    */
}
