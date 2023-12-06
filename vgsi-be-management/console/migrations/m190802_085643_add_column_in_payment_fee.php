<?php

use yii\db\Migration;

/**
 * Class m190802_085643_add_column_in_payment_fee
 */
class m190802_085643_add_column_in_payment_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_payment_fee', 'money_collected', $this->integer(11)->defaultValue(0)->comment('số tiền đã thu'));
        $this->addColumn('service_payment_fee', 'more_money_collecte', $this->integer(11)->defaultValue(0)->comment('số tiền cần thu thêm'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190802_085643_add_column_in_payment_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190802_085643_add_column_in_payment_fee cannot be reverted.\n";

        return false;
    }
    */
}
