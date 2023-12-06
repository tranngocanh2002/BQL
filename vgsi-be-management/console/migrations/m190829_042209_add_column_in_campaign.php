<?php

use yii\db\Migration;

/**
 * Class m190829_042209_add_column_in_campaign
 */
class m190829_042209_add_column_in_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'total_apartment_send_success', $this->integer(11)->defaultValue(0));
        $this->addColumn('announcement_campaign', 'total_email_send_success', $this->integer(11)->defaultValue(0));
        $this->addColumn('announcement_campaign', 'total_sms_send_success', $this->integer(11)->defaultValue(0));
        $this->addColumn('announcement_campaign', 'total_app_send', $this->integer(11)->defaultValue(0));
        $this->addColumn('announcement_campaign', 'total_app_open', $this->integer(11)->defaultValue(0));
        $this->addColumn('announcement_campaign', 'total_app_success', $this->integer(11)->defaultValue(0));
        $this->createIndex( 'idx-announcement_campaign-total_apartment_send_success','announcement_campaign','total_apartment_send_success' );
        $this->createIndex( 'idx-announcement_campaign-total_email_send_success','announcement_campaign','total_email_send_success' );
        $this->createIndex( 'idx-announcement_campaign-total_sms_send_success','announcement_campaign','total_sms_send_success' );
        $this->createIndex( 'idx-announcement_campaign-total_app_send','announcement_campaign','total_app_send' );
        $this->createIndex( 'idx-announcement_campaign-total_app_open','announcement_campaign','total_app_open' );
        $this->createIndex( 'idx-announcement_campaign-total_app_success','announcement_campaign','total_app_success' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190829_042209_add_column_in_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190829_042209_add_column_in_campaign cannot be reverted.\n";

        return false;
    }
    */
}
