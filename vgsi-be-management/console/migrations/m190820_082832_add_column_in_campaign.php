<?php

use yii\db\Migration;

/**
 * Class m190820_082832_add_column_in_campaign
 */
class m190820_082832_add_column_in_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'type', $this->integer(11)->defaultValue(0)->comment('Loại thông báo: 0 thông báo thường, 1 thông náo nhắc nợ lần 1, thông báo nhắc nợ lần 2 , 3 thông báo nhắc nợ lần 3'));
        $this->addColumn('announcement_campaign', 'content_sms', $this->text()->comment('Nội dung thông báo gửi qua tin nhắn'));
        $this->addColumn('announcement_campaign', 'total_email_send', $this->integer(11)->defaultValue(0)->comment('Tổng email gửi'));
        $this->addColumn('announcement_campaign', 'total_email_open', $this->integer(11)->defaultValue(0)->comment('Tổng email đã đọc'));
        $this->addColumn('announcement_campaign', 'total_sms_send', $this->integer(11)->defaultValue(0)->comment('Tổng sms gửi'));
        $this->createIndex( 'idx-announcement_campaign-type','announcement_campaign','type' );
        $this->createIndex( 'idx-announcement_campaign-total_email_send','announcement_campaign','total_email_send' );
        $this->createIndex( 'idx-announcement_campaign-total_email_open','announcement_campaign','total_email_open' );
        $this->createIndex( 'idx-announcement_campaign-total_sms_send','announcement_campaign','total_sms_send' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190820_082832_add_column_in_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190820_082832_add_column_in_campaign cannot be reverted.\n";

        return false;
    }
    */
}
