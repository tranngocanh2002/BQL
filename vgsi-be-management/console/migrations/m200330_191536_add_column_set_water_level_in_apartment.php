<?php

use yii\db\Migration;

/**
 * Class m200330_191536_add_column_set_water_level_in_apartment
 */
class m200330_191536_add_column_set_water_level_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment', 'set_water_level', $this->integer(11)->defaultValue(1)->comment('0- chưa khai báo định mức, 1- đã khai báo định mức'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200330_191536_add_column_set_water_level_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200330_191536_add_column_set_water_level_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
