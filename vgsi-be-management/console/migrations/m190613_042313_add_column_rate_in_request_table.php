<?php

use yii\db\Migration;

/**
 * Class m190613_042313_add_column_rate_in_request_table
 */
class m190613_042313_add_column_rate_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request', 'rate', $this->integer(1)->comment('Dánh giá : 1 - 5 sao'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190613_042313_add_column_rate_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190613_042313_add_column_rate_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
