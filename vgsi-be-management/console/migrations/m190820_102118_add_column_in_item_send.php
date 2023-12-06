<?php

use yii\db\Migration;

/**
 * Class m190820_102118_add_column_in_item_send
 */
class m190820_102118_add_column_in_item_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('announcement_item_send', 'type', $this->integer(11));
        $this->addColumn('announcement_item_send', 'end_debt', $this->integer(11));
        $this->createIndex( 'idx-announcement_item_send-type','announcement_item_send','type' );
        $this->createIndex( 'idx-announcement_item_send-end_debt','announcement_item_send','end_debt' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190820_102118_add_column_in_item_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190820_102118_add_column_in_item_send cannot be reverted.\n";

        return false;
    }
    */
}
