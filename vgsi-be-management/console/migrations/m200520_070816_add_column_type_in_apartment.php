<?php

use yii\db\Migration;

/**
 * Class m200520_070816_add_column_type_in_apartment
 */
class m200520_070816_add_column_type_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment', 'form_type', $this->integer(11)->defaultValue(0)->comment('0 - can ho chung cu'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200520_070816_add_column_type_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_070816_add_column_type_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
