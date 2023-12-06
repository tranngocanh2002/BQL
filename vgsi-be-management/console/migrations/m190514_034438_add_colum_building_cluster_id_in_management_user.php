<?php

use yii\db\Migration;

/**
 * Class m190514_034438_add_colum_building_cluster_id_in_management_user
 */
class m190514_034438_add_colum_building_cluster_id_in_management_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('management_user', 'building_cluster_id', $this->integer(11)->notNull());
        $this->createIndex( 'idx-management_user-building_cluster_id','management_user','building_cluster_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190514_034438_add_colum_building_cluster_id_in_management_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190514_034438_add_colum_building_cluster_id_in_management_user cannot be reverted.\n";

        return false;
    }
    */
}
