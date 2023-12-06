<?php

use yii\db\Migration;

/**
 * Class m190612_033640_add_column_is_event_in_campaign
 */
class m190612_033640_add_column_is_event_in_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'is_event', $this->integer(1)->defaultValue(0)->comment('1 - là lịch sự kiện'));
        $this->addColumn('announcement_campaign', 'is_send_event', $this->integer(1)->defaultValue(0)->comment('0 - chưa gửi, 1 - đã gửi'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190612_033640_add_column_is_event_in_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190612_033640_add_column_is_event_in_campaign cannot be reverted.\n";

        return false;
    }
    */
}
