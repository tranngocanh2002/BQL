<?php

use yii\db\Migration;

/**
 * Class m190820_095618_add_column_in_campaign_item
 */
class m190820_095618_add_column_in_campaign_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_item', 'title', $this->string(255));
        $this->addColumn('announcement_item', 'content', $this->text());
        $this->addColumn('announcement_item', 'content_sms', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190820_095618_add_column_in_campaign_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190820_095618_add_column_in_campaign_item cannot be reverted.\n";

        return false;
    }
    */
}
