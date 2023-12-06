<?php

use yii\db\Migration;

/**
 * Class m230602_030357_add_column_type_close_in_request
 */
class m230602_030357_add_column_type_close_in_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request', 'type_close', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230602_030357_add_column_type_close_in_request cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230602_030357_add_column_type_close_in_request cannot be reverted.\n";

        return false;
    }
    */
}
