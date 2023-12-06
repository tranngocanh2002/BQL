<?php

use yii\db\Migration;

/**
 * Class m230602_022643_add_column_alias_in_building_cluster
 */
class m230602_022643_add_column_alias_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'alias', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230602_022643_add_column_alias_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down(),
    {
        echo "m230602_022643_add_column_alias_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
