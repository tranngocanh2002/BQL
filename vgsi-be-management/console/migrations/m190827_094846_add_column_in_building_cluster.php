<?php

use yii\db\Migration;

/**
 * Class m190827_094846_add_column_in_building_cluster
 */
class m190827_094846_add_column_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'bank_name', $this->string(255));
        $this->addColumn('building_cluster', 'bank_holders', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190827_094846_add_column_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190827_094846_add_column_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
