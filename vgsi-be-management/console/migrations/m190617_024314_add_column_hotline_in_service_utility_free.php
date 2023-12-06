<?php

use yii\db\Migration;

/**
 * Class m190617_024314_add_column_hotline_in_service_utility_free
 */
class m190617_024314_add_column_hotline_in_service_utility_free extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_free', 'hotline', $this->string(1000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190617_024314_add_column_hotline_in_service_utility_free cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190617_024314_add_column_hotline_in_service_utility_free cannot be reverted.\n";

        return false;
    }
    */
}
