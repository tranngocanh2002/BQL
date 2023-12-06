<?php

use yii\db\Migration;

/**
 * Class m190823_025435_add_column_item_send_id_in_item
 */
class m190823_025435_add_column_item_send_id_in_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_item', 'announcement_item_send_id', $this->integer(11));
        $this->createIndex( 'idx-announcement_item-announcement_item_send_id','announcement_item','announcement_item_send_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190823_025435_add_column_item_send_id_in_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190823_025435_add_column_item_send_id_in_item cannot be reverted.\n";

        return false;
    }
    */
}
