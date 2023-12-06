<?php

use yii\db\Migration;

/**
 * Class m190807_023202_add_column_end_index_in_water_info
 */
class m190807_023202_add_column_end_index_in_water_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_water_info', 'end_index', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190807_023202_add_column_end_index_in_water_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190807_023202_add_column_end_index_in_water_info cannot be reverted.\n";

        return false;
    }
    */
}
