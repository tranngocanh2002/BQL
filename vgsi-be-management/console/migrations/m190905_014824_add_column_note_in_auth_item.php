<?php

use yii\db\Migration;

/**
 * Class m190905_014824_add_column_note_in_auth_item
 */
class m190905_014824_add_column_note_in_auth_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('auth_item', 'note', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190905_014824_add_column_note_in_auth_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190905_014824_add_column_note_in_auth_item cannot be reverted.\n";

        return false;
    }
    */
}
