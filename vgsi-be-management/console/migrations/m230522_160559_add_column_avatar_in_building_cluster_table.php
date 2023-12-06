<?php

use yii\db\Migration;

/**
 * Class m230522_160559_add_column_avatar_in_building_cluster_table
 */
class m230522_160559_add_column_avatar_in_building_cluster_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'avatar', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230522_160559_add_column_avatar_in_building_cluster_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230522_160559_add_column_avatar_in_building_cluster_table cannot be reverted.\n";

        return false;
    }
    */
}
