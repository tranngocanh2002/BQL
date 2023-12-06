<?php

use yii\db\Migration;

/**
 * Class m190612_095227_add_column_number_in_bill
 */
class m190612_095227_add_column_number_in_bill extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_bill', 'number', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190612_095227_add_column_number_in_bill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190612_095227_add_column_number_in_bill cannot be reverted.\n";

        return false;
    }
    */
}
