<?php

use yii\db\Migration;

/**
 * Class m190521_090514_add_colum_total_apartment_send_total_apartment_open_in_campaign_table
 */
class m190521_090514_add_colum_total_apartment_send_total_apartment_open_in_campaign_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'total_apartment_send', $this->integer(11)->defaultValue(0)->comment('Số căn hộ đã gửi'));
        $this->addColumn('announcement_campaign', 'total_apartment_open', $this->integer(11)->defaultValue(0)->comment('Số căn hộ đã mở'));
        $this->createIndex( 'idx-announcement_campaign-total_apartment_send','announcement_campaign','total_apartment_send' );
        $this->createIndex( 'idx-announcement_campaign-total_apartment_open','announcement_campaign','total_apartment_open' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190521_090514_add_colum_total_apartment_send_total_apartment_open_in_campaign_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190521_090514_add_colum_total_apartment_send_total_apartment_open_in_campaign_table cannot be reverted.\n";

        return false;
    }
    */
}
