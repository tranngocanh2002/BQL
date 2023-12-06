<?php

use yii\db\Migration;

/**
 * Class m230407_014352_rename_rateting_table
 */
class m230407_014352_rename_rateting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('service_utility_rateting', 'service_utility_ratting');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230407_014352_rename_rateting_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230407_014352_rename_rateting_table cannot be reverted.\n";

        return false;
    }
    */
}
