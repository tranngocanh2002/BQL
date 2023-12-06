<?php

use yii\db\Migration;

/**
 * Class m190517_064242_add_colum_parent_path_in_building_area
 */
class m190517_064242_add_colum_parent_path_in_building_area extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_area', 'parent_path', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190517_064242_add_colum_parent_path_in_building_area cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190517_064242_add_colum_parent_path_in_building_area cannot be reverted.\n";

        return false;
    }
    */
}
