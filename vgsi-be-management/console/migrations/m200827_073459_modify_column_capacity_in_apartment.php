<?php

use yii\db\Migration;

/**
 * Class m200827_073459_modify_column_capacity_in_apartment
 */
class m200827_073459_modify_column_capacity_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('apartment', 'capacity', $this->float()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200827_073459_modify_column_capacity_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200827_073459_modify_column_capacity_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
