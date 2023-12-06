<?php

use yii\db\Migration;

/**
 * Class m190520_014717_add_colum_totall_answer_in_request_table
 */
class m190520_014717_add_colum_totall_answer_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request', 'total_answer', $this->integer(11)->defaultValue(0)->comment('Tổng số câu trả lời'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190520_014717_add_colum_totall_answer_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190520_014717_add_colum_totall_answer_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
