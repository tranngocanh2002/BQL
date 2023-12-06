<?php

use yii\db\Migration;

/**
 * Class m201005_044418_add_link_dksd_in_building
 */
class m201005_044418_add_link_dksd_in_building extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $this->addColumn('building_cluster', 'link_dksd', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201005_044418_add_link_dksd_in_building cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201005_044418_add_link_dksd_in_building cannot be reverted.\n";

        return false;
    }
    */
}
