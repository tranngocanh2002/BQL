<?php

use yii\db\Migration;

/**
 * Class m190724_102158_add_column_is_hidden_in_campaign
 */
class m190724_102158_add_column_is_hidden_in_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_item', 'is_hidden', $this->integer(1)->defaultValue(0));
        $this->createIndex( 'idx-announcement_item-is_hidden','announcement_item','is_hidden' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190724_102158_add_column_is_hidden_in_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190724_102158_add_column_is_hidden_in_campaign cannot be reverted.\n";

        return false;
    }
    */
}
