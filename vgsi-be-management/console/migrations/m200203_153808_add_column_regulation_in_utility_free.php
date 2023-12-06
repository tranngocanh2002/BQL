<?php

use yii\db\Migration;

/**
 * Class m200203_153808_add_column_regulation_in_utility_free
 */
class m200203_153808_add_column_regulation_in_utility_free extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_free', 'regulation', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200203_153808_add_column_regulation_in_utility_free cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200203_153808_add_column_regulation_in_utility_free cannot be reverted.\n";

        return false;
    }
    */
}
