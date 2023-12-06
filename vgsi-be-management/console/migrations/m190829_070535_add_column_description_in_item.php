<?php

use yii\db\Migration;

/**
 * Class m190829_070535_add_column_description_in_item
 */
class m190829_070535_add_column_description_in_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_item', 'description', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190829_070535_add_column_description_in_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190829_070535_add_column_description_in_item cannot be reverted.\n";

        return false;
    }
    */
}
