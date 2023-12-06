<?php

use yii\db\Migration;

/**
 * Class m230525_145657_add_column_image_in_announcement_campaign_table
 */
class m230525_145657_add_column_image_in_announcement_campaign_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'image', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230525_145657_add_column_image_in_announcement_campaign_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230525_145657_add_column_image_in_announcement_campaign_table cannot be reverted.\n";

        return false;
    }
    */
}
