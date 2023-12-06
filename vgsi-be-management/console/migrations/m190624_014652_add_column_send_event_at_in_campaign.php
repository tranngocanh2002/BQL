<?php

use yii\db\Migration;

/**
 * Class m190624_014652_add_column_send_event_at_in_campaign
 */
class m190624_014652_add_column_send_event_at_in_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'send_event_at', $this->integer(11)->comment('thời gian gửi event'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190624_014652_add_column_send_event_at_in_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190624_014652_add_column_send_event_at_in_campaign cannot be reverted.\n";

        return false;
    }
    */
}
