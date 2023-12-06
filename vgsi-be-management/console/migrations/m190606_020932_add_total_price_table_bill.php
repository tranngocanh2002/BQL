<?php

use yii\db\Migration;

/**
 * Class m190606_020932_add_total_price_table_bill
 */
class m190606_020932_add_total_price_table_bill extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_bill', 'total_price', 'int(11) default 0');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190606_020932_add_total_price_table_bill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190606_020932_add_total_price_table_bill cannot be reverted.\n";

        return false;
    }
    */
}
