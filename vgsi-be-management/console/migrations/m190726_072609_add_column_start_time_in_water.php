<?php

use yii\db\Migration;

/**
 * Class m190726_072609_add_column_start_time_in_water
 */
class m190726_072609_add_column_start_time_in_water extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_water_fee', 'start_time', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190726_072609_add_column_start_time_in_water cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190726_072609_add_column_start_time_in_water cannot be reverted.\n";

        return false;
    }
    */
}
