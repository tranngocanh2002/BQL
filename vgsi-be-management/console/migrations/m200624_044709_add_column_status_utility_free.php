<?php

use yii\db\Migration;

/**
 * Class m200624_044709_add_column_status_utility_free
 */
class m200624_044709_add_column_status_utility_free extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_free', 'status', $this->integer(11)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200624_044709_add_column_status_utility_free cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200624_044709_add_column_status_utility_free cannot be reverted.\n";

        return false;
    }
    */
}
