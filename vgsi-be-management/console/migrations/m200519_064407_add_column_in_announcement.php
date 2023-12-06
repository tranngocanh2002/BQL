<?php

use yii\db\Migration;

/**
 * Class m200519_064407_add_column_in_announcement
 */
class m200519_064407_add_column_in_announcement extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'apartment_not_send_ids', $this->text());
        $this->addColumn('announcement_campaign', 'add_phone_send', $this->text());
        $this->addColumn('announcement_campaign', 'add_email_send', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200519_064407_add_column_in_announcement cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200519_064407_add_column_in_announcement cannot be reverted.\n";

        return false;
    }
    */
}
