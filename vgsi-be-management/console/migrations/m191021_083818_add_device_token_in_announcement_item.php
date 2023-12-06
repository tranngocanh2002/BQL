<?php

use yii\db\Migration;

/**
 * Class m191021_083818_add_device_token_in_announcement_item
 */
class m191021_083818_add_device_token_in_announcement_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_item', 'device_token', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191021_083818_add_device_token_in_announcement_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191021_083818_add_device_token_in_announcement_item cannot be reverted.\n";

        return false;
    }
    */
}
